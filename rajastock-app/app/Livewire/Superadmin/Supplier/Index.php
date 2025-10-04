<?php

namespace App\Livewire\Superadmin\Supplier;

use App\Models\Supplier;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $search = '';
    public $supplierId;

    #[Computed()]
    public function suppliers()
    {
        return Supplier::query()
            ->when($this->search, function ($query) {
                $query->where('Supplier_name', 'like', '%' . $this->search . '%')
                    ->orWhere('Supplier_code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
    }

    #[On('supplier-updated')]
    public function refreshSupplier()
    {
        // Kosongkan, Livewire otomatis re-render
    }

    public function edit($id)
    {
        Flux::modal('edit-supplier')->show();
        $this->dispatch('edit-supplier', $id);
    }

    public function delete($id)
    {
        $this->supplierId = $id;
        Flux::modal('delete-supplier')->show();
    }

    public function deleteSupplier()
    {
        Supplier::find($this->supplierId)?->delete();
        session()->flash('success', 'supplier successfully deleted ✅');
        Flux::modal('delete-supplier')->close();
    }


    public function render()
    {
        return view('livewire.superadmin.supplier.index');
    }
}
