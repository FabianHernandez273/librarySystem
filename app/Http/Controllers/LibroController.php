<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Libro;
use App\Services\LibroService;

class LibroController extends Controller
{
    protected $libroService;

    public function __construct(LibroService $libroService)
    {
        $this->libroService = $libroService;
    }

    public function index()
    {
        return Libro::with(['autor', 'genero'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required',
            'autor_id' => 'required|exists:autores,id',
            'genero_id' => 'required|exists:generos,id',
            'isbn' => 'nullable|unique:libros',
            'copias_totales' => 'required|integer|min:1',
        ]);

        return $this->libroService->crear($data);
    }

    public function show(Libro $libro)
    {
        return $libro->load(['autor', 'genero']);
    }

    public function update(Request $request, Libro $libro)
    {
        $data = $request->validate([
            'titulo' => 'required',
            'autor_id' => 'required|exists:autores,id',
            'genero_id' => 'required|exists:generos,id',
            'isbn' => 'nullable|unique:libros,isbn,' . $libro->id,
            'copias_totales' => 'required|integer|min:1',
        ]);

        return $this->libroService->actualizar($libro, $data);
    }

    public function destroy(Libro $libro)
    {
        try {
            $this->libroService->eliminar($libro);
            return response()->noContent();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->errors()['message']], 400);
        }
    }
}
