<?php

namespace App\Livewire\Superadmin\Customer;


use App\Models\Customer;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $customerId;

    #[Computed()]
    public function customers()
    {
        return Customer::query()
            ->when($this->search, function ($query) {
                $query->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
    }

    #[On('customer-updated')]
    public function refreshCustomer()
    {
        // Kosongkan, Livewire otomatis re-render
    }
      public function edit($id)
    {
        Flux::modal('edit-customer')->show();
        $this->dispatch('edit-customer', $id);
    }

    public function delete($id)
    {
        $this->customerId = $id;
        Flux::modal('delete-customer')->show();
    }

    public function deleteCustomer()
    {
        Customer::find($this->customerId)?->delete();
        session()->flash('success', 'customer successfully deleted ✅');
        Flux::modal('delete-customer')->close();
    }

    public function render()
    {
        return view('livewire.superadmin.customer.index');
    }
}
