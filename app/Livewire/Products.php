<?php 
namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;


class Products extends Component
{
    use WithPagination, WithFileUploads;
   public $search = '';
   public $categoryFilter = 'all';
    public $statusFilter = 'all';
    public $isOpen = false;
    public $productId = null;

    // Campos del Formulario
    public $category_id = '';
    public $code = '';
    public $name = '';
    public $description = '';
    public $cost_price = '';
    public $sale_price = '';
    public $stock = 0;
    public $min_stock = 5;
    public $is_active = true;
    public $image;
    public $existing_image = null;


     public function create(){
        $this->resetForm();
        $this->isOpen = true;
     }

    private function resetForm()
    {
        $this->productId = null;
        $this->category_id = '';
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->cost_price = '';
        $this->sale_price = '';
        $this->stock = 0;
        $this->min_stock = 5;
        $this->is_active = true;
        $this->image = null;
        $this->existing_image = null;
        $this->resetValidation();
    }
    public function save()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'code'=>[
                'required',
                'string',
                Rule::unique('products', 'code')->ignore($this->productId),
            ],
           'name' => 'required|min:3',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048', // Máx 2MB
            'description' => 'nullable|max:500',
        ], [
            'category_id.required' => 'Selecciona una categoría.',
            'code.required' => 'El código/SKU es obligatorio.',
            'code.unique' => 'Este código ya pertenece a otro producto.',
            'name.required' => 'El nombre del producto es obligatorio.',
            'sale_price.required' => 'Ingresa el precio de venta.',
        ]);
        try {
          DB::transaction(function () {
                $imagePath = $this->existing_image;

                if ($this->image) {
                    if ($this->existing_image && Storage::disk('public')->exists($this->existing_image)) {
                        Storage::disk('public')->delete($this->existing_image);
                    }
                    $imagePath = $this->image->store('products', 'public');
                }

                $product = Product::updateOrCreate(
                    ['id' => $this->productId],
                    [
                        'category_id' => $this->category_id,
                        'code' => trim($this->code),
                        'name' => trim($this->name),
                        'slug' => Str::slug($this->name),
                        'description' => trim($this->description),
                        'cost_price' => $this->cost_price,
                        'sale_price' => $this->sale_price,
                        'stock' => $this->stock,
                        'min_stock' => $this->min_stock,
                        'image_path' => $imagePath,
                        'is_active' => (bool) $this->is_active,
                    ]
                );

                Log::info('Producto guardado correctamente', [
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'action' => $this->productId ? 'UPDATE' : 'CREATE',
                ]);
            });

            $title = $this->productId ? '¡Producto Actualizado!' : '¡Producto Creado!';
            $message = $this->productId ? 'El producto se actualizó correctamente.' : 'El producto se registró con éxito.';

            $this->dispatch('swal:success', title: $title, text: $message);
            $this->closeModal();
        } catch (\Throwable $th) {
            Log::error('Error al guardar producto', [
                'product_id' => $this->productId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('swal:error', title: 'Error', text: 'No se pudo guardar el producto.');
        }
    }
     public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->category_id = $product->category_id;
        $this->code = $product->code;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->cost_price = $product->cost_price;
        $this->sale_price = $product->sale_price;
        $this->stock = $product->stock;
        $this->min_stock = $product->min_stock;
        $this->is_active = (bool) $product->is_active;
        $this->existing_image = $product->image_path;

        $this->isOpen = true;
    }

    public function delete($id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $product->delete();

            Log::info('Producto eliminado', ['product_id' => $id, 'user_id' => auth()->id()]);
            $this->dispatch('swal:success', title: '¡Eliminado!', text: 'El producto ha sido eliminado.');

        } catch (\Throwable $e) {
            Log::error('Error al eliminar producto', ['product_id' => $id, 'error' => $e->getMessage()]);
            $this->dispatch('swal:error', title: 'Error', text: 'No se pudo eliminar el producto.');
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }
    #[Layout('components.layouts.app')]
    public function render()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // Métricas rápidas
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();
        $activeProducts = Product::where('is_active', true)->count();

        // Consulta filtrada
        $products = Product::with('category')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhere('code', 'ilike', '%' . $this->search . '%')
                      ->orWhere('description', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter !== 'all', function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.products', compact('products', 'categories','totalProducts','lowStockProducts','activeProducts'
        ));
    }
    
}