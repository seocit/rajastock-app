<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\Supplier::class;

    public function definition()
    {
        return [
            'supplier_code' => $this->faker->unique()->bothify('SUP-####'),
            'supplier_name' => $this->faker->company(),
            'address'        => $this->faker->address(),
            'no_contact'       => $this->faker->phoneNumber(),
            'email'         => $this->faker->unique()->safeEmail(),

        ];
    }
}
