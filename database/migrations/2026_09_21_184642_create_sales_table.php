<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            
            // Folio / Número de Factura o Ticket único
            $table->string('voucher_number')->unique(); 
            
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Cajero / Usuario
            
            // Resumen Financiero
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0); // Impuestos (Ej. IVA)
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            
            // Método de Pago y Estado
            $table->string('payment_method')->default('cash'); // cash, transfer, card, mobile_pay
            $table->decimal('paid_amount', 12, 2); // Monto entregado por el cliente
            $table->decimal('change_amount', 12, 2)->default(0); // Vuelto / Cambio
            $table->string('status')->default('completed'); // completed, cancelled
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};