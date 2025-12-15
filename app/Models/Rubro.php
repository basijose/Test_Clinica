<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'nombre', 'descripcion', 'estado'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function fields()
    {
        return $this->hasMany(RubroField::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}
