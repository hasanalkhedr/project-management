<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥'],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ'],
        ];

        $currency = $this->faker->unique()->randomElement($currencies);

        return [
            'code' => $currency['code'],
            'name' => $currency['name'],
            'symbol' => $currency['symbol'],
            'exchange_rate' => $this->faker->randomFloat(4, 0.5, 5),
            'is_default' => false,
        ];
    }
}
