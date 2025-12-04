<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class EditSaleReturn extends Component
{
    public $salesReturn;
    public $selectedSale;
    public $selectedItems = [];

    public function mount($id)
    {
        $this->salesReturn = SalesReturn::with([
            'sale',
            'details.salesDetail.item'
        ])->findOrFail($id);

        $this->selectedSale = $this->salesReturn->sale;

        foreach ($this->salesReturn->details as $detail) {
            $this->selectedItems[$detail->sales_detail_id] = [
                'quantity_returned' => $detail->quantity_returned,
                'unit_price' => $detail->salesDetail->unit_price,
                'sub_total' => $detail->sub_total,
                'condition' => $detail->condition,
                'reason' => $detail->reason,
                'existing_id' => $detail->id // penting untuk update vs create
            ];
        }
    }

    public function toggleItem($detailId)
    {
        // uncheck → remove selected
        if (isset($this->selectedItems[$detailId])) {
            unset($this->selectedItems[$detailId]);
            return;
        }

        // check → add new item return
        $detail = $this->selectedSale->saleDetails->firstWhere('id', $detailId);
        if (! $detail) return;

        $price = $detail->unit_price ?? 0;

        $this->selectedItems[$detailId] = [
            'quantity_returned' => 1,
            'unit_price' => $price,
            'sub_total' => $price,
            'condition' => 'good',
            'reason' => '',
            'existing_id' => null
        ];
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
        if (empty($this->selectedItems)) {
            session()->flash('error', 'No items selected.');
            return;
        }

        $total = collect($this->selectedItems)->sum('sub_total');

        DB::beginTransaction();

        try {
            // update main return
            $this->salesReturn->update([
                'total_amount' => $total,
            ]);

            $existingIds = [];

            foreach ($this->selectedItems as $detailId => $item) {
                // update existing return detail
                if (!empty($item['existing_id'])) {
                    $detail = SalesReturnDetail::find($item['existing_id']);
                    $detail->update([
                        'quantity_returned' => $item['quantity_returned'],
                        'sub_total' => $item['sub_total'],
                        'condition' => $item['condition'],
                        'reason' => $item['reason']
                    ]);
                    $existingIds[] = $detail->id;
                } else {
                    // create new return detail
                    $new = SalesReturnDetail::create([
                        'sales_return_id' => $this->salesReturn->id,
                        'sales_detail_id' => $detailId,
                        'quantity_returned' => $item['quantity_returned'],
                        'sub_total' => $item['sub_total'],
                        'condition' => $item['condition'],
                        'reason' => $item['reason']
                    ]);
                    $existingIds[] = $new->id;
                }
            }

            // delete details that were unchecked
            SalesReturnDetail::where('sales_return_id', $this->salesReturn->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            DB::commit();

            session()->flash('success', 'Sales return updated successfully.');
            return redirect()->route('sale-returns');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.superadmin.return.edit-sale-return');
    }
}
