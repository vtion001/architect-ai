<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'tenant_id' => Tenant::factory(), // Creates a tenant automatically if not provided
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password', // Model cast will hash this
            'remember_token' => Str::random(10),
        ];
    }
}
