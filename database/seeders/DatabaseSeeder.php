<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // العملات الأساسية
        \App\Models\Currency::create([
            'code' => 'SAR',
            'name' => 'الريال السعودي',
            'symbol' => 'ر.س',
            'exchange_rate' => 1,
            'is_default' => true,
        ]);

        \App\Models\Currency::create([
            'code' => 'USD',
            'name' => 'الدولار الأمريكي',
            'symbol' => '$',
            'exchange_rate' => 3.75,
        ]);

        \App\Models\Currency::create([
            'code' => 'EUR',
            'name' => 'اليورو',
            'symbol' => '€',
            'exchange_rate' => 4.20,
        ]);

        // عملاء نموذجيين
        \App\Models\Client::factory(10)->create();

        // مشاريع نموذجية
        \App\Models\Project::factory(5)->create();

        // نفقات ودفعات نموذجية
        \App\Models\Expense::factory(20)->create();
        \App\Models\Payment::factory(15)->create();
    }
}
