<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\PurchaseReturn;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class IndexPurchaseReturn extends Component
{
    use WithPagination;
    public $search = '';
    public $returnId;

    //filter
    public $supplierId;
    public $dateStart;
    public $dateEnd;
    public $status;


    #[Computed()]
    public function returns()
    {
        return PurchaseReturn::with(['purchase.supplier'])
            ->when($this->search, function ($q) {
                $q->where('return_number', 'like', "%{$this->search}%")
                    ->orWhereHas(
                        'purchase',
                        fn($p) =>
                        $p->where('purchase_code', 'like', "%{$this->search}%")
                    );
            })
            ->when(
                $this->supplierId,
                fn($q) =>
                $q->whereHas(
                    'purchase',
                    fn($p) =>
                    $p->where('supplier_id', $this->supplierId)
                )
            )
            ->when(
                $this->status,
                fn($q) =>
                $q->where('status', $this->status)
            )
            ->when(
                $this->dateStart,
                fn($q) =>
                $q->whereDate('return_date', '>=', $this->dateStart)
            )
            ->when(
                $this->dateEnd,
                fn($q) =>
                $q->whereDate('return_date', '<=', $this->dateEnd)
            )
            ->latest()
            ->paginate(10);
    }

    public function resetFilters()
    {
        $this->search     = '';
        $this->supplierId = null;
        $this->dateStart  = null;
        $this->dateEnd    = null;
        $this->status     = null;
    }


    public function delete($id)
    {
        $this->returnId = $id;
        Flux::modal('delete-purchase-return')->show();
    }

    public function deletePurchaseReturn()
    {
        $return = PurchaseReturn::with('details.purchaseDetail.item')->find($this->returnId);

        if ($return) {


            foreach ($return->details as $detail) {
                $purchaseDetail = $detail->purchaseDetail;

                if ($purchaseDetail && $purchaseDetail->item) {
                    $item = $purchaseDetail->item;

                    // tambah stok sesuai qty return
                    $item->stock += $detail->quantity_returned;
                    $item->save();
                }
            }


            $return->details()->delete();


            $return->delete();
        }

        session()->flash('success', 'Purchase Return berhasil dihapus & stok dikembalikan!');

        Flux::modal('delete-purchase-return')->close();

        $this->dispatch('refreshReturnPurchaseList');
    }

    public function updateStatus($id, $newStatus)
    {
        $allowed = ['pending', 'completed', 'cancelled'];

        if (!in_array($newStatus, $allowed)) {
            $this->dispatch('toast', type: 'error', message: 'Status tidak valid.');
            return;
        }

        $return = PurchaseReturn::with('details.purchaseDetail.item')->findOrFail($id);

        // update status
        $return->status = $newStatus;
        $return->save();

        $this->dispatch('toast', type: 'success', message: 'Status berhasil diperbarui.');
        $this->dispatch('refreshReturnPurchaseList');
    }






    public function render()
    {
        return view('livewire.superadmin.return.index-purchase-return');
    }
}
