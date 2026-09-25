<?php 

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    #[Rule('required', message: 'El campo correo electrónico es obligatorio.')]
    #[Rule('email', message: 'Ingresa un correo electrónico válido.')]
    public string $email = '';

    #[Rule('required', message: 'La contraseña es obligatoria.')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        try {
            // 1. Intentar autenticar validando email, password y que el estado sea activo
            if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'estado' => true], $this->remember)) {
                session()->regenerate();
                return redirect()->intended('/dashboard');
            }

            // 2. Si falla la autenticación, verificamos si el correo existe para saber si fue por cuenta inactiva
            $user = User::where('email', $this->email)->first();

            if ($user && !$user->estado) {
                $this->dispatch('swal:modal', 
                    title: 'Cuenta Inactiva',
                    text: 'Tu usuario se encuentra desactivado. Contacta al administrador del sistema.',
                    type: 'error'
                );
                return;
            }

            // 3. Credenciales incorrectas
            $this->dispatch('swal:modal', 
                title: 'Acceso Denegado',
                text: 'Las credenciales ingresadas son incorrectas.',
                type: 'error'
            );

        } catch (\Throwable $th) {
            Log::error('Error crítico durante el proceso de autenticación', [
                'email' => $this->email,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            $this->dispatch('swal:modal', 
                title: 'Error del Sistema',
                text: 'Ocurrió un inconveniente al procesar la solicitud. Por favor, reintenta más tarde.',
                type: 'error'
            );
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}