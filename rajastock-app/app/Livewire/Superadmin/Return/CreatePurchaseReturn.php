<?php

namespace App\Livewire\Superadmin\Return;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CreatePurchaseReturn extends Component
{
  use WithPagination;

  public $search = '';
  public $selectedPurchase = null;
  public $selectedItems = [];

  protected $queryString = ['search'];
  protected $paginationTheme = 'tailwind';

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function selectPurchase($id)
  {
    $this->selectedPurchase = Purchase::with('details.item', 'supplier')->find($id);
    $this->selectedItems = [];
  }

  public function toggleItem($detailId)
  {
    if (! $this->selectedPurchase) return;

    if (isset($this->selectedItems[$detailId])) {
      unset($this->selectedItems[$detailId]);
      return;
    }

    $detail = $this->selectedPurchase->details->firstWhere('id', $detailId);

    if (! $detail) return;

    // Ambil unit price dengan fallback jika nama kolom berbeda
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

  /**
   * Panggil method ini dari view ketika quantity berubah:
   * wire:change="updateQuantity({{ $detail->id }}, $event.target.value)"
   */
  public function updateQuantity($detailId, $quantity)
  {
    if (! isset($this->selectedItems[$detailId])) return;

    $quantity = (int) $quantity;
    if ($quantity < 1) $quantity = 1;

    $this->selectedItems[$detailId]['quantity_returned'] = $quantity;
    $this->selectedItems[$detailId]['sub_total'] = $this->selectedItems[$detailId]['unit_price'] * $quantity;
  }

  public function save()
  {
    if (! $this->selectedPurchase) {
      session()->flash('error', 'No purchase selected.');
      return redirect()->route('purchase-returns');
    }

    if (empty($this->selectedItems)) {
      session()->flash('error', 'No items selected for return.');
      return redirect()->route('purchase-returns');
    }

    // Hitung total dari sub_total yang sudah berisi unit_price * qty
    $total = collect($this->selectedItems)->sum('sub_total');

    DB::beginTransaction();
    try {
      $return = PurchaseReturn::create([
        'return_number' => 'RTN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
        'purchase_id' => $this->selectedPurchase->id,
        'return_date' => now(),
        'total_amount' => $total,
        'status' => 'pending'
      ]);

      foreach ($this->selectedItems as $detailId => $item) {
        PurchaseReturnDetail::create([
          'purchase_return_id' => $return->id,
          'purchase_detail_id' => $detailId,
          'quantity_returned' => $item['quantity_returned'],
          'sub_total' => $item['sub_total'],
          'condition' => $item['condition'],
          'reason' => $item['reason']
        ]);
      }

      DB::commit();

      session()->flash('success', 'Purchase return created successfully.');
      return redirect()->route('purchase-returns');
    } catch (\Throwable $e) {
      DB::rollBack();
      // untuk debugging sementara kamu bisa log atau session flash error
      session()->flash('error', 'Failed to create purchase return: ' . $e->getMessage());
      return redirect()->route('purchase-returns');
    }
  }
  public function recalculateSubTotal($detailId)
  {
    if (!isset($this->selectedItems[$detailId])) return;

    $qty = (int) $this->selectedItems[$detailId]['quantity_returned'];
    $price = (float) $this->selectedItems[$detailId]['unit_price'];

    $this->selectedItems[$detailId]['sub_total'] = $qty * $price;
  }
  public function render()
  {
    $purchases = Purchase::with('supplier')
      ->when($this->search, function ($query) {
        $query->where('purchase_code', 'like', "%{$this->search}%")
          ->orWhereHas('supplier', fn($q) => $q->where('supplier_name', 'like', "%{$this->search}%"));
      })
      ->latest()
      ->paginate(10);

    return view('livewire.superadmin.return.create-purchase-return', [
      'purchases' => $purchases,
    ]);
  }
}
