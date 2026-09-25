<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::firstOrCreate(
            ['document_number' => '00000000'],
            [
                'document_type' => 'V',
                'name' => 'Público Contado / Cliente General',
                'email' => null,
                'phone' => null,
                'addres' => 'S/D',
                'is_active' => true,
            ]
        );
    }
}