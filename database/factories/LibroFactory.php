<?php

namespace Database\Factories;

use App\Models\Libro;
use App\Models\Autor;
use App\Models\Genero;

use Illuminate\Database\Eloquent\Factories\Factory;

class LibroFactory extends Factory
{
    protected $model = Libro::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->sentence(3),
            'autor_id' => Autor::factory(), 
            'genero_id' => Genero::factory(),         
            'isbn' => $this->faker->unique()->isbn13,
            'copias_totales' => $this->faker->numberBetween(1, 10),
            'copias_disponibles' => $this->faker->numberBetween(0, 10),
            'descripcion' => $this->faker->paragraph,
        ];
    }
}
