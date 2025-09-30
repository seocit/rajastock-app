<?php

namespace App\Livewire\Superadmin\Merk;

use App\Models\Merk;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{ 
    use WithPagination;

    public $search = '';
    public $merkId;

    #[Computed()]
    public function merks()
    {
        return Merk::query()
            ->when($this->search, function ($query) {
                $query->where('merk_name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
    }

    #[On('merk-updated')]
    public function refreshMerks()
    {
        // Kosongkan, Livewire otomatis re-render
    }

    public function edit($id)
    {
        Flux::modal('edit-merk')->show();        
        $this->dispatch('edit-merk', $id);
    }

    public function delete($id)
    {
        $this->merkId = $id;
        Flux::modal('delete-merk')->show();
    }

    public function deleteMerk()
    {
        Merk::find($this->merkId)?->delete();
        session()->flash('success', 'Merk successfully deleted ✅');    
        Flux::modal('delete-merk')->close();
    }

    public function render()
    {
        return view('livewire.superadmin.merk.merk');
    }
}
