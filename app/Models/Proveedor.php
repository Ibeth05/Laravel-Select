<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    protected $fillable = ['nombre', 'email', 'telefono'];

    public function productos() { return $this->hasMany(Producto::class, 'proveedor_id'); }
}