<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'estado'];

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}
