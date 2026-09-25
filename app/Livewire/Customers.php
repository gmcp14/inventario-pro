<?php

namespace App\Livewire;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public $statusFilter = 'all';

    // Estado del Modal
    public $isOpen = false;
    public $customerId = null;

    // Campos del Formulario
    public $document_type = 'V';
    public $document_number = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $is_active = true;

    #[Layout('components.layouts.app')]

    public function create()
    {
        $this->resetForm();
        $this->isOpen = true;
    }
    public function save()
    {
        $this->validate([
            'document_type' => 'required|string|max:10',
            'document_number' => [
                'required',
                'string',
                Rule::unique('customers', 'document_number')->ignore($this->customerId),
            ],
            'name' => 'required|min:3',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'document_number.required' => 'El número de cédula/RIF/documento es obligatorio.',
            'document_number.unique' => 'Este número de documento ya está registrado.',
            'name.required' => 'El nombre del cliente es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
        ]);

        try {
            DB::transaction(function () {
                $customer = Customer::updateOrCreate(
                    ['id' => $this->customerId],
                    [
                        'document_type' => strtoupper($this->document_type),
                        'document_number' => trim($this->document_number),
                        'name' => trim($this->name),
                        'email' => $this->email ? trim($this->email) : null,
                        'phone' => $this->phone ? trim($this->phone) : null,
                        'addres' => $this->address ? trim($this->address) : null,
                        'is_active' => (bool) $this->is_active,
                    ]
                );

                Log::info('Cliente guardado correctamente', [
                    'customer_id' => $customer->id,
                    'user_id' => auth()->id(),
                    'action' => $this->customerId ? 'UPDATE' : 'CREATE',
                ]);
            });

            $title = $this->customerId ? '¡Cliente Actualizado!' : '¡Cliente Creado!';
            $message = $this->customerId ? 'La información del cliente fue actualizada.' : 'El cliente se registró con éxito.';

            $this->dispatch('swal:success', title: $title, text: $message);
            $this->closeModal();

        } catch (\Throwable $e) {
            Log::error('Error al guardar cliente', [
                'customer_id' => $this->customerId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('swal:error', title: 'Error', text: 'No se pudo guardar la información del cliente.');
        }
    }
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->document_type = $customer->document_type;
        $this->document_number = $customer->document_number;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->is_active = (bool) $customer->is_active;

        $this->isOpen = true;
    }
    public function delete($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            // Evitar eliminar al cliente por defecto de público contado
            if ($customer->document_number === '00000000') {
                $this->dispatch('swal:error', title: 'Acción No Permitida', text: 'No se puede eliminar el cliente público general.');
                return;
            }

            $customer::destroy($id);

            Log::info('Cliente eliminado', ['customer_id' => $id, 'user_id' => auth()->id()]);
            $this->dispatch('swal:success', title: '¡Eliminado!', text: 'El cliente ha sido eliminado.');

        } catch (\Throwable $e) {
            Log::error('Error al eliminar cliente', ['customer_id' => $id, 'error' => $e->getMessage()]);
            $this->dispatch('swal:error', title: 'Error', text: 'No se pudo eliminar el cliente.');
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }
public function toggleStatus($id)
{
    try {
        $customer = Customer::findOrFail($id);

        // Protección para no inactivar al cliente público contado por defecto
        if ($customer->document_number === '00000000' && $customer->is_active) {
            $this->dispatch('swal:error', title: 'Acción No Permitida', text: 'El cliente público contado debe permanecer activo.');
            return;
        }

        $customer->is_active = !$customer->is_active;
        $customer->save();

        Log::info('Estado de cliente actualizado', [
            'customer_id' => $id,
            'is_active' => $customer->is_active,
            'user_id' => auth()->id(),
        ]);

        $statusText = $customer->is_active ? 'activado' : 'inactivado';
        $this->dispatch('swal:success', title: '¡Estado Actualizado!', text: "El cliente ha sido {$statusText}.");

    } catch (\Throwable $e) {
        Log::error('Error al cambiar estado de cliente', ['customer_id' => $id, 'error' => $e->getMessage()]);
        $this->dispatch('swal:error', title: 'Error', text: 'No se pudo cambiar el estado del cliente.');
    }
}

    private function resetForm()
    {
        $this->customerId = null;
        $this->document_type = 'V';
        $this->document_number = '';
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->is_active = true;
        $this->resetValidation();
    }
    public function render()
    {
        // Métricas
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('is_active', true)->count();

        // Consulta filtrada compatible con PostgreSQL (ilike)
        $customers = Customer::when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhere('document_number', 'ilike', '%' . $this->search . '%')
                      ->orWhere('phone', 'ilike', '%' . $this->search . '%')
                      ->orWhere('email', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.customers', compact('customers', 'totalCustomers', 'activeCustomers'));
    }
}