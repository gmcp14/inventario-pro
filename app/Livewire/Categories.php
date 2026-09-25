<?php

namespace App\Livewire;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Categories extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $isOpen= false;
    public $categoryId = null;


    public $name='';
    public $description = '';
    public $is_active = true;
    
    #[Layout('components.layouts.app')]
    public function render()
    {
        // Métricas rápidas
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();
        $inactiveCategories = Category::where('is_active', false)->count();
        $createdThisMonth = Category::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Consulta filtrada para la tabla
        $categories = Category::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%') // 'ilike' para PostgreSQL sin distinción de mayúsculas/minúsculas
                      ->orWhere('slug', 'ilike', '%' . $this->search . '%')
                      ->orWhere('description', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.categories', compact(
            'categories',
            'totalCategories',
            'activeCategories',
            'inactiveCategories',
            'createdThisMonth'
        ));
    }
    // public function render()
    // {
    //    $categories = Category::where('name', 'like', '%' . $this->search . '%')
    //         ->orderBy('id', 'desc')
    //         ->paginate(5);

    //     return view('livewire.categories', compact('categories'));
    // }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function create()
    {
        $this->resetForm();
        $this->isOpen= true;
    }
    public function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->description = '';
        $this->is_active = true;
        $this->resetValidation();
    }
    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }
   public function save()
{
    // Construimos la regla de forma explícita y segura para PostgreSQL
    $categoryId = $this->categoryId ?: 'NULL';

    $this->validate([
        'name' => 'required|min:3|unique:categories,name,' . $categoryId,
        'description' => 'nullable|max:255',
    ], [
        'name.required' => 'El nombre de la categoría es obligatorio.',
        'name.min' => 'El nombre debe tener al menos 3 caracteres.',
        'name.unique' => 'Esta categoría ya existe.',
    ]);

    try {
        DB::transaction(function () {
            $category = Category::updateOrCreate(
                ['id' => $this->categoryId],
                [
                    'name' => trim($this->name),
                    'slug' => Str::slug($this->name),
                    'description' => trim($this->description),
                    'is_active' => (bool) $this->is_active,
                ]
            );

            Log::info('Categoría guardada correctamente', [
                'category_id' => $category->id,
                'user_id' => auth()->id(),
                'action' => $this->categoryId ? 'UPDATE' : 'CREATE',
            ]);
        });

        $title = $this->categoryId ? '¡Categoría Actualizada!' : '¡Categoría Creada!';
        $message = $this->categoryId ? 'La categoría se actualizó correctamente.' : 'La categoría fue registrada con éxito.';

        $this->dispatch('swal:success', title: $title, text: $message);

        $this->closeModal();

    } catch (\Throwable $e) {
        Log::error('Error al guardar la categoría', [
            'category_id' => $this->categoryId,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        $this->dispatch('swal:error', title: 'Error', text: 'Ocurrió un problema al guardar la categoría.');
    }
}
    public function edit($id){
        $category= Category::findOrFail($id);
        $this->categoryId= $category->id;
        $this->name = $category->name;
        $this->description =$category->description;
        $this->is_active = (bool) $category->is_active;
        $this->isOpen = true;
    }
   public function delete($id)
{
    try {
        Category::findOrFail($id)->delete();

        Log::info('Categoría eliminada', [
            'category_id' => $id, 
            'user_id' => auth()->id()
        ]);

        // Dispara la alerta de éxito al terminar de eliminar
        $this->dispatch('swal:success', title: '¡Eliminado!', text: 'La categoría ha sido eliminada correctamente.');

    } catch (\Throwable $e) {
        Log::error('Error al eliminar categoría', [
            'category_id' => $id, 
            'error' => $e->getMessage()
        ]);

        $this->dispatch('swal:error', title: 'Error', text: 'No se pudo eliminar la categoría.');
    }
}

}
