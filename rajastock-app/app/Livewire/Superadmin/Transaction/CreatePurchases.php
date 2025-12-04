<?php

namespace App\Livewire\Superadmin\Transaction;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB as FacadesDB;
use Livewire\Component;

class CreatePurchases extends Component
{
    public $suppliers;
    public $items;
    public $supplier_id;
    public $purchase_date;
    public $rows = [];
    public $total = 0;

    public function mount()
    {
        $this->suppliers = Supplier::all();
        $this->items = Item::all();
        $this->purchase_date = date('Y-m-d');
        $this->addRow(); // start with one empty row
    }

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


    public function refreshTotal()
    {
        foreach ($this->rows as $index => $row) {
            $qty = $row['quantity'] ?? 0;
            $price = $row['unit_price'] ?? 0;
            $discount = $row['discount'] ?? 0;

            // hitung subtotal per item
            $subtotal = ($qty * $price) * (1 - ($discount / 100));
            $this->rows[$index]['subtotal'] = max($subtotal, 0); // jaga-jaga biar gak minus
        }

        $this->calculateTotal();

        $this->dispatch('success', message: 'Totals refreshed successfully!');
    }

    private function calculateTotal()
    {
        $this->total = collect($this->rows)->sum('subtotal');
    }

    public function save()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'rows.*.item_id' => 'required|exists:items,id',
            'rows.*.discount' => 'nullable|numeric|min:0',
            'rows.*.quantity' => 'required|numeric|min:1',
            'rows.*.unit_price' => 'required|numeric|min:0',
        ]);

        FacadesDB::transaction(function () {
            $purchase = Purchase::create([
                'supplier_id' => $this->supplier_id,
                'purchase_code' => 'PUR-' . now()->format('YmdHis'),
                'purchase_date' => $this->purchase_date,
                'total_amount' => $this->total,
                'status' => 'completed',
            ]);

            foreach ($this->rows as $row) {

                $item = Item::find($row['item_id']);
                PurchaseDetail::create([
                    'purchases_id' => $purchase->id,
                    'item_id' => $row['item_id'],
                    'item_name' => $item->item_name,
                    'item_code' => $item->item_code,
                    'quantity' => $row['quantity'],
                    'discount' => $row['discount'] ?? 0,
                    'unit_price' => $row['unit_price'],
                    'subtotal' => $row['subtotal'],
                ]);

                // update item stock

                $item->increment('stock', $row['quantity']);
            }
        });

        $this->resetExcept('suppliers', 'items');
        $this->purchase_date = date('Y-m-d');
        $this->addRow();
        $this->dispatch('success', message: 'Purchase saved successfully!');
    }
    public function render()
    {
        return view('livewire.superadmin.transaction.create-purchases');
    }
}
