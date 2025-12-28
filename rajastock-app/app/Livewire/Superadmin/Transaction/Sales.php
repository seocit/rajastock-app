<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Sale;
use App\Models\Customer;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Sales extends Component
{
    use WithPagination;

    public $search = '';
    public $saleId;

    // FILTERS
    public $customerId = '';
    public $dateStart = '';
    public $dateEnd = '';


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
            ->when(
                $this->customerId,
                fn($q) =>
                $q->where('customer_id', $this->customerId)
            )
            ->when(
                $this->dateStart,
                fn($q) =>
                $q->whereDate('sale_date', '>=', $this->dateStart)
            )
            ->when(
                $this->dateEnd,
                fn($q) =>
                $q->whereDate('sale_date', '<=', $this->dateEnd)
            )

            ->latest()
            ->paginate(10);
    }

    public function resetFilters()
    {
        $this->customerId = '';
        $this->dateStart = '';
        $this->dateEnd = '';


        $this->dispatch('refreshSalesList');
    }

    public function showDetails($saleId)
    {
        $this->dispatch('showSaleDetails', saleId: $saleId);
    }

    public function confirmPostSale($id)
    {
        $this->saleId = $id;
        Flux::modal('post-sale')->show();
    }

    public function postSaleConfirmed()
    {
        $sale = Sale::with('saleDetails.item')->find($this->saleId);

        if (! $sale) {
            $this->dispatch('error', message: 'Data sale tidak ditemukan');
            Flux::modal('post-sale')->close();
            return;
        }

        if ($sale->is_posted) {
            $this->dispatch('error', message: 'Sale sudah diposting');
            Flux::modal('post-sale')->close();
            return;
        }

        try {
            DB::transaction(function () use ($sale) {

                // cek stok
                foreach ($sale->saleDetails as $detail) {
                    if ($detail->item->stock < $detail->quantity) {
                        throw new \Exception(
                            "Stok {$detail->item->item_name} tidak mencukupi"
                        );
                    }
                }

                // kurangi stok
                foreach ($sale->saleDetails as $detail) {
                    $detail->item->decrement('stock', $detail->quantity);
                }

                $sale->update([
                    'status' => 'posted',
                    'is_posted' => true,
                ]);
            });

            $this->dispatch('success', message: 'Sale berhasil diposting & stok dikurangi');
        } catch (\Exception $e) {
            $this->dispatch('error', message: $e->getMessage());
        }

        Flux::modal('post-sale')->close();
        $this->saleId = null;
    }



    public function delete($id)
    {
        $this->saleId = $id;
        Flux::modal('delete-sale')->show();
    }

    public function deleteSale()
    {
        $sale = Sale::find($this->saleId);

        if (! $sale) {
            $this->dispatch('error', message: 'Data sale tidak ditemukan');
            return;
        }

        if ($sale->is_posted) {
            $this->dispatch('error', message: 'Sale yang sudah diposting tidak bisa dihapus');
            Flux::modal('delete-sale')->close();
            return;
        }

        $sale->saleDetails()->delete();
        $sale->delete();

        $this->dispatch('success', message: 'Sale draft berhasil dihapus');

        Flux::modal('delete-sale')->close();
        $this->dispatch('refreshSalesList');

        $this->saleId = null;
    }


    public function render()
    {
        return view('livewire.superadmin.transaction.sale', [
            'customers' => Customer::all()
        ]);
    }
}
