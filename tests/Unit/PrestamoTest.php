<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Libro;
use App\Models\Prestamo;

class PrestamoTest extends TestCase
{
    use RefreshDatabase; // Limpia la DB entre pruebas

    /** @test */
    public function puede_crear_un_prestamo_si_hay_copias_disponibles()
    {
        $user = User::factory()->create();
        $libro = Libro::factory()->create([
            'copias_totales' => 3,
            'copias_disponibles' => 3
        ]);

        $response = $this->postJson('/api/prestamos', [
            'libro_id' => $libro->id,
            'user_id' => $user->id,
            'fecha_prestamo' => now()->format('Y-m-d'),
            'fecha_devolucion' => now()->addDays(7)->format('Y-m-d')
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'libro_id' => $libro->id,
                     'user_id' => $user->id,
                     'estado' => 'prestado'
                 ]);

        $this->assertDatabaseHas('prestamos', [
            'libro_id' => $libro->id,
            'user_id' => $user->id
        ]);

        $libro->refresh();
        $this->assertEquals(2, $libro->copias_disponibles); // Copia restada
    }

    /** @test */
    public function no_puede_crear_prestamo_si_no_hay_copias()
    {
        $user = User::factory()->create();
        $libro = Libro::factory()->create([
            'copias_totales' => 1,
            'copias_disponibles' => 0
        ]);

        $response = $this->postJson('/api/prestamos', [
            'libro_id' => $libro->id,
            'user_id' => $user->id,
            'fecha_prestamo' => now()->format('Y-m-d'),
            'fecha_devolucion' => now()->addDays(7)->format('Y-m-d')
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'message' => 'No hay copias disponibles'
                 ]);
    }

    /** @test */
    public function puede_devolver_un_libro()
    {
        $user = User::factory()->create();
        $libro = Libro::factory()->create([
            'copias_totales' => 3,
            'copias_disponibles' => 2
        ]);

        $prestamo = Prestamo::create([
            'libro_id' => $libro->id,
            'user_id' => $user->id,
            'fecha_prestamo' => now()->format('Y-m-d'),
            'fecha_devolucion' => now()->addDays(7)->format('Y-m-d'),
            'estado' => 'prestado'
        ]);

        $response = $this->putJson("/api/prestamos/{$prestamo->id}", [
            'fecha_devuelto' => now()->format('Y-m-d')
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['estado' => 'devuelto']);

        $libro->refresh();
        $this->assertEquals(3, $libro->copias_disponibles); // Copia devuelta
    }
}
