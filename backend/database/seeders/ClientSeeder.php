<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate unique clients
        $this->generateUniqueClients(500);

        // Generate duplicate clients
        $this->generateDuplicateClients(50);
    }

    private function generateUniqueClients($count=100): void
    {
        $faker = Faker::create();
        $uniqueClients = [];

        for ($i = 0; $i < $count; $i++) {
            $uniqueClients[] = [
                'company_name' => $faker->company,
                'email' => $faker->unique()->companyEmail,
                'phone_number' => $faker->phoneNumber,
                'is_duplicate' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Client::insert($uniqueClients);
    }

    private function generateDuplicateClients($count=100): void
    {
        $originalClients = Client::inRandomOrder()->take($count)->get();

        $duplicateClients = [];
        foreach ($originalClients as $originalClient) {
            $groupUuid = $originalClient->duplicate_group_id ?? Str::uuid();

            // Update original client to mark duplicate grouping
            $originalClient->update([
                'duplicate_group_id' => $groupUuid,
            ]);

            // Create 2-3 duplicates clients
            $totalDuplicateCount = rand(2, 3);
            for ($i = 0; $i < $totalDuplicateCount; $i++) {
                $duplicateClients[] = [
                    'company_name' => $originalClient->company_name,
                    'email' => $originalClient->email,
                    'phone_number' => $originalClient->phone_number,
                    'is_duplicate' => true,
                    'duplicate_group_id' => $groupUuid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Client::insert($duplicateClients);
    }
}
