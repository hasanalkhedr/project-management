<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'currency_id' => Currency::inRandomOrder()->first()->id ?? Currency::factory(),
            'amount' => $this->faker->randomFloat(2, 1000, 50000),
            'description' => $this->faker->sentence(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'payment_method' => $this->faker->randomElement(['Cash', 'Bank Transfer', 'Check', 'Credit Card']),
            'reference' => $this->faker->bothify('PAY-####-####'),
        ];
    }
}
