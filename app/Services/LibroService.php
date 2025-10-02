<?php

namespace App\Services;

use App\Models\Libro;
use Illuminate\Validation\ValidationException;

class LibroService
{
    /**
     * Crear un libro nuevo
     */
    public function crear(array $data)
    {
        $data['copias_disponibles'] = $data['copias_totales'];
        return Libro::create($data);
    }

    /**
     * Actualizar un libro existente
     */
    public function actualizar(Libro $libro, array $data)
    {
        // Guardamos el valor original de copias_disponibles
        $copiasDisponiblesOriginal = $libro->copias_disponibles;

        // Ajustar copias disponibles solo si cambia el total
        if (isset($data['copias_totales']) && $data['copias_totales'] != $libro->copias_totales) {
            $diferencia = $data['copias_totales'] - $libro->copias_totales;
            $data['copias_disponibles'] = $copiasDisponiblesOriginal + $diferencia;

            // Evitar negativos
            if ($data['copias_disponibles'] < 0) {
                $data['copias_disponibles'] = 0;
            }
        }

        // Eliminamos cualquier intento de modificar copias_disponibles desde el request
        if (isset($data['copias_disponibles'])) {
            unset($data['copias_disponibles']);
        }

        $libro->update($data);

        // Recalcular copias_disponibles si cambió el total
        if (isset($diferencia)) {
            $libro->copias_disponibles = $copiasDisponiblesOriginal + $diferencia;
            if ($libro->copias_disponibles < 0) $libro->copias_disponibles = 0;
            $libro->save();
        }

        return $libro->load(['autor', 'genero']);
    }


    /**
     * Eliminar un libro
     */
    public function eliminar(Libro $libro)
    {
        $prestamosActivos = $libro->prestamos()
            ->whereIn('estado', ['prestado', 'vencido'])
            ->count();

        if ($prestamosActivos > 0) {
            throw ValidationException::withMessages([
                'message' => 'No se puede eliminar el libro porque tiene préstamos activos.'
            ]);
        }

        $libro->delete();
        return true;
    }
}
