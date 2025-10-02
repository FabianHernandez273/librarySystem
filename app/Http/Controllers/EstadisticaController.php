<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EstadisticaController extends Controller
{
    public function index()
    {
        // Libros más prestados (Top 5)
        $librosMasPrestados = Prestamo::select('libro_id', DB::raw('count(*) as total'))
            ->groupBy('libro_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->load('libro'); // cargar relación

        $librosMasPrestados = $librosMasPrestados->map(function ($item) {
            return [
                'titulo' => $item->libro->titulo,
                'autor' => $item->libro->autor->nombre ?? 'Desconocido',
                'total_prestamos' => $item->total
            ];
        });

        // Total de préstamos activos
        $prestamosActivos = Prestamo::where('estado', 'prestado')->count();

        // Total de préstamos vencidos
        $prestamosVencidos = Prestamo::where('estado', 'vencido')->count();

        // Usuarios más activos (Top 5)
        $usuariosMasActivos = Prestamo::select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->load('user');

        $usuariosMasActivos = $usuariosMasActivos->map(function ($item) {
            return [
                'usuario' => $item->user->name ?? 'Desconocido',
                'total_prestamos' => $item->total
            ];
        });

        // Libros disponibles por género
        $librosDisponiblesPorGenero = Libro::select('genero_id', DB::raw('sum(copias_disponibles) as disponibles'))
            ->groupBy('genero_id')
            ->get()
            ->load('genero');

        $librosDisponiblesPorGenero = $librosDisponiblesPorGenero->map(function ($item) {
            return [
                'genero' => $item->genero->nombre ?? 'Desconocido',
                'copias_disponibles' => $item->disponibles
            ];
        });

        // Estadísticas finales
        return response()->json([
            'libros_mas_prestados' => $librosMasPrestados,
            'prestamos_activos' => $prestamosActivos,
            'prestamos_vencidos' => $prestamosVencidos,
            'usuarios_mas_activos' => $usuariosMasActivos,
            'libros_disponibles_por_genero' => $librosDisponiblesPorGenero
        ]);
    }
}
