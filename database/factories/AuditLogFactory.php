<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(),
            'subject_id' => $this->faker->numberBetween(1, 100),
            'subject_type' => 'App\\Models\\Control',
            'user_id' => User::factory(),
            'properties' => ['description' => 'test log entry'],
            'host' => $this->faker->ipv4(),
        ];
    }
}
