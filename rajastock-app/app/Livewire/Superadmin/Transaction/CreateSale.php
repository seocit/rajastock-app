<?php

namespace App\Livewire\Superadmin\Transaction;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CreateSale extends Component
{
    public $customers;
    public $items;
    public $customer_id;
    public $sale_date;
    public $rows = [];
    public $total = 0;
    public $description;

    public function mount()
    {
        $this->customers = Customer::all();
        $this->items = Item::all();
        $this->sale_date = date('Y-m-d');
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

            // Hitung subtotal per item
            $subtotal = ($qty * $price) * (1 - ($discount / 100));
            $this->rows[$index]['subtotal'] = max($subtotal, 0);
        }

        $this->calculateTotal();

        $this->dispatch('success', message: 'Totals refreshed successfully!');
    }

    private function calculateTotal()
    {
        $this->total = collect($this->rows)->sum('subtotal');
    }

    public function refreshUnitPrice($index)
    {
        $itemId = $this->rows[$index]['item_id'] ?? null;

        if ($itemId) {
            $item = Item::find($itemId);
            $this->rows[$index]['unit_price'] = $item->selling_price ?? 0;
        }

        $this->refreshTotal();
    }

    public function save()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'rows.*.item_id' => 'required|exists:items,id',
            'rows.*.discount' => 'nullable|numeric|min:0',
            'rows.*.quantity' => 'required|numeric|min:1',
            'rows.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Cek stok sebelum transaksi
        foreach ($this->rows as $row) {
            $item = Item::find($row['item_id']);

            if ($item->stock < $row['quantity']) {
                // Kirim message error ke frontend
                $this->dispatch('error', message: "Stok {$item->name} hanya {$item->stock}, tidak cukup untuk quantity {$row['quantity']}.");
                return; // hentikan eksekusi
            }
        }


        DB::transaction(function () {
            $sale = Sale::create([
                'user_id' => Auth::id(), 
                'sale_code' => 'SAL-' . now()->format('YmdHis'),
                'customer_id' => $this->customer_id,
                'sale_date' => $this->sale_date,
                'description' => $this->description,
                'total_amount' => $this->total,
                'is_posted' => false, 
            ]);

            foreach ($this->rows as $row) {

                $item = Item::find($row['item_id']);
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'item_id' => $row['item_id'],
                    'item_name' => $item->item_name,
                    'item_code' => $item->item_code,
                    'quantity' => $row['quantity'],
                    'discount' => $row['discount'] ?? 0,
                    'unit_price' => $row['unit_price'],
                    'subtotal' => $row['subtotal'],
                ]);

                
            }
        });

        $this->resetExcept('customers', 'items');
        $this->sale_date = date('Y-m-d');
        $this->addRow();

        $this->dispatch('success', message: 'Sale saved successfully!');
    }

    public function render()
    {
        return view('livewire.superadmin.transaction.create-sale');
    }
}
