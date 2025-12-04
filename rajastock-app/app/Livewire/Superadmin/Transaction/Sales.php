<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Sale;
use Flux\Flux;
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


     public function delete($id)
    {
        $this->saleId = $id;

        Flux::modal('delete-sale')->show();
    }

    public function deleteSale()
    {
        $sale = Sale::with('saleDetails.item')->find($this->saleId);

        if ($sale) {

            // Kembalikan stok
            foreach ($sale->saleDetails as $detail) {
                if ($detail->item) {
                    $detail->item->stock += $detail->qty;
                    $detail->item->save();
                }
            }

            // Hapus semua detail
            $sale->saleDetails()->delete();

            // Hapus Sales utama
            $sale->delete();
        }

        session()->flash('success', 'Sale berhasil dihapus!');

        Flux::modal('delete-sale')->close();

        // refresh tabel
        $this->dispatch('refreshSalesList');

        // clear id
        $this->saleId = null;
    }

    public function render()
    {
        return view('livewire.superadmin.transaction.sale');
    }
}
