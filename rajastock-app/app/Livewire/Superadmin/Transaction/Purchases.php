<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Purchase;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Purchases extends Component
{
    use WithPagination;

    public $search = '';
    public $purchaseId;

    #[Computed()]
    public function purchase()
    {
        return Purchase::with('supplier')
            ->when($this->search, function ($query) {
                $query->where('purchase_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('supplier_name', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);
    }

    public function showDetails($purchaseId)
    {
        // kirim event ke modal terpisah
        $this->dispatch('showPurchaseDetails', purchaseId: $purchaseId);
    }


    public function render()
    {
        return view('livewire.superadmin.transaction.purchases');
    }
}
