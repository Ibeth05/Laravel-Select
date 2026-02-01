<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre','sku','precio','unidad',
        'stock','stock_minimo','ubicacion','lote','fecha_caducidad',
        'categoria_id','proveedor_id'
    ];

    protected $casts = [
        'fecha_caducidad' => 'date',
        'precio' => 'decimal:2',
    ];
    
    public function movimientos()
    {
    return $this->hasMany(\App\Models\MovimientoInventario::class);
    }

    public function categoria() { return $this->belongsTo(Categoria::class, 'categoria_id'); }
    public function proveedor() { return $this->belongsTo(Proveedor::class, 'proveedor_id'); }
    public function getBajoStockAttribute(): bool
    {
        return $this->stock_minimo > 0 && $this->stock <= $this->stock_minimo;
    }
}