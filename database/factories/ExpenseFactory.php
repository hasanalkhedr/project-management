<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'currency_id' => Currency::inRandomOrder()->first()->id ?? Currency::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'description' => $this->faker->sentence(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'invoice_number' => $this->faker->bothify('INV-####-####'),
            'supplier' => $this->faker->company(),
            'category' => $this->faker->randomElement(['Materials', 'Labor', 'Equipment', 'Subcontractors', 'Other']),
        ];
    }
}
