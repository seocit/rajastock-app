<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\PurchaseReturn;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
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


            if ($return->status === 'completed') {
                foreach ($return->details as $detail) {
                    $purchaseDetail = $detail->purchaseDetail;

                    if ($purchaseDetail && $purchaseDetail->item) {
                        $item = $purchaseDetail->item;
                        $item->increment('stock', $detail->quantity_returned);
                    }
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

        DB::beginTransaction();
        try {
            $return = PurchaseReturn::with('details.purchaseDetail.item')->lockForUpdate()->findOrFail($id);
            $oldStatus = $return->status;

            // Kalau status tidak berubah, skip
            if ($oldStatus === $newStatus) {
                DB::rollBack();
                $this->dispatch('toast', type: 'info', message: 'Status tidak berubah.');
                return;
            }

            // TRANSISI: bukan completed -> completed (kurangi stok)
            if ($oldStatus !== 'completed' && $newStatus === 'completed') {
                foreach ($return->details as $detail) {
                    $item = $detail->purchaseDetail?->item;

                    if ($item) {
                        // optional: validasi stok cukup
                        if ($item->stock < $detail->quantity_returned) {
                            DB::rollBack();
                            $this->dispatch('toast', type: 'error', message: "Stok tidak cukup untuk item {$item->item_name}.");
                            return;
                        }

                        $item->decrement('stock', $detail->quantity_returned);
                    }
                }
            }

            // TRANSISI: completed -> selain completed (balikin stok)
            if ($oldStatus === 'completed' && $newStatus !== 'completed') {
                foreach ($return->details as $detail) {
                    $item = $detail->purchaseDetail?->item;
                    if ($item) {
                        $item->increment('stock', $detail->quantity_returned);
                    }
                }
            }

            // update status
            $return->status = $newStatus;
            $return->save();

            DB::commit();

            $this->dispatch('toast', type: 'success', message: 'Status berhasil diperbarui.');
            $this->dispatch('refreshReturnPurchaseList');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('toast', type: 'error', message: 'Gagal update status: ' . $e->getMessage());
        }
    }







    public function render()
    {
        return view('livewire.superadmin.return.index-purchase-return');
    }
}
