<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Price::create([
            'volume' => 5,
            'price' => 100,
        ], [
            'volume' => 10,
            'price' => 100,
        ], [
            'volume' => 20,
            'price' => 100,
        ], [
            'volume' => 30,
            'price' => 100,
        ]);
    }
}
