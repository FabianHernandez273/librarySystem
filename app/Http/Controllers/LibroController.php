<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Libro;
class LibroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Libro::with(['autor', 'genero'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required',
            'autor_id' => 'nullable|exists:autores,id',
            'genero_id' => 'nullable|exists:generos,id',
            'isbn' => 'nullable|unique:libros',
            'copias_totales' => 'required|integer|min:1',
        ]);

        $data['copias_disponibles'] = $data['copias_totales'];

        return Libro::create($data);
    }

    /**
     * Display the specified resource.
     */
  public function show(Libro $libro)
    {
        return $libro->load(['autor', 'genero']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Libro $libro)
    {
        $data = $request->validate([
            'titulo' => 'required',
            'autor_id' => 'nullable|exists:autores,id',
            'genero_id' => 'nullable|exists:generos,id',
            'isbn' => 'nullable|unique:libros,isbn,' . $libro->id,
            'copias_totales' => 'required|integer|min:1',
        ]);

        $libro->update($data);

        return $libro;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Libro $libro)
    {
        $libro->delete();

        return response()->noContent();
    }
}
