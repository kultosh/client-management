<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'is_duplicate' => false,
            'duplicate_group_id' => null,
        ];
    }

    public function duplicate()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_duplicate' => true,
                'duplicate_group_id' => \Illuminate\Support\Str::uuid(),
            ];
        });
    }
}
