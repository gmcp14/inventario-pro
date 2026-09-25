<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use App\Models\InventoryMovement;
use Livewire\Component;

class Pos extends Component
{
    // Búsqueda de Productos y Filtros
    public $searchProduct = '';
    public $selectedCategory = 'all';

    // Carrito de Compras
    public $cart = [];

    // Selección de Cliente
    public $searchCustomer = '';
    public $selectedCustomerId = null;
    public $selectedCustomerName = '';

    // Pagos y Totales
    public $paymentMethod = 'cash';
    public $paidAmount = 0;
    public $taxRate = 0; // % IVA / Impuesto (ej. 16 para 16%)
    public $discount = 0;

    #[Layout('components.layouts.app')]
    public function mount()
    {
        // Asignar por defecto el Cliente General / Público Contado (Documento '00000000')
        $defaultCustomer = Customer::where('document_number', '00000000')->first();
        if ($defaultCustomer) {
            $this->selectedCustomerId = $defaultCustomer->id;
            $this->selectedCustomerName = $defaultCustomer->name;
        }
    }

    public function render()
    {
        $categories = Category::all();

        // Consulta de productos disponibles en el POS
        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->when($this->searchProduct, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'ilike', '%' . $this->searchProduct . '%')
                          ->orWhere('code', 'ilike', '%' . $this->searchProduct . '%');
                });
            })
            ->when($this->selectedCategory !== 'all', function ($q) {
                $q->where('category_id', $this->selectedCategory);
            })
            ->orderBy('name', 'asc')
            ->take(12)
            ->get();

        // Búsqueda rápida de clientes para la modal/drop-down
        $customers = Customer::where('is_active', true)
            ->when($this->searchCustomer, function ($q) {
                $q->where('name', 'ilike', '%' . $this->searchCustomer . '%')
                  ->orWhere('document_number', 'ilike', '%' . $this->searchCustomer . '%');
            })
            ->take(5)
            ->get();

        // Totales del carrito
        $subtotal = collect($this->cart)->sum('subtotal');
        $taxAmount = ($subtotal * ($this->taxRate / 100));
        $grandTotal = max(0, ($subtotal + $taxAmount) - $this->discount);
        $changeAmount = max(0, floatval($this->paidAmount) - $grandTotal);

        return view('livewire.pos', compact(
            'products',
            'categories',
            'customers',
            'subtotal',
            'taxAmount',
            'grandTotal',
            'changeAmount'
        ));
    }

    // Agregar producto al carrito
    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            $this->dispatch('swal:error', title: 'Sin Stock', text: 'El producto seleccionado no cuenta con existencias disponibles.');
            return;
        }

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] + 1 > $product->stock) {
                $this->dispatch('swal:error', title: 'Límite Superado', text: 'No puedes agregar más unidades que las disponibles en stock.');
                return;
            }
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $this->cart[$productId]['price'];
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => (float) $product->sale_price,
                'quantity' => 1,
                'stock' => $product->stock,
                'subtotal' => (float) $product->sale_price,
            ];
        }
    }

    // Actualizar cantidad desde el carrito
    public function updateQuantity($productId, $quantity)
    {
        $quantity = (int) $quantity;

        if (!isset($this->cart[$productId])) return;

        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        if ($quantity > $this->cart[$productId]['stock']) {
            $this->dispatch('swal:error', title: 'Límite de Stock', text: "Solo hay {$this->cart[$productId]['stock']} unidades disponibles.");
            $this->cart[$productId]['quantity'] = $this->cart[$productId]['stock'];
        } else {
            $this->cart[$productId]['quantity'] = $quantity;
        }

        $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $this->cart[$productId]['price'];
    }

    // Remover del carrito
    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
    }

    // Vaciar carrito
    public function clearCart()
    {
        $this->cart = [];
        $this->paidAmount = 0;
    }

    // Seleccionar cliente
    public function selectCustomer($id, $name)
    {
        $this->selectedCustomerId = $id;
        $this->selectedCustomerName = $name;
        $this->searchCustomer = '';
    }

    // Procesar y Guardar Venta
   public function processSale()
{
    if (empty($this->cart)) {
        $this->dispatch('swal:error', title: 'Carrito Vacío', text: 'Agrega al menos un producto para procesar la venta.');
        return;
    }

    if (!$this->selectedCustomerId) {
        $this->dispatch('swal:error', title: 'Cliente Requerido', text: 'Por favor selecciona un cliente.');
        return;
    }

    $subtotal = collect($this->cart)->sum('subtotal');
    $taxAmount = ($subtotal * ($this->taxRate / 100));
    $grandTotal = max(0, ($subtotal + $taxAmount) - $this->discount);

    if ($this->paymentMethod === 'cash' && floatval($this->paidAmount) < $grandTotal) {
        $this->dispatch('swal:error', title: 'Monto Insuficiente', text: 'El monto pagado no cubre el total de la venta.');
        return;
    }

    try {
        DB::transaction(function () use ($subtotal, $taxAmount, $grandTotal) {
            // Generar número de comprobante único (Ej. V-20260921-0001)
            $voucherNumber = 'V-' . date('Ymd') . '-' . str_pad(Sale::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'voucher_number' => $voucherNumber,
                'customer_id' => $this->selectedCustomerId,
                'user_id' => auth()->id() ?? 1,
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'discount' => floatval($this->discount),
                'total' => $grandTotal,
                'payment_method' => $this->paymentMethod,
                'paid_amount' => floatval($this->paidAmount) > 0 ? floatval($this->paidAmount) : $grandTotal,
                'change_amount' => max(0, floatval($this->paidAmount) - $grandTotal),
                'status' => 'completed',
            ]);

            foreach ($this->cart as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // 1. Obtener el producto y capturar el stock previo
                $product = Product::find($item['id']);

                if ($product) {
                    $previousStock = $product->stock; // Stock antes de restar

                    // 2. Descontar el stock
                    $product->decrement('stock', $item['quantity']);

                    // 3. REGISTRO EN EL KÁRDEX
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id() ?? 1,
                        'type' => 'out',
                        'quantity' => -$item['quantity'],
                        'previous_stock' => $previousStock,
                        'new_stock' => $previousStock - $item['quantity'],
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'description' => "Venta POS Comprobante #{$sale->voucher_number}",
                    ]);
                }
            }

            Log::info('Venta registrada con éxito e inscrita en Kárdex', ['sale_id' => $sale->id, 'total' => $grandTotal]);
        });

        $this->dispatch('swal:success', title: '¡Venta Realizada!', text: 'La venta ha sido procesada y registrada en el Kárdex.');
        $this->clearCart();

    } catch (\Throwable $e) {
        Log::error('Error al procesar la venta', ['error' => $e->getMessage()]);
        $this->dispatch('swal:error', title: 'Error', text: 'No se pudo completar la transacción.');
    }
}
}