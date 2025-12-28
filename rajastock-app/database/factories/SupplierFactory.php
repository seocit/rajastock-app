<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = \App\Models\Supplier::class;

    public function definition(): array
    {
        $suppliers = [
            ['name' => 'PT Sumber Aki Nasional',        'address' => 'Jl. Industri No. 12, Jakarta',   'phone' => '021-555-0101'],
            ['name' => 'PT Astra Otoparts Distribution','address' => 'Jl. Raya Cakung No. 88, Jakarta','phone' => '021-555-0202'],
            ['name' => 'CV Prima Battery Indonesia',   'address' => 'Jl. Pabrik No. 5, Bekasi',      'phone' => '021-555-0303'],
            ['name' => 'PT Indo Battery Sejahtera',    'address' => 'Jl. Pergudangan No. 21, Tangerang','phone' => '021-555-0404'],
            ['name' => 'CV Jaya Power Accu',           'address' => 'Jl. Gudang No. 9, Depok',       'phone' => '021-555-0505'],
        ];

        $pick = $this->faker->randomElement($suppliers);

        return [
            'supplier_code' => $this->faker->unique()->bothify('SUP-####'),
            'supplier_name' => $pick['name'],
            'address'       => $pick['address'],
            'no_contact'    => $pick['phone'],
            'email'         => $this->faker->unique()->safeEmail(),
        ];
    }
}
