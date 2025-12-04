<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditPurchases extends Component
{
    public $purchaseId;
    public $suppliers = [];
    public $items = [];
    public $supplier_id;
    public $purchase_date;
    public $rows = [];
    public $total = 0;

    public function mount($id)
    {
        $this->purchaseId = $id;
        $this->suppliers = Supplier::all();
        $this->items = Item::all();

        $this->loadPurchase();
    }

    /** ------------------------------------------------------------------
     * Load Purchase for Editing
     * ------------------------------------------------------------------*/
    public function loadPurchase()
    {
        $purchase = Purchase::with('details')->findOrFail($this->purchaseId);

        if ($purchase->is_locked ?? false) {
            $this->dispatch('error', message: 'This purchase is locked and cannot be edited.');
            return redirect()->route('purchases');
        }

        $this->supplier_id = $purchase->supplier_id;
        $this->purchase_date = $purchase->purchase_date;

        $this->rows = $purchase->details->map(function ($detail) {
            return [
                'item_id' => $detail->item_id,
                'quantity' => $detail->quantity,
                'discount' => $detail->discount,
                'unit_price' => $detail->unit_price,
                'subtotal' => $detail->subtotal,
            ];
        })->toArray();

        $this->calculateTotal();
    }

    /** ------------------------------------------------------------------
     * Add & Remove Rows
     * ------------------------------------------------------------------*/
    public function addRow()
    {
        $this->rows[] = [
            'item_id' => '',
            'quantity' => 1,
            'discount' => 0,
            'unit_price' => 0,
            'subtotal' => 0,
        ];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
        $this->calculateTotal();
    }

    /** ------------------------------------------------------------------
     * Refresh Subtotals
     * Same behavior as CreatePurchases
     * ------------------------------------------------------------------*/
    public function refreshRow($index)
    {
        $qty = (float) ($this->rows[$index]['quantity'] ?? 0);
        $price = (float) ($this->rows[$index]['unit_price'] ?? 0);
        $discount = (float) ($this->rows[$index]['discount'] ?? 0);

        $subtotal = ($qty * $price) * (1 - ($discount / 100));

        $this->rows[$index]['subtotal'] = max($subtotal, 0);

        $this->calculateTotal();
    }

    public function refreshTotal()
    {
        foreach ($this->rows as $index => $row) {
            $this->refreshRow($index);
        }

        $this->dispatch('success', message: 'Totals refreshed successfully!');
    }

    private function calculateTotal()
    {
        $this->total = collect($this->rows)->sum('subtotal');
    }

    /** ------------------------------------------------------------------
     * Update Purchase
     * ------------------------------------------------------------------*/
    public function update()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'rows.*.item_id' => 'required|exists:items,id',
            'rows.*.quantity' => 'required|numeric|min:1',
            'rows.*.discount' => 'nullable|numeric|min:0',
            'rows.*.unit_price' => 'required|numeric|min:0',
        ]);

        $purchase = Purchase::with('details')->findOrFail($this->purchaseId);

        if ($purchase->is_locked ?? false) {
            $this->dispatch('error', message: 'This purchase is already locked.');
            return;
        }

        DB::transaction(function () use ($purchase) {

            /** 1️⃣ Kembalikan stok lama */
            foreach ($purchase->details as $detail) {
                Item::where('id', $detail->item_id)
                    ->increment('stock', -$detail->quantity);
            }

            /** 2️⃣ Update purchase utama */
            $purchase->update([
                'supplier_id' => $this->supplier_id,
                'purchase_date' => $this->purchase_date,
                'total_amount' => $this->total,
            ]);

            /** 3️⃣ Hapus semua detail lama */
            PurchaseDetail::where('purchases_id', $purchase->id)->delete();

            /** 4️⃣ Tambahkan detail baru + update stok */
            foreach ($this->rows as $row) {

                PurchaseDetail::create([
                    'purchases_id' => $purchase->id,
                    'item_id'      => $row['item_id'],
                    'quantity'     => $row['quantity'],
                    'discount'     => $row['discount'] ?? 0,
                    'unit_price'   => $row['unit_price'],
                    'subtotal'     => $row['subtotal'],
                ]);

                Item::where('id', $row['item_id'])
                    ->increment('stock', $row['quantity']);
            }
        });

        $this->dispatch('success', message: 'Purchase updated successfully!');
        return redirect()->route('purchases');
    }

    public function render()
    {
        return view('livewire.superadmin.transaction.edit-purchases');
    }
}
