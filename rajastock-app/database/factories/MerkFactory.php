<?php

namespace Database\Factories;

use App\Models\Merk;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Merk>
 */
class MerkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

protected static $counter = 1;

    public function definition(): array
        {
            $name = $this->faker->company();
            $words = explode(' ', strtoupper($name));

            $baseCode = strtoupper(Str::substr(Str::replace(' ', '', $name), 0, 3));
            $firstWord = $words[0]; 
            $uniquePart = strtoupper(substr(uniqid(), -4));

            $code = $baseCode . '-' . $firstWord . '-' . $uniquePart;

            return [
                'merk_name' => $name, 
                'code'      => $code, 
            ];
        }
}
