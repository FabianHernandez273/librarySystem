<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Libro;

class PrestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Prestamo::with(['libro', 'user'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'libro_id' => 'required|exists:libros,id',
            'user_id' => 'required|exists:users,id',
            'fecha_prestamo' => 'required|date',
            'fecha_devolucion' => 'required|date|after_or_equal:fecha_prestamo',
        ]);

        // Actualizar disponibilidad del libro
        $libro = Libro::findOrFail($data['libro_id']);
        if ($libro->copias_disponibles < 1) {
            return response()->json(['message' => 'No hay copias disponibles'], 400);
        }
        $libro->copias_disponibles -= 1;
        $libro->save();

        $prestamo = Prestamo::create($data);

        return response()->json($prestamo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Prestamo $prestamo)
    {
        $data = $request->validate([
            'fecha_devuelto' => 'nullable|date',
            'estado' => 'nullable|in:prestado,devuelto,vencido',
        ]);

        $prestamo->update($data);

        // Si se devuelve, actualizar disponibilidad del libro
        if (isset($data['fecha_devuelto']) && $prestamo->estado !== 'devuelto') {
            $prestamo->libro->copias_disponibles += 1;
            $prestamo->libro->save();
            $prestamo->estado = 'devuelto';
            $prestamo->save();
        }

        return response()->json($prestamo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prestamo $prestamo)
    {
        // Devolver libro si estaba prestado
        if ($prestamo->estado === 'prestado') {
            $prestamo->libro->copias_disponibles += 1;
            $prestamo->libro->save();
        }

        $prestamo->delete();

        return response()->noContent();
    }
}
