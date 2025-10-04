<?php

namespace App\Livewire\Superadmin\Supplier;

use App\Models\Supplier;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditSupplier extends Component
{
    #[Validate('nullable|string|max:20')]
    public $supplier_code;
    #[Validate('nullable|string|max:100')]
    public $supplier_name;
    #[Validate('nullable|string|max:255')]
    public $address;
    #[Validate('nullable|string|max:15')]
    public $no_contact;
    #[Validate('nullable|string|max:100')]
    public $email;

    public $supplierId;

    #[On('edit-customer')]
    public function edit($id)
    {
        $supplier = Supplier::findorfail($id);
        $this->supplierId = $supplier->id;
        $this->supplier_code = $supplier->supplier_code;
        $this->supplier_name = $supplier->supplier_name;
        $this->address = $supplier->address;
        $this->no_contact = $supplier->no_contact;
        $this->email = $supplier->email;
    }

    public function update()
    {
        try {
            $this->validate();
            $item = Supplier::find($this->supplierId);
            $item->supplier_code = $this->supplier_code;
            $item->supplier_name = $this->supplier_name;
            $item->address = $this->address;
            $item->no_contact = $this->no_contact;
            $item->email = $this->email;
            $item->save();

            session()->flash('success', 'Item berhasil diupdate ✅');
            Flux::modal('edit-supplier')->close();
            $this->dispatch('supplier-updated');
            $this->resetValidation();
        } catch (\Exception $e) {
            // error message
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.superadmin.supplier.edit-supplier');
    }
}
