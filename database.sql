-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS mi_base_de_datos;

-- Usar la base de datos
USE mi_base_de_datos;

-- Crear una tabla de ejemplo
CREATE TABLE alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar un usuario de prueba
INSERT INTO usuarios (username, password) VALUES
('admin', MD5('1234'));

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
