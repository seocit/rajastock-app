<?php

namespace App\Livewire\Superadmin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Flux\Flux;
use Livewire\Attributes\On;

class EditUser extends Component
{
    public $userId;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $roles = [];
    public $selectedRole;

    public function mount()
    {
        $this->roles = Role::orderBy('name')->get();
    }

    #[On('edit-user')] // event dari Index
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRole = $user->roles->first()?->name ?? '';
    }

    public function update()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->userId],
            'password' => ['nullable', 'string', 'confirmed', Rules\Password::defaults()],
            'selectedRole' => ['required'],
        ]);

        $user = User::findOrFail($this->userId);
        $user->name = $this->name;
        $user->email = $this->email;
        if ($this->password) {
            $user->password = Hash::make($this->password);
        }
        $user->save();

        // Sync role
        $user->syncRoles([$this->selectedRole]);

        session()->flash('success', 'User berhasil diupdate ✅');
        Flux::modal('edit-user')->close();
        $this->dispatch('user-updated');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.superadmin.users.edit-user');
    }
}
