<?php

namespace App\Livewire\Superadmin\Users;

use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public $search = '';
    public $userId;
    public $confirmText = '';

    public function updatingSearch()
    {
        $this->resetPage(); // biar pagination balik ke halaman 1
    }

    #[On('user-created')]
    public function refreshUsers()
    {
        // just refresh data items purpose
    }

    #[Computed()]
    public function users()
    {

        return User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function edit($id)
    {
        Flux::modal('edit-user')->show();
        $this->dispatch('edit-user', $id);
    }

    public function delete($id)
    {
        $this->userId = $id;
        Flux::modal('delete-user')->show();
    }
    #[on('delete-user')]
    public function deleteUser()
    {
        if ($this->confirmText !== 'delete') {
            session()->flash('error', 'Please type "delete" to confirm.');
            return;
        }

        $user = User::findOrFail($this->userId);
        $user->delete();

        session()->flash('success', 'User successfully deleted ✅');
        Flux::modal('delete-user')->close();

        // reset confirmText agar modal berikutnya kosong
        $this->confirmText = '';

        // optional: dispatch event untuk refresh tabel
        $this->dispatch('user-deleted');
    }


    public function render()
    {

        return view('livewire.superadmin.users.users');
    }
}
