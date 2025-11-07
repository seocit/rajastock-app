<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Sale;
use App\Models\SaleDetail;
use Livewire\Component;
use Livewire\WithPagination;

class SalesDetail extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $saleId;
    public $sale;

    protected $listeners = ['showSaleDetails' => 'open'];

    protected $paginationTheme = 'tailwind'; 

    public function open($saleId)
    {
        $this->saleId = $saleId;
        $this->sale = Sale::with('customer')->find($saleId);
        $this->isOpen = true;

      
        $this->resetPage();
    }

    public function getDetailsProperty()
    {
        return SaleDetail::with('item')
            ->where('sale_id', $this->saleId)
            ->paginate(5);
    }

    public function render()
    {
        return view('livewire.superadmin.transaction.sales-detail', [
            'details' => $this->details,
        ]);
    }
}
