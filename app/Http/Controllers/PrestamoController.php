<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Services\PrestamoService;

class PrestamoController extends Controller
{
    protected $prestamoService;

    public function __construct(PrestamoService $prestamoService)
    {
        $this->prestamoService = $prestamoService;
    }

    public function index()
    {
        return Prestamo::with(['libro', 'user'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'libro_id' => 'required|exists:libros,id',
            'user_id' => 'required|exists:users,id',
            'fecha_prestamo' => 'required|date',
            'fecha_devolucion' => 'required|date|after_or_equal:fecha_prestamo',
        ]);

        try {
            $prestamo = $this->prestamoService->crear($data);
            return response()->json($prestamo, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->errors()['message']], 400);
        }
    }

    public function update(Request $request, Prestamo $prestamo)
    {
        $data = $request->validate([
            'fecha_devuelto' => 'nullable|date',
            'estado' => 'nullable|in:prestado,devuelto,vencido',
        ]);

        try {
            if (isset($data['fecha_devuelto']) || ($data['estado'] ?? '') === 'devuelto') {
                $prestamo = $this->prestamoService->devolver($prestamo, $data['fecha_devuelto'] ?? null);
            } else {
                $prestamo->update($data);
            }
            return response()->json($prestamo);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->errors()['message']], 400);
        }
    }

    public function destroy(Prestamo $prestamo)
    {
        $this->prestamoService->eliminar($prestamo);
        return response()->noContent();
    }
}
