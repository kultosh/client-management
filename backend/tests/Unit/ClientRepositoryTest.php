<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use App\Models\ImportFailure;
use App\Models\ImportStatus;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class ClientRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ClientRepository();
    }

    public function test_it_can_get_clients_with_pagination(): void
    {
        Client::factory()->count(25)->create();

        $request = new Request();
        $result = $this->repository->getClients($request);

        $this->assertCount(20, $result->items());
        $this->assertEquals(25, $result->total());
    }

    public function test_it_can_filter_clients_by_type(): void
    {
        $duplicateClient = Client::factory()->create(['is_duplicate' => true]);
        $uniqueClient = Client::factory()->create(['is_duplicate' => false]);

        $request = new Request(['filter' => 'duplicate']);
        $result = $this->repository->getClients($request);

        $clientIds = array_map(function($client) {
            return $client->id;
        }, $result->items());
        
        $this->assertContains($duplicateClient->id, $clientIds);
        $this->assertNotContains($uniqueClient->id, $clientIds);
    }

    public function test_it_can_search_clients(): void
    {
        Client::factory()->create(['company_name' => 'Apple Inc']);
        Client::factory()->create(['company_name' => 'Microsoft Corp']);

        $request = new Request(['search' => 'Apple']);
        $result = $this->repository->getClients($request);

        $this->assertCount(1, $result->items());
        $this->assertEquals('Apple Inc', $result->items()[0]->company_name);
    }

    public function test_it_can_update_client_and_detect_duplicates(): void
    {
        $client1 = Client::factory()->create([
            'company_name' => 'Original Company',
            'email' => 'original@test.com',
            'phone_number' => '123456'
        ]);

        $client2 = Client::factory()->create([
            'company_name' => 'Existing Company',
            'email' => 'existing@test.com',
            'phone_number' => '789012'
        ]);

        $updatedData = [
            'company_name' => 'Existing Company',
            'email' => 'existing@test.com',
            'phone_number' => '789012'
        ];

        $result = $this->repository->updateClient($updatedData, $client1->id);

        $this->assertTrue($result->is_duplicate);
        $this->assertNotNull($result->duplicate_group_id);
    }

    public function test_it_can_delete_client(): void
    {
        $client = Client::factory()->create();

        $result = $this->repository->deleteClient($client->id);

        $this->assertEquals(['deleted_id' => $client->id], $result);
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_it_cleans_up_single_remaining_duplicate(): void
    {
        $groupId = \Illuminate\Support\Str::uuid();
        
        $client1 = Client::factory()->create([
            'is_duplicate' => true,
            'duplicate_group_id' => $groupId
        ]);
        
        $client2 = Client::factory()->create([
            'is_duplicate' => true,
            'duplicate_group_id' => $groupId
        ]);

        $this->repository->deleteClient($client1->id);
        
        $remainingClient = Client::find($client2->id);
        
        $this->assertFalse($remainingClient->is_duplicate);
        $this->assertNull($remainingClient->duplicate_group_id);
    }

    public function test_it_keeps_duplicates_when_multiple_remain(): void
    {
        $groupId = \Illuminate\Support\Str::uuid();
        
        $client1 = Client::factory()->create([
            'is_duplicate' => true,
            'duplicate_group_id' => $groupId
        ]);
        
        $client2 = Client::factory()->create([
            'is_duplicate' => true,
            'duplicate_group_id' => $groupId
        ]);

        $client3 = Client::factory()->create([
            'is_duplicate' => true,
            'duplicate_group_id' => $groupId
        ]);

        $this->repository->deleteClient($client1->id);
        
        $remainingClient2 = Client::find($client2->id);
        $remainingClient3 = Client::find($client3->id);
        
        $this->assertTrue($remainingClient2->is_duplicate);
        $this->assertTrue($remainingClient3->is_duplicate);
        $this->assertEquals($groupId, $remainingClient2->duplicate_group_id);
    }

    public function test_it_can_import_clients_and_return_import_data(): void
    {
        Excel::fake();
        
        $file = UploadedFile::fake()->create('clients.csv', 100, 'text/csv');

        $result = $this->repository->importClients($file);

        $this->assertArrayHasKey('import_id', $result);
        $this->assertArrayHasKey('check_failures_url', $result);
        $this->assertEquals('Import queued successfully.', $result['message']);
    }

    public function test_it_can_get_import_status(): void
    {
        $importId = \Illuminate\Support\Str::uuid();
        
        ImportStatus::create([
            'import_id' => $importId,
            'status' => 'processing'
        ]);

        ImportFailure::create([
            'import_id' => $importId,
            'row' => 2,
            'attribute' => 'email',
            'errors' => ['The email must be valid'],
            'values' => ['company_name' => 'Test Co']
        ]);

        $result = $this->repository->importStatus($importId);

        $this->assertEquals('processing', $result['status']);
        $this->assertCount(1, $result['failures']);
        $this->assertEquals(1, $result['summary']['total_failures']);
    }
}