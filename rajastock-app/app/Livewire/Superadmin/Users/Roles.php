<?php

namespace App\Livewire\Superadmin\Users;


use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Roles extends Component
{
    use WithPagination;

    public $roleName;
    public $permissionName;


    public function createPermission()
    {
        Permission::create(['name' => $this->permissionName]);
        $this->reset('permissionName');
        $this->dispatch('$refresh');
    }
    public function createRole()
    {
        Role::create(['name' => $this->roleName]);
        $this->reset('roleName');
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.superadmin.users.roles',
            ['roles' => Role::latest()->paginate(10),
             'permissions' => Permission::latest()->paginate(10),
            ]
        );
    }
}
