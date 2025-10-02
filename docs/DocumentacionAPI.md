# Documentación API – Sistema de Biblioteca
# Libros
# GET /libros

Descripción: Obtiene todos los libros.
Parámetros: ninguno
Respuesta exitosa:
[
  {
    "id": 1,
    "titulo": "El principito",
    "autor": { "id": 1, "nombre": "Antoine de Saint-Exupéry" },
    "genero": { "id": 2, "nombre": "Infantil" },
    "copias_totales": 5,
    "copias_disponibles": 3
  }
]


# POST /libros
Descripción: Crea un nuevo libro.
Payload:
{
  "titulo": "Cien años de soledad",
  "autor_id": 2,
  "genero_id": 3,
  "copias_totales": 4
}

Respuesta exitosa:
{
  "id": 10,
  "titulo": "Cien años de soledad",
  "autor": { "id": 2, "nombre": "Gabriel García Márquez" },
  "genero": { "id": 3, "nombre": "Novela" },
  "copias_totales": 4,
  "copias_disponibles": 4
}

# PUT /libros/{id}

Descripción: Actualiza un libro existente.
Payload: Igual que POST
Respuesta exitosa: JSON del libro actualizado

# DELETE /libros/{id}

Descripción: Elimina un libro.
Restricción: No se puede eliminar si tiene préstamos activos.
Respuesta exitosa: 204 No Content
Errores:400 / 422 si hay préstamos activos


######    2 Préstamos
# GET /prestamos

Descripción: Lista todos los préstamos.
Método: GET
Respuesta:
[
  {
    "id": 1,
    "libro": { "id": 1, "titulo": "El principito" },
    "user": { "id": 2, "name": "Juan Pérez" },
    "fecha_prestamo": "2025-10-02",
    "fecha_devolucion": "2025-10-10",
    "fecha_devuelto": null,
    "estado": "prestado"
  }
]


# POST /prestamos

Descripción: Crea un nuevo préstamo.
Payload:
{
  "libro_id": 1,
  "user_id": 2,
  "fecha_prestamo": "2025-10-02",
  "fecha_devolucion": "2025-10-10"
}

Respuesta exitosa: JSON del préstamo creado

Errores:
422 Validación fallida (campos obligatorios)
400 Si no hay copias disponibles del libro


# PUT /prestamos/{id}

Descripción: Marca un préstamo como devuelto.
Payload:
{
  "fecha_devuelto": "2025-10-05"
}
Respuesta exitosa: JSON del préstamo actualizado con estado "devuelto"


# 3 Estadísticas
# GET /estadisticas

Descripción: Obtiene estadísticas de la biblioteca.
Respuesta exitosa:

{
  "libros_mas_prestados": [
    { "libro_id": 1, "libro": { "titulo": "El principito" }, "total": 5 }
  ],
  "prestamos_activos": 3,
  "usuarios_mas_activos": [
    { "user_id": 2, "user": { "name": "Juan Pérez" }, "total": 4 }
  ],
  "libros_disponibles_por_genero": [
    { "genero_id": 2, "genero": { "nombre": "Infantil" }, "disponibles": 7 }
  ]
}



#### Notas

Todos los endpoints devuelven JSON.

Validaciones básicas en todos los POST/PUT (campos obligatorios, tipos de datos).

Código HTTP de respuesta:

200 OK → Éxito general

201 Created → Creación exitosa

204 No Content → Eliminación exitosa

422 Unprocessable Entity → Validación fallida
