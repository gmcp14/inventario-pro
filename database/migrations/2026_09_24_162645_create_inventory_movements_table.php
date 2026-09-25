<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('type'); // 'in' (Entrada), 'out' (Salida), 'adjustment' (Ajuste)
            $table->integer('quantity'); // Cantidad operada
            $table->integer('previous_stock'); // Stock antes del movimiento
            $table->integer('new_stock'); // Stock resultante
            
            $table->string('reference_type')->nullable(); // Sale, Purchase, Adjustment
            $table->unsignedBigInteger('reference_id')->nullable(); // ID de la venta/compra
            $table->text('description'); // Ej: "Venta Factura #FAC-20260924-0001", "Ajuste manual"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};