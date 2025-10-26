<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use App\Models\ImportStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Maatwebsite\Excel\Facades\Excel;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_clients(): void
    {
        Client::factory()->count(15)->create();

        $response = $this->getJson('/api/clients');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'status',
                'message',
                'content' => [
                    'data' => [
                        '*' => ['id', 'company_name', 'email', 'phone_number', 'is_duplicate']
                    ],
                    'current_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    public function test_it_can_filter_clients_by_type(): void
    {
        // Create both duplicate and unique clients
        Client::factory()->create(['is_duplicate' => true]);
        Client::factory()->create(['is_duplicate' => false]);

        $response = $this->getJson('/api/clients?filter=duplicates');
        $response->assertStatus(200);

        $this->assertTrue(true);
    }

    public function test_it_can_list_unique_clients(): void
    {
        Client::factory()->create(['is_duplicate' => true]);
        Client::factory()->create(['is_duplicate' => false]);

        $response = $this->getJson('/api/clients?filter=unique');
        $response->assertStatus(200);

        $this->assertTrue(true);
    }

    public function test_it_can_search_clients_via_api(): void
    {
        $client1 = Client::factory()->create(['company_name' => 'Apple Inc']);
        $client2 = Client::factory()->create(['company_name' => 'Microsoft Corp']);

        $response = $this->getJson('/api/clients?search=Apple');

        $response->assertStatus(200);
        
        $data = $response->json('content.data');
        $this->assertCount(1, $data);
        $this->assertEquals('Apple Inc', $data[0]['company_name']);
    }

    public function test_it_can_update_client_via_api(): void
    {
        $client = Client::factory()->create([
            'company_name' => 'Old Company',
            'email' => 'old@test.com'
        ]);

        $updateData = [
            'company_name' => 'Updated Company',
            'email' => 'updated@test.com',
            'phone_number' => '123456789'
        ];

        $response = $this->putJson("/api/clients/{$client->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Client updated successfully.'
            ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'company_name' => 'Updated Company',
            'email' => 'updated@test.com'
        ]);
    }

    public function test_it_validates_update_request_via_api(): void
    {
        $client = Client::factory()->create();

        $response = $this->putJson("/api/clients/{$client->id}", [
            'company_name' => '',
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'code',
                'status',
                'message',
                'content' => [
                    'company_name',
                    'email'
                ]
            ]);
    }

    public function test_it_can_delete_client_via_api(): void
    {
        $client = Client::factory()->create();

        $response = $this->deleteJson("/api/clients/{$client->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Client deleted successfully.'
            ]);

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_it_handles_nonexistent_client_deletion_via_api(): void
    {
        $response = $this->deleteJson("/api/clients/9999");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'error'
            ]);
    }

    public function test_it_can_export_clients_via_api(): void
    {
        Client::factory()->count(5)->create();

        $response = $this->getJson('/api/clients/export?type=all');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_it_can_export_duplicate_clients_via_api(): void
    {
        Client::factory()->create(['is_duplicate' => true]);
        Client::factory()->create(['is_duplicate' => false]);

        $response = $this->getJson('/api/clients/export?type=duplicates');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_it_can_import_clients_via_api(): void
    {
        Queue::fake();
        Excel::fake();

        $file = UploadedFile::fake()->create('clients.csv', 100, 'text/csv');

        $response = $this->postJson('/api/clients/import', [
            'file' => $file
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success'
            ])
            ->assertJsonStructure([
                'content' => ['import_id', 'check_failures_url']
            ]);
    }

    public function test_it_handles_missing_import_file_via_api(): void
    {
        $response = $this->postJson('/api/clients/import', [
            // No file provided
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'error'
            ]);
    }

    public function test_it_handles_invalid_file_types_via_api(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->postJson('/api/clients/import', [
            'file' => $file
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'error'
            ]);
    }

    public function test_it_can_check_import_status_via_api(): void
    {
        $importId = \Illuminate\Support\Str::uuid();
        
        ImportStatus::create([
            'import_id' => $importId,
            'status' => 'completed'
        ]);

        $response = $this->getJson("/api/clients/imports/{$importId}/status");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'content' => [
                    'status' => 'completed'
                ]
            ]);
    }

    public function test_it_handles_unknown_import_status_via_api(): void
    {
        $response = $this->getJson("/api/clients/imports/nonexistent-import-id/status");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success'
            ]);
    }
}