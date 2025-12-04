<div>

    <!-- TITLE -->
    <flux:heading size="xl" level="1">Audit Log</flux:heading>
    <flux:text class="mt-2">All user activity recorded in the system</flux:text>
    <flux:separator class="mb-4" />

    <!-- FILTER BAR -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        
        <div class="flex w-full">
            <!-- Search -->
            <div class="w-full md:w-1/3 mx-2">
                <flux:input 
                    wire:model.live.debounce.500ms="search" 
                    icon="magnifying-glass"
                    placeholder="Search model or event..."
                    class="w-full"
                />
            </div>

            <!-- Event Filter -->
            <div class="w-40 mx-2">
                <flux:select wire:model.live="event">
                    <option value="">All Events</option>
                    <option value="created">Created</option>
                    <option value="updated">Updated</option>
                    <option value="deleted">Deleted</option>
                </flux:select>
            </div>

          
        </div>

    </div>

    <!-- TABLE CARD -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-max w-full border border-gray-200 border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">User</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Model</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Event</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            {{ $log->user->name ?? 'System' }}
                        </td>

                        <td class="px-4 py-2 text-sm text-gray-600">
                            {{ class_basename($log->model) }}
                        </td>

                        <td class="px-4 py-2 text-sm text-gray-600 capitalize">
                            {{ $log->event }}
                        </td>

                        <td class="px-4 py-2 text-sm text-gray-600">
                            {{ $log->created_at->format('d M Y H:i') }}
                        </td>

                        <td class="px-4 py-2 text-sm">
                            <flux:button variant="primary" size="sm" color="blue"
                                wire:click="viewDetail({{ $log->id }})">
                                Detail
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-sm text-gray-600">
                            No audit data found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-2">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- DETAIL MODAL -->
    <flux:modal wire:model="showModal" max-width="4xl">

        <div class="p-6 space-y-4">

            <flux:heading size="lg">Audit Log Detail</flux:heading>

            @if ($selectedLog)

                <!-- Info GRID -->
                <div class="grid grid-cols-2 gap-4 text-sm">

                    <div>
                        <p class="text-gray-500">User</p>
                        <p class="font-semibold">{{ $selectedLog->user->name ?? 'System' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Event</p>
                        <p class="font-semibold capitalize">{{ $selectedLog->event }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Model</p>
                        <p class="font-semibold">{{ $selectedLog->model }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Date</p>
                        <p class="font-semibold">{{ $selectedLog->created_at->format('d M Y H:i:s') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">IP Address</p>
                        <p class="font-semibold">{{ $selectedLog->ip_address }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">User Agent</p>
                        <p class="font-semibold break-all text-xs">{{ $selectedLog->user_agent }}</p>
                    </div>
                </div>

                <!-- OLD VALUES -->
                <div>
                    <h3 class="font-semibold mt-4">Old Values</h3>
                    <pre class="bg-gray-100 p-3 rounded text-sm">
{{ is_string($selectedLog->old_values)
    ? json_encode(json_decode($selectedLog->old_values, true), JSON_PRETTY_PRINT)
    : json_encode($selectedLog->old_values, JSON_PRETTY_PRINT)
}}
                    </pre>
                </div>

                <!-- NEW VALUES -->
                <div>
                    <h3 class="font-semibold mt-4">New Values</h3>
                    <pre class="bg-gray-100 p-3 rounded text-sm">
{{ is_string($selectedLog->new_values)
    ? json_encode(json_decode($selectedLog->new_values, true), JSON_PRETTY_PRINT)
    : json_encode($selectedLog->new_values, JSON_PRETTY_PRINT)
}}
                    </pre>
                </div>

            @endif
        </div>

    </flux:modal>

</div>
