<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Purchase;
use Flux\Flux;
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

    public function delete($id)
    {
        $this->purchaseId = $id;
        Flux::modal('delete-purchase')->show();
    }

    public function deletePurchase()
    {
        $purchase = Purchase::find($this->purchaseId);

        if ($purchase) {
            $purchase->delete(); // otomatis hapus details & kembalikan stok
        }

        session()->flash('success', 'Purchase berhasil dihapus!');

        Flux::modal('delete-purchase')->close();
      
        // Refresh list di halaman utama
        $this->dispatch('refreshPurchaseList');
    }




    public function render()
    {
        return view('livewire.superadmin.transaction.purchases');
    }
}
