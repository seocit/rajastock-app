<?php

namespace App\Livewire\Superadmin\Merk;

use App\Models\Merk;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditMerk extends Component
{

    #[Validate('required|string|max:20')]
    public $code;
    #[Validate('required|string|max:100')]
    public $name;

    public $merkId;

    #[On('edit-merk')]
    public function edit($id)
    {
        $merks = Merk::findorfail($id);
        $this->merkId = $merks->id;
        $this->code = $merks->code;
        $this->name = $merks->merk_name;
    }

    public function update()
    {
        try {
            $this->validate();
            $item = Merk::find($this->merkId);
            $item->code = $this->code;
            $item->merk_name = $this->name;           
            $item->save();

            session()->flash('success', 'Item berhasil diupdate ✅');
            Flux::modal('edit-merk')->close();
            $this->dispatch('merk-updated');
            $this->resetValidation();
        } catch (\Exception $e) {
            // error message
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.superadmin.merk.edit-merk');
    }
}
