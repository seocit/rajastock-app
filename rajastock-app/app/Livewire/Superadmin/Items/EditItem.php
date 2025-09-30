<?php

namespace App\Livewire\Superadmin\Items;

use App\Models\Item;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditItem extends Component
{

    public $itemId;
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
    #[Validate('required|string|max:500')]
    public $description;

   


    #[On('edit-item')]
    public function edit($id)
    {

        $item = Item::findorfail($id);
        $this->itemId = $item->id;
        $this->code = $item->item_code;
        $this->name = $item->item_name;
        $this->price = $item->price;
        $this->sellingPrice = $item->selling_price;
        $this->stock = $item->stock;
        $this->minimumStock = $item->minimum_stock;
        $this->description = $item->description;
    }

    
    public function update()
    {
        try {
            $this->validate();

            $item = Item::find($this->itemId);           
            $item->item_code = $this->code;
            $item->item_name = $this->name;
            $item->price = $this->price;
            $item->selling_price = $this->sellingPrice;
            $item->stock  = $this->stock;
            $item->minimum_stock = $this->minimumStock;
            $item->description = $this->description;
            $item->save();




            // success message
            session()->flash('success', 'Item berhasil diupdate ✅');
            Flux::modal('edit-item')->close();
            $this->dispatch('item-updated');
            $this->resetValidation();

        } catch (\Exception $e) {
            // error message
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        
    }

    public function render()
    {
        return view('livewire.superadmin.items.edit-item');
    }
}
