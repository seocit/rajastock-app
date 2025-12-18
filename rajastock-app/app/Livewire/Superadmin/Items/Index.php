<?php

namespace App\Livewire\Superadmin\Items;

use App\Models\Item;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $itemId;
    public $filter = '';
  

    public function updatingSearch()
    {
        $this->resetPage(); //biar pagination balik ke halaman 1
    }

    #[Computed]
    public function items()
    {
        $search = trim($this->search);

        return Item::query()
            ->when($search, function ($query, $search) {
                $keywords = explode(' ', $search);

                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->where(function ($sub) use ($word) {
                            $sub->where('item_name', 'like', "%{$word}%")
                                ->orWhere('item_code', 'like', "%{$word}%")
                                ->orWhereHas(
                                    'merk',
                                    fn($q2) =>
                                    $q2->where('merk_name', 'like', "%{$word}%")
                                );
                        });
                    }
                });
            })

          
            ->when($this->filter === 'cheapest', fn($q) => $q->orderBy('selling_price', 'asc'))
            ->when($this->filter === 'expensive', fn($q) => $q->orderBy('selling_price', 'desc'))
            ->when($this->filter === 'most_stock', fn($q) => $q->orderBy('stock', 'desc'))
            ->when($this->filter === 'least_stock', fn($q) => $q->orderBy('stock', 'asc'))

            ->latest()
            ->paginate(10);
    }
    #[On('item-updated')]
    public function refreshItems()
    {
        // just refresh data items purpose
    }

    public function edit($id)
    {
        Flux::modal('edit-item')->show();
        $this->dispatch('edit-item', $id);
    }



    public function delete($id)
    {
        $this->itemId = $id;
        Flux::modal('delete-item')->show();
    }
    public function deleteItem()
    {
        Item::find($this->itemId)->delete();
        session()->flash('success', 'Item sucessfully deleted ✅');
        Flux::modal('delete-item')->close();
    }


    public function render()
    {
        return view(
            'livewire.superadmin.items.index'
        );
    }
}
