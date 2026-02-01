<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('precio', 10, 2);
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnUpdate()->restrictOnDelete();

            // Campos de inventario incluidos aquí para simplificar (puedes tenerlos en otra migración si ya la usas)
            $table->string('sku')->nullable();
            $table->string('unidad', 20)->default('unidad');
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->string('ubicacion')->nullable();
            $table->string('lote')->nullable();
            $table->date('fecha_caducidad')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('productos');
    }
};