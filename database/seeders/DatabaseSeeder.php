<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
   public function run(): void
    {
       app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

       $adminRole = Role::firstOrCreate([
            'name' => 'Administrador',
            'guard_name' => 'web'
        ]);

        // (Opcional) Crear otros roles iniciales
        Role::firstOrCreate(['name' => 'Cajero', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Almacenista', 'guard_name' => 'web']);

      $user = User::create([
    'name' => 'Administrador',
    'email' => 'admin@inventariopro.com',
    'password' => Hash::make('admin123'),
]);
        $user->assignRole($adminRole);
    }
}
