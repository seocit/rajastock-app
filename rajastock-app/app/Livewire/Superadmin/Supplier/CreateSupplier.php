<?php

namespace App\Livewire\Superadmin\Supplier;

use App\Models\Supplier;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateSupplier extends Component
{
    #[Validate('required|string|max:20|unique:suppliers,supplier_code')]
    public $supplier_code;
    #[Validate('required|string|max:100')]
    public $supplier_name;
    #[Validate('nullable|string|max:255')]
    public $address;
    #[Validate('nullable|string|max:15')]
    public $no_contact;
    #[Validate('nullable|string|max:100')]
    public $email;



    public function save()
    {
        try {
            $this->validate();

            Supplier::create([

                'supplier_code'      => $this->supplier_code,
                'supplier_name'      => $this->supplier_name,
                'address'            => $this->address,
                'no_contact'         => $this->no_contact,
                'email'              => $this->email

            ]);

            $this->reset([
                'supplier_code',
                'supplier_name',
                'address',
                'no_contact',
                'email'

            ]);

            // success message
            session()->flash('success', 'Item berhasil ditambahkan ✅');
            $this->dispatch('supplier-updated');
        } catch (\Exception $e) {
            // error message
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function resetForm()
    {

        $this->reset([
            'supplier_code',
            'supplier_name',
            'address',
            'no_contact',
            'email'
        ]);
    }


    public function render()
    {
        return view('livewire.superadmin.supplier.create-supplier');
    }
}
