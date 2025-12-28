<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = \App\Models\Customer::class;

    public function definition(): array
    {
        $customers = [
            // personal
            ['name' => 'Andi Pratama',  'address' => 'Jl. Ahmad Yani No. 12, Bekasi',     'phone' => '0812-3456-7890', 'email' => 'andi.pratama@mail.com'],
            ['name' => 'Budi Santoso',  'address' => 'Jl. Sudirman No. 45, Jakarta',      'phone' => '0813-2222-3333', 'email' => 'budi.santoso@mail.com'],
            ['name' => 'Ahmad Fauzi',   'address' => 'Jl. Gatot Subroto No. 8, Bandung',  'phone' => '0821-1111-2222', 'email' => 'ahmad.fauzi@mail.com'],
            ['name' => 'Rudi Hartono',  'address' => 'Jl. Raya Bogor Km 21, Depok',       'phone' => '0852-8888-9999', 'email' => 'rudi.hartono@mail.com'],
            ['name' => 'Deni Saputra',  'address' => 'Jl. Veteran No. 30, Tangerang',     'phone' => '0812-9090-8080', 'email' => 'deni.saputra@mail.com'],

            // toko/bengkel
            ['name' => 'Bengkel Jaya Motor',        'address' => 'Jl. Raya Bekasi No. 55, Bekasi', 'phone' => '021-8899-1122', 'email' => 'jayamotor@demo.id'],
            ['name' => 'Toko Sumber Aki',           'address' => 'Jl. Kapten Tendean No. 9, Jakarta', 'phone' => '021-7788-3344', 'email' => 'sumberaki@demo.id'],
            ['name' => 'Bengkel Makmur Sejahtera',  'address' => 'Jl. Pajajaran No. 10, Bogor', 'phone' => '0251-123-456', 'email' => 'makmursejahtera@demo.id'],
            ['name' => 'CV Maju Lancar Motor',      'address' => 'Jl. Diponegoro No. 77, Bandung', 'phone' => '022-765-4321', 'email' => 'majulancar@demo.id'],
            ['name' => 'UD Sentosa Motor',          'address' => 'Jl. Basuki Rahmat No. 15, Surabaya', 'phone' => '031-4455-6677', 'email' => 'sentosamotor@demo.id'],
        ];

        $pick = $this->faker->randomElement($customers);

        return [
            'customer_code' => $this->faker->unique()->bothify('C-####'),
            'customer_name' => $pick['name'],
            'address'       => $pick['address'],
            'no_contact'    => $pick['phone'],
            'email'         => $this->faker->unique()->safeEmail(), // kalau mau fixed: $pick['email']
        ];
    }
}
