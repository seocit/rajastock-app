<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Purchase;
use Livewire\Component;
use Livewire\WithPagination;

class DetailPurchase extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $purchaseId;
    public $purchase;

    protected $listeners = ['showPurchaseDetails' => 'open'];

    protected $paginationTheme = 'tailwind'; 

    public function open($purchaseId)
    {
        $this->purchaseId = $purchaseId;
        $this->purchase = Purchase::with('supplier')->find($purchaseId);
        $this->isOpen = true;

        
        $this->resetPage();
    }


    public function getDetailsProperty()
    {
        return \App\Models\PurchaseDetail::with('item')
            ->where('purchases_id', $this->purchaseId)
            ->paginate(5);
    }

    public function render()
    {
        return view('livewire.superadmin.transaction.detail-purchase', [
            'details' => $this->details,
        ]);
    }
}
