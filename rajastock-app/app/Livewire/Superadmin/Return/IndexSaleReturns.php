<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\SalesReturn;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class IndexSaleReturns extends Component
{
    use WithPagination;

    public $search = '';

    #[Computed()]
    public function returns()
    {
        return SalesReturn::with('sale')
            ->when(
                $this->search,
                fn($q) =>
                $q->where('return_code', 'like', "%{$this->search}%")
                    ->orWhereHas('sale', fn($s) => $s->where('sale_code', 'like', "%{$this->search}%"))
            )
            ->latest()
            ->paginate(10);
    }



    public function render()
    {
        return view('livewire.superadmin.return.index-sale-returns',[
             'returns' => $this->returns,
        ]);
    }
}
