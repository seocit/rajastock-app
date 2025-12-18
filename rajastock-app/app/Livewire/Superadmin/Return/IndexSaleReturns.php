<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\SalesReturn;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class IndexSaleReturns extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $dateStart;
    public $dateEnd;
    public $customerId;
    public $returnId;


    #[Computed()]
    public function returns()
    {
        return SalesReturn::with(['sale.customer'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where('return_code', 'like', "%{$this->search}%")
                    ->orWhereHas(
                        'sale',
                        fn($s) =>
                        $s->where('sale_code', 'like', "%{$this->search}%")
                    )
            )
            ->when(
                $this->customerId,
                fn($q) =>
                $q->whereHas(
                    'sale',
                    fn($s) =>
                    $s->where('customer_id', $this->customerId)
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
        $this->reset([
            'search',
            'status',
            'customerId',
            'dateStart',
            'dateEnd',
        ]);

        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, [
            'search',
            'status',
            'customerId',
            'dateStart',
            'dateEnd'
        ])) {
            $this->resetPage();
        }
    }


    public function delete($id)
    {
        $this->returnId = $id;
        \Flux\Flux::modal('delete-sale-return')->show();
    }

    public function deleteSaleReturn()
    {
        $return = SalesReturn::with('details.salesDetail.item')->find($this->returnId);

        if ($return) {

            foreach ($return->details as $detail) {
                $salesDetail = $detail->salesDetail;

                if ($salesDetail && $salesDetail->item) {
                    $item = $salesDetail->item;

                    // Tambah stok kembali
                    $item->stock += $detail->quantity_returned;
                    $item->save();
                }
            }

            $return->details()->delete();

            $return->delete();
        }

        session()->flash('success', 'Sales Return berhasil dihapus & stok dikembalikan!');

        Flux::modal('delete-sale-return')->close();

        // Refresh list
        $this->dispatch('refreshSaleReturnList');
    }

    public function updateStatus($id, $newStatus)
    {
        $allowed = ['pending', 'approved', 'rejected'];

        if (!in_array($newStatus, $allowed)) {
            $this->dispatch('toast', type: 'error', message: 'Status tidak valid.');
            return;
        }

        $return = SalesReturn::findOrFail($id);
        $return->status = $newStatus;
        $return->save();

        $this->dispatch('toast', type: 'success', message: 'Status berhasil diperbarui.');
    }




    public function render()
    {
        return view('livewire.superadmin.return.index-sale-returns', [
            'returns' => $this->returns,
        ]);
    }
}
