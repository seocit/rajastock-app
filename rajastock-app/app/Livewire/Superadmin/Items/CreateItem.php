<?php

namespace App\Livewire\Superadmin\Items;

use App\Models\Item;
use App\Models\Merk;

use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateItem extends Component
{
    #[Validate('required|string|max:20')]
    public $code;
    #[Validate('required|string|max:100')]
    public $name;
    #[Validate('required|decimal:0,2')]
    public $price;
    #[Validate('required|decimal:0,2')]
    public $sellingPrice;
    #[Validate('required|integer|min:0')]
    public $stock;
    #[Validate('required|integer|min:0')]
    public $minimumStock;
    // #[Validate('required|string|max:500')]
    public $description;
    #[Validate('required|exists:merks,id')]
    public $merk_id = [];

    public $merks;

    public function mount()
    {
        $this->merks = Merk::all(); // ambil semua merk
    }

    public function save()
    {
        try {
            $this->validate();

            Item::create([
                'merk_id'       => $this->merk_id,
                'item_code'      => $this->code,
                'item_name'      => $this->name,
                'price'          => $this->price,
                'selling_price'  => $this->sellingPrice,
                'stock'          => $this->stock,
                'minimum_stock'  => $this->minimumStock,
                'description'    => $this->description,

            ]);

            $this->reset([
                'code',
                'name',
                'price',
                'sellingPrice',
                'stock',
                'minimumStock',
                'description',
                'merk_id',
            ]);

            // success message
            session()->flash('success', 'Item berhasil ditambahkan ✅');
            $this->dispatch('item-updated');
        } catch (\Exception $e) {
            // error message
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function resetForm() {

        $this->reset([
                'code',
                'name',
                'price',
                'sellingPrice',
                'stock',
                'minimumStock',
                'description',
                'merk_id',
            ]);
    }


    public function render()
    {
        return view('livewire.superadmin.items.create-item');
    }
}
