<x-modal name="edit-user" title="Edit User" subtitle="Update user data">
    <form wire:submit.prevent="update" class="space-y-4">

        {{-- Name --}}
        <div>
            <label class="font-medium">Name</label>
            <input type="text" wire:model="name" class="w-full border rounded p-2 focus:ring focus:ring-blue-300" />
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="font-medium">Email</label>
            <input type="email" wire:model="email" class="w-full border rounded p-2 focus:ring focus:ring-blue-300" />
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="font-medium">Password</label>
            <input type="password" wire:model="password" class="w-full border rounded p-2 focus:ring focus:ring-blue-300" />
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="font-medium">Confirm Password</label>
            <input type="password" wire:model="password_confirmation" class="w-full border rounded p-2 focus:ring focus:ring-blue-300" />
        </div>

        {{-- Role --}}
        <div>
            <label class="font-medium">Role</label>
            <select wire:model="selectedRole" class="w-full border rounded p-2 focus:ring focus:ring-blue-300">
                <option value="">-- Select Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
            @error('selectedRole') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Update
            </button>
        </div>

    </form>
</x-modal>
