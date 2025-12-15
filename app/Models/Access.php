<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'destino', 'tipo', 'descripcion', 'icono', 'orden', 'estado'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function parent()
    {
        return $this->belongsTo(Access::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Access::class, 'parent_id')->orderBy('orden');
    }
}
