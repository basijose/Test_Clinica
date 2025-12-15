<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RubroField extends Model
{
    use HasFactory;

    protected $fillable = ['rubro_id', 'nombre', 'label', 'tipo', 'opciones', 'requerido'];

    protected $casts = [
        'opciones' => 'array',
        'requerido' => 'boolean',
    ];

    public function rubro()
    {
        return $this->belongsTo(Rubro::class);
    }
}
