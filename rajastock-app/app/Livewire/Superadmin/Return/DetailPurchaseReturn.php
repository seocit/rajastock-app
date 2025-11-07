<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\PurchaseReturn;
use Livewire\Component;
use Livewire\WithPagination;

class DetailPurchaseReturn extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $returnId;
    public $returnData;

    protected $listeners = ['showReturnDetails' => 'open'];
    protected $paginationTheme = 'tailwind';

    public function open($returnId)
    {
        $this->returnId = $returnId;
        $this->returnData = PurchaseReturn::with(['purchase', 'details.purchaseDetail.item'])
            ->find($returnId);

        $this->isOpen = true;
        $this->resetPage();
    }

    public function getDetailsProperty()
    {
        return $this->returnData
            ? $this->returnData->details()->with('purchaseDetail.item')->paginate(5) : collect();
    }    

    public function render()
    {
        return view('livewire.superadmin.return.detail-purchase-return', [
            'details' => $this->details,
        ]);
    }

}
