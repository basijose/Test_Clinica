<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    protected $fillable = ['destino', 'tipo', 'descripcion', 'icono', 'orden', 'estado'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
