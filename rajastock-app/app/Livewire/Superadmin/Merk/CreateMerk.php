<?php

namespace App\Livewire\Superadmin\Merk;

use App\Models\Merk;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateMerk extends Component
{

    #[Validate('required|string|max:20|unique:merks,code')]
    public $code;
    #[Validate('required|string|max:100')]
    public $name;

    public function save()
    {
        try {
            $this->validate();

            Merk::create([

                'code'      => $this->code,
                'merk_name'      => $this->name,

            ]);

            $this->reset([
                'code',
                'name',

            ]);

            // success message
            session()->flash('success', 'Item berhasil ditambahkan ✅');
            $this->dispatch('merk-updated');
        } catch (\Exception $e) {
            // error message
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function resetForm()
    {

        $this->reset([
            'code',
            'merk_name',

        ]);
    }

    public function render()
    {
        return view('livewire.superadmin.merk.create-merk');
    }
}
