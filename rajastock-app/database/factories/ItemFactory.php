<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Merk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Item::class;

    public function definition(): array
    {

        static $merkIds = null;

        $merkIds = \App\Models\Merk::pluck('id')->toArray();

        return [
            'merk_id' => function () use ($merkIds) {
                return fake()->randomElement($merkIds);
            },
            'item_code'     => strtoupper($this->faker->bothify('ITM-####')),
            'item_name'     => $this->faker->word(),
            'price'         => $this->faker->numberBetween(10000, 100000),
            'selling_price' => $this->faker->numberBetween(15000, 150000),
            'stock'         => $this->faker->numberBetween(0, 100),
            'minimum_stock' => $this->faker->numberBetween(1, 10),
            'description'   => $this->faker->sentence(),
        ];
    }
}
