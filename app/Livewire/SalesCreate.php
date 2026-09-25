<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use App\Models\InventoryMovement;
use Livewire\Component;

class SalesCreate extends Component
{
    // Datos de Cabecera
    public $customer_id = '';
    public $sale_type = 'cash'; // cash = Contado, credit = Crédito
    public $payment_method = 'transfer';
    public $due_date = '';
    public $notes = '';
    public $discount = 0;
    public $taxRate = 0; // % IVA o Impuesto

    // Buscador y Carrito
    public $searchProduct = '';
    public $items = [];

    #[Layout('components.layouts.app')]
    public function mount()
    {
        $this->due_date = now()->addDays(15)->format('Y-m-d');
    }

    public function render()
    {
        $customers = Customer::where('is_active', true)->orderBy('name', 'asc')->get();

        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->when($this->searchProduct, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'ilike', '%' . $this->searchProduct . '%')
                          ->orWhere('code', 'ilike', '%' . $this->searchProduct . '%');
                });
            })
            ->take(6)
            ->get();

        $subtotal = collect($this->items)->sum('subtotal');
        $taxAmount = ($subtotal * ($this->taxRate / 100));
        $grandTotal = max(0, ($subtotal + $taxAmount) - floatval($this->discount));

        return view('livewire.sales-create', compact('customers', 'products', 'subtotal', 'taxAmount', 'grandTotal'));
    }

    public function addProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            $this->dispatch('swal:error', title: 'Sin Stock', text: 'El producto no tiene stock disponible.');
            return;
        }

        if (isset($this->items[$productId])) {
            if ($this->items[$productId]['quantity'] + 1 > $product->stock) {
                $this->dispatch('swal:error', title: 'Stock Insuficiente', text: 'No puedes superar el stock disponible.');
                return;
            }
            $this->items[$productId]['quantity']++;
            $this->items[$productId]['subtotal'] = $this->items[$productId]['quantity'] * $this->items[$productId]['price'];
        } else {
            $this->items[$productId] = [
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'stock' => $product->stock,
                'price' => (float) $product->sale_price,
                'quantity' => 1,
                'subtotal' => (float) $product->sale_price,
            ];
        }

        $this->searchProduct = '';
    }

    public function updateItem($productId, $field, $value)
    {
        if (!isset($this->items[$productId])) return;

        $val = floatval($value);

        if ($field === 'quantity') {
            $qty = (int) $val;
            if ($qty > $this->items[$productId]['stock']) {
                $this->dispatch('swal:error', title: 'Límite Superado', text: "Stock disponible: {$this->items[$productId]['stock']} unidades.");
                $this->items[$productId]['quantity'] = $this->items[$productId]['stock'];
            } else {
                $this->items[$productId]['quantity'] = max(1, $qty);
            }
        } elseif ($field === 'price') {
            $this->items[$productId]['price'] = max(0, $val);
        }

        $this->items[$productId]['subtotal'] = $this->items[$productId]['quantity'] * $this->items[$productId]['price'];
    }

    public function removeItem($productId)
    {
        unset($this->items[$productId]);
    }

    public function saveSale()
{
    $this->validate([
        'customer_id' => 'required|exists:customers,id',
        'sale_type' => 'required|in:cash,credit',
        'payment_method' => 'required|string',
        'items' => 'required|array|min:1',
        'due_date' => 'required_if:sale_type,credit',
    ], [
        'customer_id.required' => 'Selecciona un cliente para emitir la factura.',
        'items.min' => 'Agrega al menos un producto a la venta.',
        'due_date.required_if' => 'Para ventas a crédito, debes indicar la fecha de vencimiento.',
    ]);

    $subtotal = collect($this->items)->sum('subtotal');
    $taxAmount = ($subtotal * ($this->taxRate / 100));
    $grandTotal = max(0, ($subtotal + $taxAmount) - floatval($this->discount));

    try {
        DB::transaction(function () use ($subtotal, $taxAmount, $grandTotal) {
            // Número de Comprobante / Factura
            $voucherNumber = 'FAC-' . date('Ymd') . '-' . str_pad(Sale::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT);

            $paidAmount = $this->sale_type === 'cash' ? $grandTotal : 0;
            $pendingAmount = $this->sale_type === 'credit' ? $grandTotal : 0;

            $sale = Sale::create([
                'voucher_number' => $voucherNumber,
                'customer_id' => $this->customer_id,
                'user_id' => auth()->id() ?? 1,
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'discount' => floatval($this->discount),
                'total' => $grandTotal,
                'payment_method' => $this->payment_method,
                'paid_amount' => $paidAmount,
                'change_amount' => 0,
                'sale_type' => $this->sale_type,
                'due_date' => $this->sale_type === 'credit' ? $this->due_date : null,
                'pending_amount' => $pendingAmount,
                'status' => 'completed',
                'notes' => $this->notes ? trim($this->notes) : null,
            ]);

            foreach ($this->items as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // 1. Obtener el producto y guardar el stock previo
                $product = Product::find($item['product_id']);

                if ($product) {
                    $previousStock = $product->stock; // Stock antes del descuento

                    // 2. Descontar el inventario
                    $product->decrement('stock', $item['quantity']);

                    // 3. REGISTRO EN EL KÁRDEX (Movimiento de Salida)
                    InventoryMovement::create([
                        'product_id' => $item['product_id'],
                        'user_id' => auth()->id() ?? 1,
                        'type' => 'out',
                        'quantity' => -$item['quantity'],
                        'previous_stock' => $previousStock,
                        'new_stock' => $previousStock - $item['quantity'],
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'description' => "Venta Factura #{$sale->voucher_number}",
                    ]);
                }
            }

            Log::info('Factura de venta emitida con éxito e inscrita en Kárdex', ['sale_id' => $sale->id, 'voucher' => $voucherNumber]);
        });

        $this->dispatch('swal:success', title: '¡Factura Registrada!', text: 'La venta ha sido procesada e inscrita en el Kárdex.');
        return redirect()->route('sales.index');

    } catch (\Throwable $e) {
        Log::error('Error al registrar venta formal', ['error' => $e->getMessage()]);
        $this->dispatch('swal:error', title: 'Error', text: 'No se pudo procesar la factura de venta.');
    }
}
}