<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listing all clients with pagination
     */
    public function test_can_list_all_clients()
    {
        Client::factory()->count(3)->create();
        $response = $this->getJson('/api/clients');
        $this->assertApiSuccess($response, 3);
    }

    /**
     * Test filtering duplicate clients
     */
    public function test_can_filter_duplicate_clients()
    {
        Client::factory()->create(['is_duplicate' => 1]);
        Client::factory()->create(['is_duplicate' => 1]);
        Client::factory()->create(['is_duplicate' => 0]);

        $response = $this->getJson('/api/clients?filter=duplicate');
        $this->assertApiSuccess($response, 2);
        $this->assertAllClientsAreDuplicates($response);
    }

    /**
     * Test filtering unique clients
     */
    public function test_can_filter_unique_clients()
    {
        Client::factory()->create(['is_duplicate' => 0]);
        Client::factory()->create(['is_duplicate' => 0]);
        Client::factory()->create(['is_duplicate' => 1]);
        
        $response = $this->getJson('/api/clients?filter=unique');
        $this->assertApiSuccess($response, 2);
        $this->assertAllClientsAreUnique($response);
    }

    /**
     * Test searching clients by company name
     */
    public function test_can_search_by_company_name()
    {
        Client::factory()->create(['company_name' => 'Apple Inc']);
        Client::factory()->create(['company_name' => 'Microsoft Corporation']);
        
        $response = $this->getJson('/api/clients?search=Apple');
        $this->assertApiSuccess($response, 1);
        $this->assertResponseContainsCompany($response, 'Apple Inc');
    }

    /**
     * Test searching clients by email
     */
    public function test_can_search_by_email()
    {
        Client::factory()->create(['email' => 'contact@apple.com']);
        Client::factory()->create(['email' => 'info@microsoft.com']);

        $response = $this->getJson('/api/clients?search=microsoft.com');
        $this->assertApiSuccess($response, 1);
        $this->assertResponseContainsEmail($response, 'info@microsoft.com');
    }

    /**
     * Test pagination functionality
     */
    public function test_pagination_works()
    {
        Client::factory()->count(25)->create();

        $response = $this->getJson('/api/clients?page=2');
        $response->assertStatus(200)->assertJsonPath('content.current_page', 2)->assertJsonPath('content.per_page', 20);
    }

    /**
     * Test empty results for non-matching search
     */
    public function test_returns_empty_for_no_results()
    {
        $response = $this->getJson('/api/clients?search=NonExistentCompany');
        $this->assertApiSuccess($response, 0);
    }

    /**
     * Test combining search and filter
     */
    public function test_combines_search_and_filter()
    {
        Client::factory()->create([
            'company_name' => 'Apple Inc',
            'is_duplicate' => 1
        ]);
        Client::factory()->create([
            'company_name' => 'Microsoft Corp', 
            'is_duplicate' => 1
        ]);
        Client::factory()->create([
            'company_name' => 'Apple Store',
            'is_duplicate' => 0
        ]);
        
        $response = $this->getJson('/api/clients?search=Apple&filter=duplicate');
        $this->assertApiSuccess($response, 1);
        
        $data = $this->getApiData($response);
        $this->assertEquals('Apple Inc', $data[0]['company_name']);
        $this->assertEquals(1, $data[0]['is_duplicate']);
    }

    /**
     * Test default per_page value
     */
    public function test_uses_default_per_page()
    {
        Client::factory()->count(25)->create();

        $response = $this->getJson('/api/clients');
        $response->assertStatus(200)->assertJsonPath('content.per_page', 20)->assertJsonCount(20, 'content.data');
    }

    /**
     * Assert API returns success response with expected data count
     */
    private function assertApiSuccess($response, $expectedDataCount = null)
    {
        $response->assertStatus(200)->assertJsonStructure(['code','status','message',
                'content' => [
                    'current_page',
                    'data',
                    'per_page',
                    'total'
                ]
            ])->assertJsonPath('status', 'success');

        if ($expectedDataCount !== null) {
            $response->assertJsonCount($expectedDataCount, 'content.data');
        }

        return $this;
    }

    /**
     * Get response data array
     */
    private function getApiData($response)
    {
        return json_decode($response->getContent(), true)['content']['data'];
    }

    /**
     * Assert all clients in response are duplicates
     */
    private function assertAllClientsAreDuplicates($response)
    {
        $data = $this->getApiData($response);
        $this->assertTrue(collect($data)->every('is_duplicate', 1));
    }

    /**
     * Assert all clients in response are unique
     */
    private function assertAllClientsAreUnique($response)
    {
        $data = $this->getApiData($response);
        $this->assertTrue(collect($data)->every('is_duplicate', 0));
    }

    /**
     * Assert response contains specific company
     */
    private function assertResponseContainsCompany($response, $companyName)
    {
        $data = $this->getApiData($response);
        $this->assertEquals($companyName, $data[0]['company_name']);
    }

    /**
     * Assert response contains specific email
     */
    private function assertResponseContainsEmail($response, $email)
    {
        $data = $this->getApiData($response);
        $this->assertEquals($email, $data[0]['email']);
    }
}