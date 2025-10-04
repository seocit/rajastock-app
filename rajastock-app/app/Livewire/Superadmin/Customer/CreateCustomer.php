<?php

namespace App\Livewire\Superadmin\Customer;


use App\Models\Customer;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateCustomer extends Component
{
    #[Validate('required|string|max:20|unique:customers,customer_code')]
    public $customer_code;
    #[Validate('required|string|max:100')]
    public $customer_name;
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

            Customer::create([
                'customer_code'      => $this->customer_code,
                'customer_name'      => $this->customer_name,
                'address'            => $this->address,
                'no_contact'         => $this->no_contact,
                'email'              => $this->email

            ]);

            $this->reset([
                'customer_code',
                'customer_name',
                'address',
                'no_contact',
                'email'

            ]);

            // success message
            session()->flash('success', 'Item berhasil ditambahkan ✅');
            $this->dispatch('customer-updated');
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
        return view('livewire.superadmin.customer.create-customer');
    }
}
