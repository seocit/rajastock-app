<?php

namespace Database\Factories;

use App\Models\Merk;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MerkFactory extends Factory
{
    protected $model = Merk::class;

    public function definition(): array
    {
        $merks = [
            'GS Astra',
            'Yuasa',
            'Amaron',
            'Delkor',
            'Incoe',
            'Bosch',
            'Motobatt',
            'Deltec',
        ];

        $name = $this->faker->unique()->randomElement($merks);
        $prefix = strtoupper(Str::of($name)->replace(' ', '')->substr(0, 3));
        $code = $prefix . '-' . strtoupper(Str::random(4));

        return [
            'merk_name' => $name,
            'code' => $code,
        ];
    }
}
