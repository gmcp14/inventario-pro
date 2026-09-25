<?php

namespace App\Livewire;

use App\Models\Supplier;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
  use Illuminate\Validation\Rule; 

class SuppliersIndex extends Component
{
  
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $supplierId = null;

    // Campos del formulario
    public $document_type = 'RIF';
    public $document_number = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $contact_person = '';
    public $is_active = true;

    #[Layout('components.layouts.app')]
    public function render()
    {
        $suppliers = Supplier::where(function ($q) {
            $q->where('name', 'ilike', '%' . $this->search . '%')
              ->orWhere('document_number', 'ilike', '%' . $this->search . '%')
              ->orWhere('contact_person', 'ilike', '%' . $this->search . '%');
        })
        ->orderBy('name', 'asc')
        ->paginate(10);

        return view('livewire.suppliers-index', compact('suppliers'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->supplierId = null;
        $this->document_type = 'RIF';
        $this->document_number = '';
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->contact_person = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $supplier->id;
        $this->document_type = $supplier->document_type;
        $this->document_number = $supplier->document_number;
        $this->name = $supplier->name;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
        $this->contact_person = $supplier->contact_person;
        $this->is_active = $supplier->is_active;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
        'document_type' => 'required|string|max:10',
        'document_number' => [
            'required',
            'string',
            'max:20',
            Rule::unique('suppliers', 'document_number')->ignore($this->supplierId),
        ],
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:50',
        'address' => 'nullable|string|max:500',
        'contact_person' => 'nullable|string|max:255',
        ],[ 
            
        'document_number.required' => 'El Documento es obligatorio.',
        'name.required' => 'El nombre es obliagtorio.',
    
    ]);

          

        try {
            Supplier::updateOrCreate(
                ['id' => $this->supplierId],
                [
                    'document_type' => $this->document_type,
                    'document_number' => trim($this->document_number),
                    'name' => trim($this->name),
                    'email' => $this->email ? trim($this->email) : null,
                    'phone' => $this->phone ? trim($this->phone) : null,
                    'address' => $this->address ? trim($this->address) : null,
                    'contact_person' => $this->contact_person ? trim($this->contact_person) : null,
                    'is_active' => $this->is_active,
                ]
            );

            $message = $this->supplierId ? 'Proveedor actualizado con éxito.' : 'Proveedor registrado con éxito.';
            $this->dispatch('swal:success', title: '¡Éxito!', text: $message);
            $this->closeModal();

        } catch (\Throwable $e) {
            Log::error('Error al guardar proveedor', ['error' => $e->getMessage()]);
            $this->dispatch('swal:error', title: 'Error', text: 'Ocurrió un problema al procesar los datos.');
        }
    }
}