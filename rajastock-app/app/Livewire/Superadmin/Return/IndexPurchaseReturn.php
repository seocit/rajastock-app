<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\PurchaseReturn;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class IndexPurchaseReturn extends Component
{   
    use WithPagination;
    public $search = '';

    #[Computed()]
    public function returns()
    {
        return PurchaseReturn::with('purchase')
            ->when($this->search, fn($q) =>
                $q->where('return_number', 'like', "%{$this->search}%")
                  ->orWhereHas('purchase', fn($p) => $p->where('purchase_code', 'like', "%{$this->search}%"))
            )
            ->latest()
            ->paginate(10);

    }



    public function render()
    {
        return view('livewire.superadmin.return.index-purchase-return');
    }
}
