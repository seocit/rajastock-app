<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Costumer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\Customer::class;
    public function definition(): array
    {
        return [
            'customer_code' => $this->faker->unique()->bothify('C-####'),
            'customer_name' => $this->faker->name(),
            'address'        => $this->faker->address(),
            'no_contact'       => $this->faker->phoneNumber(),
            'email'         => $this->faker->unique()->safeEmail(),
        ];
    }
}
