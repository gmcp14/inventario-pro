<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'sale_type')) {
                $table->string('sale_type')->default('cash'); // cash (Contado), credit (Crédito)
            }
            if (!Schema::hasColumn('sales', 'due_date')) {
                $table->date('due_date')->nullable(); // Fecha de vencimiento para crédito
            }
            if (!Schema::hasColumn('sales', 'pending_amount')) {
                $table->decimal('pending_amount', 12, 2)->default(0); // Saldo pendiente
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['sale_type', 'due_date', 'pending_amount']);
        });
    }
};