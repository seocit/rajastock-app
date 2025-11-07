<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Sale;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Sales extends Component
{
    use WithPagination;

    public $search = '';
    public $saleId;

    #[Computed()]
    public function sales()
    {
        return Sale::with(['customer', 'saleDetails.item'])
            ->when($this->search, function ($query) {
                $query->where('sale_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('customer_name', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);
    }

    public function showDetails($saleId)
    {
        $this->dispatch('showSaleDetails', saleId: $saleId);
    }
    public function render()
    {
        return view('livewire.superadmin.transaction.sale');
    }
}
