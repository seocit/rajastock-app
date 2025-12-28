<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merk;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | USERS (Admin Demo)
        |--------------------------------------------------------------------------
        */
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@demo.id',
            'password' => Hash::make('useruser123'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | MASTER DATA
        |--------------------------------------------------------------------------
        */

        // MERK (harus duluan)
        $this->command->info('Seeding merk...');
        Merk::factory()->count(8)->create();

        // ITEM / PRODUK
        $this->command->info('Seeding items...');
        Item::factory()->count(50)->create();

        // SUPPLIER
        $this->command->info('Seeding suppliers...');
        Supplier::factory()->count(10)->create();

        // CUSTOMER
        $this->command->info('Seeding customers...');
        Customer::factory()->count(15)->create();

        $this->command->info('✅ Database seeding completed successfully.');
    }
}
