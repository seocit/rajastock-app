<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Merk;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        // pastikan ada merk
        if (Merk::count() === 0) {
            Merk::factory()->count(8)->create();
        }

        // Katalog realistis: item sudah "nempel" ke merk
        $catalog = [
            // GS Astra
            ['merk' => 'GS Astra', 'name' => 'Aki GS Astra MF NS40',     'buy' => [480000, 560000], 'margin' => [80000, 140000]],
            ['merk' => 'GS Astra', 'name' => 'Aki GS Astra NS60',        'buy' => [650000, 780000], 'margin' => [90000, 160000]],
            ['merk' => 'GS Astra', 'name' => 'Aki GS Astra 55D23L',      'buy' => [780000, 920000], 'margin' => [100000, 180000]],
            ['merk' => 'GS Astra', 'name' => 'Aki GS GTZ6V (Motor)',     'buy' => [220000, 320000], 'margin' => [50000, 90000]],
            ['merk' => 'GS Astra', 'name' => 'Aki GS YTZ7S (Motor)',     'buy' => [250000, 350000], 'margin' => [50000, 90000]],

            // Yuasa
            ['merk' => 'Yuasa', 'name' => 'Aki Yuasa MF 46B24L',         'buy' => [680000, 820000], 'margin' => [90000, 160000]],
            ['merk' => 'Yuasa', 'name' => 'Aki Yuasa MF NS40',           'buy' => [520000, 650000], 'margin' => [80000, 140000]],
            ['merk' => 'Yuasa', 'name' => 'Aki Yuasa YTZ7S (Motor)',     'buy' => [250000, 340000], 'margin' => [50000, 90000]],
            ['merk' => 'Yuasa', 'name' => 'Aki Yuasa YTX5L-BS (Motor)',  'buy' => [180000, 260000], 'margin' => [40000, 80000]],

            // Amaron
            ['merk' => 'Amaron', 'name' => 'Aki Amaron Go NS60',         'buy' => [700000, 850000], 'margin' => [90000, 170000]],
            ['merk' => 'Amaron', 'name' => 'Aki Amaron NS40',            'buy' => [600000, 760000], 'margin' => [90000, 160000]],

            // Delkor
            ['merk' => 'Delkor', 'name' => 'Aki Delkor NS40',            'buy' => [620000, 780000], 'margin' => [90000, 170000]],
            ['merk' => 'Delkor', 'name' => 'Aki Delkor NS60',            'buy' => [720000, 900000], 'margin' => [100000, 180000]],

            // Incoe
            ['merk' => 'Incoe', 'name' => 'Aki Incoe NS40',              'buy' => [420000, 520000], 'margin' => [80000, 140000]],
            ['merk' => 'Incoe', 'name' => 'Aki Incoe NS60',              'buy' => [580000, 720000], 'margin' => [90000, 160000]],

            // Bosch
            ['merk' => 'Bosch', 'name' => 'Aki Bosch NS40',              'buy' => [750000, 900000], 'margin' => [100000, 180000]],
            ['merk' => 'Bosch', 'name' => 'Aki Bosch NS60',              'buy' => [820000, 980000], 'margin' => [110000, 190000]],
        ];

        // (opsional) cegah item_name yang persis sama kebanyakan
        static $usedNames = [];
        $pick = null;

        for ($i = 0; $i < 20; $i++) {
            $candidate = fake()->randomElement($catalog);
            if (!in_array($candidate['name'], $usedNames, true)) {
                $pick = $candidate;
                $usedNames[] = $candidate['name'];
                break;
            }
        }

        // fallback kalau sudah kepakai semua
        $pick ??= fake()->randomElement($catalog);

        // cari merk_id sesuai merk_name di katalog
        $merkId = Merk::where('merk_name', $pick['merk'])->value('id');

        // kalau merk belum ada (misal db lama), bikin 1
        if (!$merkId) {
            $merkId = Merk::create([
                'merk_name' => $pick['merk'],
                'code' => strtoupper(substr(str_replace(' ', '', $pick['merk']), 0, 3)) . '-' . strtoupper(\Illuminate\Support\Str::random(4)),
            ])->id;
        }

        $buy = $this->faker->numberBetween($pick['buy'][0], $pick['buy'][1]);
        $margin = $this->faker->numberBetween($pick['margin'][0], $pick['margin'][1]);

        return [
            'merk_id'       => $merkId,
            'item_code'     => strtoupper($this->faker->unique()->bothify('ITM-####')),
            'item_name'     => $pick['name'],
            'price'         => $buy,
            'selling_price' => $buy + $margin,
            'stock'         => $this->faker->numberBetween(5, 60),
            'minimum_stock' => $this->faker->numberBetween(3, 10),
            'description'   => 'Produk aki bergaransi, kualitas terjamin.',
        ];
    }
}
