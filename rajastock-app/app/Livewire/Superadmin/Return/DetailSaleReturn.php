<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\SalesReturn;
use Livewire\Component;
use Livewire\WithPagination;

class DetailSaleReturn extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $returnId;
    public $returnData;

    protected $listeners = ['showSaleReturnDetails' => 'open'];
    protected $paginationTheme = 'tailwind';

    public function open($returnId)
    {
        $this->returnId = $returnId;
        $this->returnData = SalesReturn::with(['sale', 'details.salesDetail.item'])
            ->find($returnId);

        $this->isOpen = true;
        $this->resetPage();
    }

    public function getDetailsProperty()
    {
        return $this->returnData
            ? $this->returnData->details()->with('salesDetail.item')->paginate(5)
            : collect();
    }
    
    public function render()
    {
        return view('livewire.superadmin.return.detail-sale-return',[
            'details' => $this->details,
        ]);
    }
}
