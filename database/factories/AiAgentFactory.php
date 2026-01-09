<?php

namespace Database\Factories;

use App\Models\AiAgent;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AiAgentFactory extends Factory
{
    protected $model = AiAgent::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'tenant_id' => Tenant::factory(),
            'name' => $this->faker->name . ' Agent',
            'role' => 'Assistant',
            'goal' => 'Help users',
            'system_prompt' => 'You are a helpful assistant.',
        ];
    }
}
