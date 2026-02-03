<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('unidad', 20)->default('unidad')->after('precio');
            $table->integer('stock')->default(0)->after('unidad');
            $table->integer('stock_minimo')->default(0)->after('stock');
            $table->string('ubicacion')->nullable()->after('stock_minimo');
            $table->string('lote')->nullable()->after('ubicacion');
            $table->date('fecha_caducidad')->nullable()->after('lote');
        });
    }
    public function down(): void {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['unidad','stock','stock_minimo','ubicacion','lote','fecha_caducidad']);
        });
    }
};
