<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Merk;
use App\Models\Supplier;
use App\Models\User;
// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory()->create();
        // Merk::factory()->count(5)->create();
        // Item::factory()->count(50)->create();
        // Supplier::factory()->count(10)->create();
        Customer::factory()->count(10)->create();
    
    }
}
