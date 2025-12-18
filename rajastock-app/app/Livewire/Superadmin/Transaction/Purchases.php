<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Purchase;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Purchases extends Component
{
    use WithPagination;

    public $search = '';
    public $purchaseId;

    // filter
    public $dateStart;
    public $dateEnd;
    public $supplierId;


    #[Computed()]
    public function purchase()
    {
        return Purchase::with('supplier','user')
            ->when($this->search, function ($query) {
                $query->where('purchase_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas(
                        'supplier',
                        fn($q) =>
                        $q->where('supplier_name', 'like', '%' . $this->search . '%')
                    );
            })
            ->when(
                $this->supplierId,
                fn($q) =>
                $q->where('supplier_id', $this->supplierId)
            )
            ->when(
                $this->dateStart,
                fn($q) =>
                $q->whereDate('purchase_date', '>=', $this->dateStart)
            )
            ->when(
                $this->dateEnd,
                fn($q) =>
                $q->whereDate('purchase_date', '<=', $this->dateEnd)
            )

            ->latest()
            ->paginate(10);
    }
    public function resetFilters()
    {
        $this->dateStart = null;
        $this->dateEnd = null;
        $this->supplierId = null;
        $this->dispatch('refreshPurchaseList');
    }

    public function showDetails($purchaseId)
    {
        // kirim event ke modal terpisah
        $this->dispatch('showPurchaseDetails', purchaseId: $purchaseId);
    }

    public function post($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        if ($purchase->is_posted) {
            session()->flash('error', 'Purchase sudah diposting');
            return;
        }

        DB::transaction(function () use ($purchase) {

            foreach ($purchase->details as $detail) {
                $detail->item->increment('stock', $detail->quantity);
            }

            $purchase->update([
                'is_posted' => true,
            ]);
        });

        $this->dispatch('success', message: 'Purchase posted successfully!');
    }


    public function delete($id)
    {
        $this->purchaseId = $id;
        Flux::modal('delete-purchase')->show();
    }

    public function deletePurchase()
    {
        $purchase = Purchase::find($this->purchaseId);

        if (! $purchase) {
            $this->dispatch('error', message: 'Data purchase tidak ditemukan');
            return;
        }

        if ($purchase->is_posted) {
            $this->dispatch('error', message: 'Purchase yang sudah diposting tidak bisa dihapus');
            Flux::modal('delete-purchase')->close();
            return;
        }

        $purchase->delete();

        $this->dispatch('success', message: 'Purchase draft berhasil dihapus');

        Flux::modal('delete-purchase')->close();
        $this->dispatch('refreshPurchaseList');
    }





    public function render()
    {
        return view('livewire.superadmin.transaction.purchases');
    }
}
