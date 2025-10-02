<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Prestamo;
use App\Models\Libro;
use App\Models\User;

class PrestamoApiTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function puede_crear_un_prestamo()
    {
        $libro = Libro::factory()->create(['copias_totales' => 3]);
        $usuario = User::factory()->create();

        $response = $this->postJson('/api/prestamos', [
            'libro_id' => $libro->id,
            'user_id' => $usuario->id,
            'fecha_prestamo' => now()->toDateString(),
            'fecha_devolucion' => now()->addDays(7)->toDateString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['libro_id' => $libro->id, 'user_id' => $usuario->id]);

        $this->assertDatabaseHas('prestamos', [
            'libro_id' => $libro->id,
            'user_id' => $usuario->id,
        ]);
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function puede_marcar_prestamo_como_devuelto()
    {
        $prestamo = Prestamo::factory()->create([
            'fecha_devuelto' => null
        ]);

        $hoy = now()->toDateString();

        $response = $this->putJson("/api/prestamos/{$prestamo->id}", [
            'fecha_devuelto' => $hoy
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['fecha_devuelto' => $hoy]);

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'fecha_devuelto' => $hoy,
        ]);
    }
}
