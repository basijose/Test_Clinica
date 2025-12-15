<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = ['rubro_id', 'location_id', 'nombre', 'serial', 'estado', 'attributes'];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function rubro()
    {
        return $this->belongsTo(Rubro::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
