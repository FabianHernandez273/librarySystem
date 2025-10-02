<?php

namespace App\Services;

use App\Models\Prestamo;
use App\Models\Libro;
use Illuminate\Validation\ValidationException;

class PrestamoService
{
    /**
     * Crear un préstamo
     */
    public function crear(array $data)
    {
        $libro = Libro::findOrFail($data['libro_id']);

        if ($libro->copias_disponibles < 1) {
            throw ValidationException::withMessages([
                'message' => 'No hay copias disponibles para este libro.'
            ]);
        }

        $libro->copias_disponibles -= 1;
        $libro->save();
        $data['estado'] = 'prestado';
        return Prestamo::create($data);
    }

    /**
     * Devolver un préstamo
     */
    public function devolver(Prestamo $prestamo, $fechaDevolucion = null)
    {
        if ($prestamo->estado === 'devuelto') {
            throw ValidationException::withMessages([
                'message' => 'El préstamo ya fue devuelto.'
            ]);
        }

        $prestamo->estado = 'devuelto';
        $prestamo->fecha_devuelto = $fechaDevolucion ?? now()->toDateString();
        $prestamo->save();

        $prestamo->libro->copias_disponibles += 1;
        $prestamo->libro->save();

        return $prestamo;
    }

    /**
     * Eliminar un préstamo
     */
    public function eliminar(Prestamo $prestamo)
    {
        // Si estaba prestado, devolver la copia
        if ($prestamo->estado === 'prestado') {
            $prestamo->libro->copias_disponibles += 1;
            $prestamo->libro->save();
        }

        $prestamo->delete();

        return true;
    }
}
