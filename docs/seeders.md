-- Autores
INSERT INTO biblioteca.autores (nombre, bio, created_at, updated_at) VALUES
('Gabriel García Márquez', 'Escritor colombiano, ganador del Premio Nobel de Literatura en 1982.', NOW(), NOW()),
('Isabel Allende', 'Escritora chilena conocida por novelas como "La casa de los espíritus".', NOW(), NOW()),
('Mario Vargas Llosa', 'Escritor peruano, ganador del Premio Nobel de Literatura en 2010.', NOW(), NOW());

-- Géneros
INSERT INTO biblioteca.generos (nombre, created_at, updated_at) VALUES
('Novela', NOW(), NOW()),
('Ciencia Ficción', NOW(), NOW()),
('Biografía', NOW(), NOW());

-- Usuarios
INSERT INTO biblioteca.users (name, email, password, created_at, updated_at) VALUES
('Fabián Hernández', 'fabian@example.com', '$2y$10$saltrandomhashedpassword1', NOW(), NOW()),
('Ana Pérez', 'ana@example.com', '$2y$10$saltrandomhashedpassword2', NOW(), NOW()),
('Carlos Gómez', 'carlos@example.com', '$2y$10$saltrandomhashedpassword3', NOW(), NOW());
