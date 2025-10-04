<?php

namespace App\Livewire\Superadmin\Customer;

use App\Models\Customer;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditCustomer extends Component
{
    #[Validate('nullable|string|max:20')]
    public $customer_code;
    #[Validate('nullable|string|max:100')]
    public $customer_name;
    #[Validate('nullable|string|max:255')]
    public $address;
    #[Validate('nullable|string|max:15')]
    public $no_contact;
    #[Validate('nullable|string|max:100')]
    public $email;

    public $customerId;

    #[On('edit-customer')]
    public function edit($id)
    {
        $customers = Customer::findorfail($id);
        $this->customerId = $customers->id;
        $this->customer_code = $customers->customer_code;
        $this->customer_name = $customers->customer_name;
        $this->address = $customers->address;
        $this->no_contact = $customers->no_contact;
        $this->email = $customers->email;
    }

    public function update()
    {
        try {
            $this->validate();
            $item = Customer::find($this->customerId);
            $item->customer_code = $this->customer_code;
            $item->customer_code = $this->customer_code;
            $item->address = $this->address;
            $item->no_contact = $this->no_contact;
            $item->email = $this->email;
            $item->save();

            session()->flash('success', 'Item berhasil diupdate ✅');
            Flux::modal('edit-customer')->close();
            $this->dispatch('customer-updated');
            $this->resetValidation();
        } catch (\Exception $e) {
            // error message
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }



    public function render()
    {
        return view('livewire.superadmin.customer.edit-customer');
    }
}
