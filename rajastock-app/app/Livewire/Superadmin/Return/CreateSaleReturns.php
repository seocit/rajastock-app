<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CreateSaleReturns extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedSale = null;
    public $selectedItems = [];

    protected $queryString = ['search'];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function selectSale($id)
    {
        $this->selectedSale = Sale::with('saleDetails.item', 'customer')->find($id);
        $this->selectedItems = [];
    }

    public function toggleItem($detailId)
    {
        if (! $this->selectedSale) return;

        if (isset($this->selectedItems[$detailId])) {
            unset($this->selectedItems[$detailId]);
            return;
        }

        $detail = $this->selectedSale->saleDetails->firstWhere('id', $detailId);

        if (! $detail) return;

        // Ambil harga per item (fallback untuk kolom berbeda)
        $unitPrice = $detail->unit_price ?? $detail->price ?? $detail->harga_satuan ?? 0;

        $quantity = 1;
        $this->selectedItems[$detailId] = [
            'quantity_returned' => $quantity,
            'unit_price' => (float) $unitPrice,
            'sub_total' => (float) $unitPrice * $quantity,
            'condition' => 'good',
            'reason' => ''
        ];
    }

    public function updateQuantity($detailId, $quantity)
    {
        if (! isset($this->selectedItems[$detailId])) return;

        $quantity = (int) $quantity;
        if ($quantity < 1) $quantity = 1;

        $this->selectedItems[$detailId]['quantity_returned'] = $quantity;
        $this->selectedItems[$detailId]['sub_total'] =
            $this->selectedItems[$detailId]['unit_price'] * $quantity;
    }

    public function recalculateSubTotal($detailId)
    {
        if (! isset($this->selectedItems[$detailId])) return;

        $qty = (int) $this->selectedItems[$detailId]['quantity_returned'];
        $price = (float) $this->selectedItems[$detailId]['unit_price'];
        $this->selectedItems[$detailId]['sub_total'] = $qty * $price;
    }

    public function save()
    {
        if (! $this->selectedSale) {
            session()->flash('error', 'No sale selected.');
            return redirect()->route('sales-returns');
        }

        if (empty($this->selectedItems)) {
            session()->flash('error', 'No items selected for return.');
            return redirect()->route('sales-returns');
        }

        $total = collect($this->selectedItems)->sum('sub_total');

        DB::beginTransaction();
        try {
            $return = SalesReturn::create([
                'return_code' => 'SRN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'sale_id' => $this->selectedSale->id,
                'return_date' => now(),
                'total_amount' => $total,
                'status' => 'pending',
                'reason' => null,
            ]);

            foreach ($this->selectedItems as $detailId => $item) {
                SalesReturnDetail::create([
                    'sales_return_id' => $return->id,
                    'sales_detail_id' => $detailId,
                    'quantity_returned' => $item['quantity_returned'],
                    'sub_total' => $item['sub_total'],
                    'condition' => $item['condition'],
                    'reason' => $item['reason']
                ]);
            }

            DB::commit();

            session()->flash('success', 'Sales return created successfully.');
            return redirect()->route('sales-returns');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create sales return: ' . $e->getMessage());
            return redirect()->route('sale-returns');
        }
    }

    public function render()
    {
        $sales = Sale::with('customer')
            ->when($this->search, function ($query) {
                $query->where('sale_code', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', fn($q) => $q->where('customer_name', 'like', "%{$this->search}%"));
            })
            ->latest()
            ->paginate(10);

        return view('livewire.superadmin.return.create-sale-returns',[
            'sales' => $sales,
        ]);
    }
}
