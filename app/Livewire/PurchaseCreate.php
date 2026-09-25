<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PurchaseCreate extends Component
{
    // Datos de la Cabecera de la Factura
    public $supplier_id = '';
    public $invoice_number = '';
    public $payment_method = 'transfer';
    public $notes = '';
   

    // Buscador de Productos
    public $searchProduct = '';
    
    // Carrito / Items de la Compra
    public $items = [];

    #[Layout('components.layouts.app')]
    public function render()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name', 'asc')->get();

        $products = Product::where('is_active', true)
            ->when($this->searchProduct, function ($q) {
                $q->where('name', 'ilike', '%' . $this->searchProduct . '%')
                  ->orWhere('code', 'ilike', '%' . $this->searchProduct . '%');
            })
            ->take(6)
            ->get();

        // Cálculo de Totales
        $subtotal = collect($this->items)->sum('subtotal');
        $total = $subtotal;

        return view('livewire.purchase-create', compact('suppliers', 'products', 'subtotal', 'total'));
    }

    // Agregar producto al listado de la compra
    public function addProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) return;

        if (isset($this->items[$productId])) {
            $this->items[$productId]['quantity']++;
            $this->items[$productId]['subtotal'] = $this->items[$productId]['quantity'] * $this->items[$productId]['cost_price'];
        } else {
            $this->items[$productId] = [
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'quantity' => 1,
                'cost_price' => (float) $product->cost_price,
                'subtotal' => (float) $product->cost_price,
            ];
        }

        $this->searchProduct = '';
    }

    // Actualizar cantidad o precio de costo
    public function updateItem($productId, $field, $value)
    {
        if (!isset($this->items[$productId])) return;

        $val = floatval($value);

        if ($field === 'quantity') {
            $this->items[$productId]['quantity'] = max(1, (int) $val);
        } elseif ($field === 'cost_price') {
            $this->items[$productId]['cost_price'] = max(0, $val);
        }

        $this->items[$productId]['subtotal'] = $this->items[$productId]['quantity'] * $this->items[$productId]['cost_price'];
    }

    // Quitar producto
    public function removeItem($productId)
    {
        unset($this->items[$productId]);
    }

    // Guardar Factura de Compra
    public function savePurchase()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|max:100',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
        ], [
            'supplier_id.required' => 'Debes seleccionar un proveedor.',
            'invoice_number.required' => 'Ingresa el número de factura de compra.',
            'items.min' => 'Agrega al menos un producto a la compra.',
        ]);

        $subtotal = collect($this->items)->sum('subtotal');
        $total = $subtotal;

        try {
            DB::transaction(function () use ($subtotal, $total) {
                // 1. Crear Registro de Compra
                $purchase = Purchase::create([
                    'invoice_number' => trim($this->invoice_number),
                    'supplier_id' => $this->supplier_id,
                    'user_id' => auth()->id() ?? 1,
                    'subtotal' => $subtotal,
                    'tax' => 0,
                    'total' => $total,
                    'payment_method' => $this->payment_method,
                    'status' => 'completed',
                    'notes' => $this->notes ? trim($this->notes) : null,
                ]);

                // 2. Registrar Detalles e Incrementar Inventario
                foreach ($this->items as $item) {
                    PurchaseDetail::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'cost_price' => $item['cost_price'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Aumentar stock del producto y actualizar su precio de costo
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $previousStock = $product->stock;
                        $product->increment('stock', $item['quantity']);
                        $product->cost_price = $item['cost_price'];
                        $product->save();

                        InventoryMovement::create([
                        'product_id' => $item['product_id'],
                        'user_id' => auth()->id() ?? 1,
                        'type' => 'in',
                        'quantity' => $item['quantity'],
                        'previous_stock' => $previousStock,
                        'new_stock' => $previousStock + $item['quantity'],
                        'reference_type' => Purchase::class,
                        'reference_id' => $purchase->id,
                        'description' => "Compra Factura #{$purchase->invoice_number}",
                    ]);
                    }
                }

                Log::info('Compra registrada con éxito', ['purchase_id' => $purchase->id, 'total' => $total]);
            });

            $this->dispatch('swal:success', title: '¡Compra Registrada!', text: 'El stock y el costo de los productos han sido actualizados.');
            return redirect()->route('purchases');

        } catch (\Throwable $e) {
            //dd($e->getMessage());
            Log::error('Error al registrar compra', ['error' => $e->getMessage()]);
            $this->dispatch('swal:error', title: 'Error', text: 'No se pudo registrar la factura de compra.');
        }
    }
}