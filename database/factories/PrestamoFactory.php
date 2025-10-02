<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Prestamo;
use App\Models\Libro;
use App\Models\User;

class PrestamoFactory extends Factory
{
    protected $model = Prestamo::class;

    public function definition()
    {
        return [
            'libro_id' => Libro::factory(),
            'user_id' => User::factory(),
            'fecha_prestamo' => $this->faker->date(),
            'fecha_devolucion' => $this->faker->date(),
            'fecha_devuelto' => null,
        ];
    }
}
