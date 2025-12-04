<?php

namespace App\Livewire\Superadmin\Transaction;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class EditSale extends Component
{
    public $saleId;
    public $customers;
    public $items;

    public $customer_id;
    public $sale_date;
    public $description;

    public $rows = [];
    public $total = 0;

    /** ----------------------------------------------------
     * MOUNT
     * ---------------------------------------------------*/
    public function mount($id)
    {
        $this->saleId = $id;

        $this->customers = Customer::all();
        $this->items = Item::all();

        $this->loadSale();
    }

    /** ----------------------------------------------------
     * Load Sale Data
     * ---------------------------------------------------*/
    private function loadSale()
    {
        $sale = Sale::with('saleDetails')->findOrFail($this->saleId);

        $this->customer_id = $sale->customer_id;
        $this->sale_date = $sale->sale_date;
        $this->description = $sale->description;

        $this->rows = $sale->saleDetails->map(function ($detail) {
            return [
                'item_id'    => $detail->item_id,
                'quantity'   => $detail->quantity,
                'discount'   => $detail->discount,
                'unit_price' => $detail->unit_price,
                'subtotal'   => $detail->subtotal,
            ];
        })->toArray();

        $this->calculateTotal();
    }


    /** ----------------------------------------------------
     * Add / Remove Rows
     * ---------------------------------------------------*/
    public function addRow()
    {
        $this->rows[] = [
            'item_id'    => '',
            'quantity'   => 1,
            'discount'   => 0,
            'unit_price' => 0,
            'subtotal'   => 0,
        ];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
        $this->calculateTotal();
    }

    /** ----------------------------------------------------
     * Auto refresh subtotal & total
     * ---------------------------------------------------*/
    public function refreshUnitPrice($index)
    {
        $itemId = $this->rows[$index]['item_id'] ?? null;

        if ($itemId) {
            $item = Item::find($itemId);
            $this->rows[$index]['unit_price'] = $item->selling_price ?? 0;
        }

        $this->refreshTotal();
    }

    public function refreshTotal()
    {
        foreach ($this->rows as $index => $row) {
            $qty      = $row['quantity'] ?? 0;
            $price    = $row['unit_price'] ?? 0;
            $discount = $row['discount'] ?? 0;

            $subtotal = ($qty * $price) * (1 - ($discount / 100));

            $this->rows[$index]['subtotal'] = max($subtotal, 0);
        }

        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        $this->total = collect($this->rows)->sum('subtotal');
    }

    /** ----------------------------------------------------
     * UPDATE SALE
     * ---------------------------------------------------*/
    public function update()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'rows.*.item_id' => 'required|exists:items,id',
            'rows.*.quantity' => 'required|numeric|min:1',
            'rows.*.discount' => 'nullable|numeric|min:0',
            'rows.*.unit_price' => 'required|numeric|min:0',
        ]);

        $sale = Sale::with('saleDetails')->find($this->saleId);

        DB::transaction(function () use ($sale) {

            /** 1️⃣ Kembalikan stok lama */
            foreach ($sale->saleDetails as $detail) {
                Item::where('id', $detail->item_id)
                    ->increment('stock', $detail->quantity);
            }

            /** 2️⃣ Hapus detail lama */
            SaleDetail::where('sale_id', $sale->id)->delete();

            /** 3️⃣ Update sale */
            $sale->update([
                'customer_id' => $this->customer_id,
                'sale_date'   => $this->sale_date,
                'description' => $this->description,
                'total_amount' => $this->total,
            ]);

            /** 4️⃣ Tambahkan detail baru & kurangi stok */
            foreach ($this->rows as $row) {
                SaleDetail::create([
                    'sale_id'    => $sale->id,
                    'item_id'    => $row['item_id'],
                    'quantity'   => $row['quantity'],
                    'discount'   => $row['discount'],
                    'unit_price' => $row['unit_price'],
                    'subtotal'   => $row['subtotal'],
                ]);

                Item::where('id', $row['item_id'])
                    ->decrement('stock', $row['quantity']);
            }
        });

        $this->dispatch('success', message: 'Sale updated successfully!');
        return redirect()->route('sales');
    }

    public function render()
    {
        return view('livewire.superadmin.transaction.edit-sale');
    }
}
