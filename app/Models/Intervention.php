<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    protected $fillable = [
        'equipment_id',
        'fecha_intervencion',
        'hora_intervencion',
        'duracion',
        'fecha_finalizacion',
        'detalle',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
