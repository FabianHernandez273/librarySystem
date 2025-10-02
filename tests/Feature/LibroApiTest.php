<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Libro;
use App\Models\Autor;
use App\Models\Genero;

class LibroApiTest extends TestCase
{
    use RefreshDatabase; // Para reiniciar la DB en cada prueba
   #[\PHPUnit\Framework\Attributes\Test]
    public function test_crear_libro()
    {
        $autor = Autor::factory()->create();
        $genero = Genero::factory()->create();

        $response = $this->postJson('/api/libros', [
            'titulo' => 'Libro Test',
            'autor_id' => $autor->id,
            'genero_id' => $genero->id,
            'copias_totales' => 5
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['titulo' => 'Libro Test']);
    }

    public function test_actualizar_libro()
    {
        $libro = Libro::factory()->create(['titulo' => 'Original']);

        $response = $this->putJson("/api/libros/{$libro->id}", [
            'titulo' => 'Actualizado',
            'autor_id' => $libro->autor_id,
            'genero_id' => $libro->genero_id,
            'copias_totales' => 10
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['titulo' => 'Actualizado']);
    }
   #[\PHPUnit\Framework\Attributes\Test]
    public function test_eliminar_libro()
    {
        $libro = Libro::factory()->create();

        $response = $this->deleteJson("/api/libros/{$libro->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('libros', ['id' => $libro->id]);
    }
}
