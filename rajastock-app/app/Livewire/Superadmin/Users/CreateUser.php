<?php

namespace App\Livewire\Superadmin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;


use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateUser extends Component
{
    // account
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $roles = [];
    // roles and permissions
    public $selectedRole;

    public function mount()
    {     
        $this->roles = Role::orderBy('name')->get();            
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password)
        ]);

        // Assign role
        $user->assignRole($this->selectedRole);


        Session::flash('message', 'User created successfully.');

        $this->reset([  'name', 
                        'email', 
                        'password', 
                        'password_confirmation', 
                        'selectedRole']);
        
         $this->dispatch('user-created');

    }

    public function render()
    {
        return view('livewire.superadmin.users.create-user');
    }
}
