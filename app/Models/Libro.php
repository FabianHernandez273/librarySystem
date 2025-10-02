<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Libro extends Model
{
        use HasFactory;
    // Campos que se pueden asignar en create() o update()
    protected $fillable = [
        'titulo',
        'autor_id',
        'genero_id',
        'isbn',
        'copias_totales',
        'copias_disponibles',
        'descripcion',
    ];

    // Relaciones
    public function autor()
    {
        return $this->belongsTo(Autor::class);
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class);
    }

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }

    
}
