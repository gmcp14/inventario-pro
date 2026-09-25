<?php

namespace App\Livewire;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryKardex extends Component
{
    use WithPagination;

    // Filtros
    public $selectedProductId = '';
    public $movementType = 'all';
    public $dateFrom = '';
    public $dateTo = '';

    // Modal de Ajuste Manual
    public $showAdjustmentModal = false;
    public $adj_product_id = '';
    public $adj_type = 'in'; // 'in' o 'out'
    public $adj_quantity = 1;
    public $adj_description = '';

    #[Layout('components.layouts.app')]
    public function render()
    {
        $products = Product::where('is_active', true)->orderBy('name', 'asc')->get();

        $movements = InventoryMovement::with(['product', 'user'])
            ->when($this->selectedProductId, function ($q) {
                $q->where('product_id', $this->selectedProductId);
            })
            ->when($this->movementType !== 'all', function ($q) {
                $q->where('type', $this->movementType);
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Información del producto seleccionado (si aplica)
        $selectedProduct = $this->selectedProductId ? Product::find($this->selectedProductId) : null;

        return view('livewire.inventory-kardex', compact('products', 'movements', 'selectedProduct'));
    }

    public function updatingSelectedProductId()
    {
        $this->resetPage();
    }

    public function openAdjustmentModal()
    {
        $this->resetAdjustmentFields();
        $this->showAdjustmentModal = true;
    }

    public function closeAdjustmentModal()
    {
        $this->showAdjustmentModal = false;
        $this->resetAdjustmentFields();
    }

    private function resetAdjustmentFields()
    {
        $this->adj_product_id = '';
        $this->adj_type = 'in';
        $this->adj_quantity = 1;
        $this->adj_description = '';
        $this->resetValidation();
    }

    // Registrar Ajuste Manual de Inventario
    public function saveAdjustment()
    {
        $this->validate([
            'adj_product_id' => 'required|exists:products,id',
            'adj_type' => 'required|in:in,out',
            'adj_quantity' => 'required|integer|min:1',
            'adj_description' => 'required|string|max:255',
        ], [
            'adj_product_id.required' => 'Selecciona un producto.',
            'adj_quantity.min' => 'La cantidad debe ser al menos 1.',
            'adj_description.required' => 'Indica el motivo del ajuste.',
        ]);

        try {
            DB::transaction(function () {
                $product = Product::findOrFail($this->adj_product_id);
                $previousStock = $product->stock;
                $qty = (int) $this->adj_quantity;

                if ($this->adj_type === 'out' && $qty > $previousStock) {
                    $this->dispatch('swal:error', title: 'Stock Insuficiente', text: 'No puedes realizar una salida mayor al stock actual.');
                    return;
                }

                $newStock = $this->adj_type === 'in' ? ($previousStock + $qty) : ($previousStock - $qty);

                // Actualizar stock del producto
                $product->stock = $newStock;
                $product->save();

                // Registrar en el Kárdex
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id() ?? 1,
                    'type' => 'adjustment',
                    'quantity' => $this->adj_type === 'in' ? $qty : -$qty,
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'description' => 'Ajuste Manual (' . ($this->adj_type === 'in' ? 'Entrada' : 'Salida') . '): ' . trim($this->adj_description),
                ]);

                Log::info('Ajuste de inventario realizado', ['product_id' => $product->id, 'new_stock' => $newStock]);
            });

            $this->dispatch('swal:success', title: '¡Ajuste Registrado!', text: 'El stock ha sido actualizado e inscrito en el Kárdex.');
            $this->closeAdjustmentModal();

        } catch (\Throwable $e) {
            Log::error('Error al realizar ajuste de inventario', ['error' => $e->getMessage()]);
            $this->dispatch('swal:error', title: 'Error', text: 'No se pudo procesar el ajuste.');
        }
    }
}