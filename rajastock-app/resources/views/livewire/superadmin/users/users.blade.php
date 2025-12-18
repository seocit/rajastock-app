<div>
    <flux:heading size="xl" level="1" class="mb-4">User Management</flux:heading>
    <flux:separator class="mb-4"></flux:separator>


    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">

        <div class="flex w-full">
            <div class="w-full md:w-1/3 mx-2">
                <flux:input wire:model.live.debounce.500ms="search" icon="magnifying-glass" placeholder="Search items..."
                    class="w-full" />
            </div>
        </div>
        @can('create users')
        <flux:modal.trigger name="create-user">
            <flux:button variant="primary" color="blue">Add User</flux:button>
        </flux:modal.trigger>
            
        @endcan
        <livewire:superadmin.users.create-user />
        <livewire:superadmin.users.edit-user />
    </div>

    <flux:heading size="lg" level="1" class="mb-4">User List</flux:heading>
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-max w-full border border-gray-200 border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Name</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Email</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Role</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Created At</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($this->users as $index => $user)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $this->users->firstItem() + $index }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $user->name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $user->getRoleNames()->first() }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                        @can('edit users')
                        <td class="px-4 py-2 text-sm text-gray-600">
                            <flux:button wire:click="edit({{ $user->id }})" :loading="true" variant="primary"
                                size="sm" color="blue">Edit</flux:button>
                            <flux:button wire:click="delete({{ $user->id }})" :loading="true"
                                variant="danger" size="sm">Delete</flux:button>
                        </td>
                            
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $this->users->links() }}
        </div>
    </div>

    {{-- modal confirm --}}
    <flux:modal name="delete-user" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete User?</flux:heading>

                <flux:text class="mt-2">
                    <p>Anda akan menghapus user ini</p>
                    <p>data tidak bisa dikembalikan. Konfirmasi, ketik <strong>delete</strong> dibawah.</p>
                </flux:text>

                {{-- Input konfirmasi --}}
                <flux:input type="text" placeholder="Type 'delete' to confirm" wire:model.live.debounce.200ms="confirmText"
                    class="mt-2 w-full border rounded p-2" />
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>

                {{-- Tombol Delete hanya aktif jika confirmText == 'delete' --}}
                <flux:button type="submit" variant="danger" wire:click="deleteUser"
                    wire:attr.disabled="{{ $confirmText !== 'delete' }}"
                    class="{{ $confirmText !== 'delete' ? 'opacity-50 cursor-not-allowed' : '' }}">
                    Ya, Hapus user!
                </flux:button>

            </div>
        </div>
    </flux:modal>


</div>
