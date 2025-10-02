<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Libro;
use App\Models\Autor;
use App\Models\Genero;
use App\Models\Prestamo;
use App\Models\User;

class LibroTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function puede_crear_un_libro()
    {
        $autor = Autor::factory()->create();
        $genero = Genero::factory()->create();

        $response = $this->postJson('/api/libros', [
            'titulo' => 'Libro de prueba',
            'autor_id' => $autor->id,
            'genero_id' => $genero->id,
            'copias_totales' => 5
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'titulo' => 'Libro de prueba',
                'copias_totales' => 5,
                'copias_disponibles' => 5
            ]);

        $this->assertDatabaseHas('libros', [
            'titulo' => 'Libro de prueba',
            'copias_totales' => 5
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function no_puede_borrar_libro_con_prestamos_activos()
    {
        $autor = Autor::factory()->create();
        $genero = Genero::factory()->create();
        $libro = Libro::factory()->create([
            'copias_totales' => 3,
            'copias_disponibles' => 2,
            'autor_id' => $autor->id,
            'genero_id' => $genero->id
        ]);

        $user = User::factory()->create();

        // Crear un préstamo activo
        Prestamo::create([
            'libro_id' => $libro->id,
            'user_id' => $user->id,
            'fecha_prestamo' => now()->format('Y-m-d'),
            'fecha_devolucion' => now()->addDays(7)->format('Y-m-d'),
            'estado' => 'prestado'
        ]);

        $response = $this->deleteJson("/api/libros/{$libro->id}");

        $response->assertStatus(400)
            ->assertJson([
                'message' => [
                    'No se puede eliminar el libro porque tiene préstamos activos.'
                ]
            ]);

        $this->assertDatabaseHas('libros', [
            'id' => $libro->id
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function puede_actualizar_libro_y_ajustar_copias_disponibles()
    {
        $autor = Autor::factory()->create();
        $genero = Genero::factory()->create();

        $libro = Libro::factory()->create([
            'titulo' => 'Original',
            'copias_totales' => 5,
            'copias_disponibles' => 3,
            'autor_id' => $autor->id,
            'genero_id' => $genero->id
        ]);

        $nuevoAutor = Autor::factory()->create();
        $nuevoGenero = Genero::factory()->create();

        $response = $this->putJson("/api/libros/{$libro->id}", [
            'titulo' => 'Modificado',
            'autor_id' => $nuevoAutor->id,
            'genero_id' => $nuevoGenero->id,
            'copias_totales' => 7
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'titulo' => 'Modificado',
                'copias_totales' => 7,
                'copias_disponibles' => 5 // 3 + (7-5)
            ]);

        $libro->refresh();
        $this->assertEquals(5, $libro->copias_disponibles);
    }
}
