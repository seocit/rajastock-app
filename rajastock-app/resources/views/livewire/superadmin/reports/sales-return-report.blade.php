<div class="p-4">

    <flux:heading size="xl" level="1">Laporan Sales Return</flux:heading>
    <flux:text class="mt-2">Preview laporan + export PDF / Excel / CSV</flux:text>
    <flux:separator class="mb-4" />

    <!-- FILTERS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">

        {{-- Search --}}
        <flux:input
            wire:model.live.debounce.500ms="search"
            placeholder="Cari kode retur..."
        />

        {{-- Customer --}}
        <flux:select wire:model.live="customer">
            <option value="">Semua Customer</option>
            @foreach ($customers as $c)
                <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
            @endforeach
        </flux:select>

        {{-- Dari Tanggal --}}
        <flux:input type="date" wire:model.live="dateStart" />

        {{-- Sampai Tanggal --}}
        <flux:input type="date" wire:model.live="dateEnd" />
    </div>

    <!-- EXPORT & MODE AREA -->
    <div class="flex flex-wrap items-end gap-3 mb-4">

        <!-- MODE CETAK -->
        <div class="flex flex-col">
            <label class="font-semibold text-sm mb-1">Mode Cetak</label>
            <select wire:model.live.debounce.300ms="printMode" class="border p-2 rounded w-48">
                <option value="summary">Ringkas (Tanpa Detail)</option>
                <option value="full">Lengkap (Dengan Detail)</option>
            </select>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex items-center gap-2">
            <flux:button wire:click="exportPdf" variant="primary" color="red">
                PDF
            </flux:button>
            <flux:button wire:click="exportExcel" variant="primary" color="green">
                Excel
            </flux:button>
            <flux:button wire:click="exportCsv" variant="primary">
                CSV
            </flux:button>
        </div>

    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2">Kode Retur</th>
                    <th class="px-3 py-2">Customer</th>
                    <th class="px-3 py-2">Tanggal</th>

                    @if ($printMode === 'summary')
                        <th class="px-3 py-2 text-right">Total Item</th>
                        <th class="px-3 py-2 text-right">Total Qty</th>
                    @else
                        <th class="px-3 py-2 text-right">Total Qty</th>
                    @endif
                </tr>
            </thead>

            <tbody>
            @forelse ($this->returns as $r)

                {{-- SUMMARY MODE --}}
                @if ($printMode === 'summary')
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ $r->return_code }}</td>
                        <td class="px-3 py-2">
                            {{ $r->sale->customer->customer_name ?? '-' }}
                        </td>
                        <td class="px-3 py-2">
                            {{ \Carbon\Carbon::parse($r->return_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            {{ $r->details->count() }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            {{ $r->details->sum('quantity_returned') }}
                        </td>
                    </tr>

                {{-- FULL MODE --}}
                @else
                    {{-- HEADER RETUR --}}
                    <tr class="border-b bg-gray-50 font-semibold">
                        <td class="px-3 py-2">{{ $r->return_code }}</td>
                        <td class="px-3 py-2">
                            {{ $r->sale->customer->customer_name ?? '-' }}
                        </td>
                        <td class="px-3 py-2">
                            {{ \Carbon\Carbon::parse($r->return_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            {{ $r->details->sum('quantity_returned') }}
                        </td>
                    </tr>

                    {{-- DETAIL ITEM --}}
                    <tr>
                        <td colspan="5" class="px-3 py-2">
                            <table class="w-full text-xs border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-2 py-1">Kode Item</th>
                                        <th class="border px-2 py-1">Nama Item</th>
                                        <th class="border px-2 py-1 text-right">Qty</th>
                                        <th class="border px-2 py-1">Kondisi</th>
                                        <th class="border px-2 py-1 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($r->details as $d)
                                        <tr>
                                            <td class="border px-2 py-1">{{ $d->item_code }}</td>
                                            <td class="border px-2 py-1">{{ $d->item_name }}</td>
                                            <td class="border px-2 py-1 text-right">
                                                {{ $d->quantity_returned }}
                                            </td>
                                            <td class="border px-2 py-1">
                                                {{ ucfirst($d->condition) }}
                                            </td>
                                            <td class="border px-2 py-1 text-right">
                                                Rp {{ number_format($d->sub_total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endif

            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">
                        Tidak ada data sales return
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="p-3">
            {{ $this->returns->links() }}
        </div>
    </div>

</div>
