<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class Users extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;

    // Propiedades del formulario
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = '';
    public $estado = true; // Valor por defecto en activo

    #[Layout('components.layouts.app')]
    public function render()
    {
        $users = User::with('roles')
            ->where(function ($query) {
                $query->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhere('email', 'ilike', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.users', compact('users', 'roles'));
    }

    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->estado = true;
        $this->resetValidation();
    }

    public function saveUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'password' => $this->userId ? 'nullable|min:6' : 'required|min:6',
            'role' => 'required|exists:roles,name',
            'estado' => 'boolean',
        ],[
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'El correo ya existe.',
            'password.required' => 'La contraseña es obligatoria.',
            'role.required' => 'Elija un rol de usuario.',
        ]);

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->name = $this->name;
            $user->email = $this->email;
            $user->estado = $this->estado;
            if ($this->password) {
                $user->password = Hash::make($this->password);
            }
            $user->save();
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'estado' => $this->estado,
            ]);
        }

        // Sincronizar el Rol del usuario con Spatie
        $user->syncRoles([$this->role]);

        $this->dispatch('swal:success', title: '¡Usuario Guardado!', text: 'Los datos y el rol han sido asignados correctamente.');
        $this->closeModal();
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->estado = (bool) $user->estado;
        $this->role = $user->roles->first()?->name ?? '';
        $this->showModal = true;
    }

    // Cambiar estado (Activo/Inactivo) directamente desde la tabla de usuarios
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Evitar que el administrador se desactive a sí mismo
        if ($user->id === auth()->id()) {
            $this->dispatch('swal:error', title: 'Acción restringida', text: 'No puedes desactivar tu propia cuenta.');
            return;
        }

        $user->estado = !$user->estado;
        $user->save();

        $this->dispatch('swal:success', title: 'Estado Actualizado', text: 'El estado del usuario ha sido actualizado.');
    }
}