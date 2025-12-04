<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditPurchaseReturn extends Component
{
    public $returnId;

    public $purchaseReturn;
    public $purchase;
    public $return_date;
    public $description;

    public $items = []; // HANYA item yang dipilih untuk return
    public $total_amount = 0;

    public function mount($id)
    {
        $this->returnId = $id;
        $this->loadExistingData();
    }

    /**
     * Load hanya data return existing
     */
    protected function loadExistingData()
    {
        $this->purchaseReturn = PurchaseReturn::with([
            'purchase.details.item',
            'details.purchaseDetail.item'
        ])->findOrFail($this->returnId);

        $this->purchase = $this->purchaseReturn->purchase;
        $this->return_date = $this->purchaseReturn->return_date;
        $this->description = $this->purchaseReturn->description;

        // ISI $items hanya dengan detail RETURN yang sudah ada
        foreach ($this->purchaseReturn->details as $detailReturn) {

            $detail = $detailReturn->purchaseDetail;

            $this->items[$detail->id] = [
                'purchase_detail_id' => $detail->id,
                'item_name' => $detail->item->item_name,
                'quantity' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'quantity_returned' => $detailReturn->quantity_returned,
                'condition' => $detailReturn->condition,
                'reason' => $detailReturn->reason,
                'sub_total' => $detailReturn->sub_total
            ];
        }

        $this->calculateTotal();
    }

    /**
     * Toggle item dari checkbox
     */
    public function toggleItem($detailId)
    {
        $detail = $this->purchase->details->firstWhere('id', $detailId);

        // Jika sudah dipilih → hapus
        if (isset($this->items[$detailId])) {
            unset($this->items[$detailId]);
        }
        // Jika belum → tambahkan default
        else {
            $this->items[$detailId] = [
                'purchase_detail_id' => $detail->id,
                'item_name' => $detail->item->item_name,
                'quantity' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'quantity_returned' => 0,
                'condition' => 'good',
                'reason' => '',
                'sub_total' => 0,
            ];
        }

        $this->updatedItems();
    }

    /**
     * Perubahan qty / harga
     */
    public function updatedItems()
    {
        foreach ($this->items as $i => $item) {
            $qty = (int) ($item['quantity_returned'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $this->items[$i]['sub_total'] = $qty * $price;
        }

        $this->calculateTotal();
    }

    protected function calculateTotal()
    {
        $this->total_amount = array_sum(array_column($this->items, 'sub_total'));
    }

    /**
     * Save
     */
    public function update()
    {
        DB::transaction(function () {

            $this->purchaseReturn->update([
                'return_date' => $this->return_date,
                'description' => $this->description,
                'total_returned_amount' => $this->total_amount,
            ]);

            // hapus detail lama
            PurchaseReturnDetail::where('purchase_return_id', $this->purchaseReturn->id)->delete();

            // insert detail baru
            foreach ($this->items as $item) {
                if ($item['quantity_returned'] > 0) {
                    PurchaseReturnDetail::create([
                        'purchase_return_id' => $this->purchaseReturn->id,
                        'purchase_detail_id' => $item['purchase_detail_id'],
                        'quantity_returned' => $item['quantity_returned'],
                        'sub_total' => $item['sub_total'],
                        'condition' => $item['condition'],
                        'reason' => $item['reason'],
                    ]);
                }
            }
        });

        session()->flash('success', 'Purchase return updated.');

        return redirect()->route('purchase-returns');
    }

    public function render()
    {
        return view('livewire.superadmin.return.edit-purchase-return');
    }
}
