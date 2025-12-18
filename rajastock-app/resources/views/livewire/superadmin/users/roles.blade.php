<div>

    <flux:heading size="xl" level="1" class="mb-4">Roles</flux:heading>
    <flux:separator class="mb-4"></flux:separator>

    {{-- Create Role --}}
    <div class="border border-gray-300/50 rounded-lg p-4 mb-6">
        <flux:heading size="lg" level="2" class="mb-4">Create Role</flux:heading>

        <div class="flex items-end gap-4">
            <div>
                <flux:input class="max-w-xs" placeholder="role name..." size="sm" type="text"
                    wire:model="roleName" :error="$errors->first('roleName')" required />
            </div>

            <flux:button type="submit" size="sm" wire:click="createRole" class="px-6">
                Add
            </flux:button>
        </div>
    </div>

    {{-- Create Permission --}}
    <div class="border border-gray-300/50 rounded-lg p-4 mb-6">
        <flux:heading size="lg" level="2" class="mb-4">Create Permission</flux:heading>

        <div class="flex items-end gap-4">
            <div>
                <flux:input class="max-w-xs" placeholder="permission name..." size="sm" type="text"
                    wire:model="permissionName" :error="$errors->first('permissionName')" required />
            </div>

            <flux:button type="submit" size="sm" wire:click="createPermission" class="px-6">
                Add
            </flux:button>
        </div>
    </div>

    
    {{-- Group table --}}
    <div class="flex border border-gray-300/50 rounded-lg p-4 gap-4">
        {{-- Roles List --}}
        <div class="border border-gray-300/50 rounded-lg p-4">
            <flux:heading size="lg" level="2" class="mb-4">Roles List</flux:heading>

            <div class="overflow-x-auto  bg-white shadow rounded-lg">
                <table class="min-w-max w-full border border-gray-200 border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Role Name</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse ($roles as $role)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $loop->index + 1 }}</td>

                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $role->name }}
                                </td>

                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center text-sm text-gray-600">
                                    No roles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div>
                    {{ $roles->links() }}
                </div>
            </div>
        </div>

        {{-- Permission List --}}
        <div class="border border-gray-300/50 rounded-lg p-4">
            <flux:heading size="lg" level="2" class="mb-4">Permission List</flux:heading>

            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="min-w-max w-full border border-gray-200 border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Permission Name</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse ($permissions as $permission)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $loop->index + 1 }}</td>

                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $permission->name }}
                                </td>

                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center text-sm text-gray-600">
                                    No permission found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div>
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
