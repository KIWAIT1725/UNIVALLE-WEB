-- Crear base de datos UNIVALLE
CREATE DATABASE IF NOT EXISTS UNIVALLE;
USE UNIVALLE;

-- Tabla de Estudiantes
CREATE TABLE IF NOT EXISTS estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_estudiante VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    fecha_nacimiento DATE,
    direccion TEXT,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Maestros
CREATE TABLE IF NOT EXISTS maestros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_maestro VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    especialidad VARCHAR(100),
    fecha_contratacion DATE,
    salario DECIMAL(10,2),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Asignaturas
CREATE TABLE IF NOT EXISTS asignaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_asignatura VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    creditos INT NOT NULL,
    horas_semanales INT NOT NULL,
    semestre INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Asignaciones (Maestros a Asignaturas)
CREATE TABLE IF NOT EXISTS asignaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maestro_id INT NOT NULL,
    asignatura_id INT NOT NULL,
    periodo VARCHAR(20) NOT NULL,
    año INT NOT NULL,
    horario VARCHAR(100),
    aula VARCHAR(50),
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maestro_id) REFERENCES maestros(id) ON DELETE CASCADE,
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id) ON DELETE CASCADE
);

-- Insertar datos de ejemplo para estudiantes
INSERT INTO estudiantes (codigo_estudiante, nombre, apellido, email, telefono, fecha_nacimiento, direccion, password) VALUES
('EST001', 'Juan Carlos', 'Pérez García', 'juan.perez@univalle.edu.co', '3001234567', '2000-05-15', 'Calle 123 #45-67', MD5('123456')),
('EST002', 'María Fernanda', 'López Rodríguez', 'maria.lopez@univalle.edu.co', '3007654321', '1999-08-22', 'Carrera 89 #12-34', MD5('123456')),
('EST003', 'Carlos Andrés', 'Martínez Ruiz', 'carlos.martinez@univalle.edu.co', '3009876543', '2001-03-10', 'Avenida 15 #20-30', MD5('123456'));

-- Insertar datos de ejemplo para maestros
INSERT INTO maestros (codigo_maestro, nombre, apellido, email, telefono, especialidad, fecha_contratacion, salario) VALUES
('MAE001', 'Dr. Roberto', 'Martínez Silva', 'roberto.martinez@univalle.edu.co', '3009876543', 'Matemáticas', '2020-01-15', 4500000.00),
('MAE002', 'Dra. Ana María', 'González Torres', 'ana.gonzalez@univalle.edu.co', '3005432109', 'Física', '2019-08-01', 4200000.00),
('MAE003', 'Mg. Luis Fernando', 'Rodríguez Pérez', 'luis.rodriguez@univalle.edu.co', '3001122334', 'Química', '2021-03-20', 4000000.00);

-- Insertar datos de ejemplo para asignaturas
INSERT INTO asignaturas (codigo_asignatura, nombre, descripcion, creditos, horas_semanales, semestre) VALUES
('MAT101', 'Cálculo Diferencial', 'Introducción al cálculo diferencial y sus aplicaciones', 4, 6, 1),
('FIS101', 'Física Mecánica', 'Principios fundamentales de la mecánica clásica', 4, 6, 1),
('QUI101', 'Química General', 'Conceptos básicos de química general', 3, 4, 1),
('MAT201', 'Cálculo Integral', 'Continuación del cálculo diferencial, enfoque en integrales', 4, 6, 2),
('FIS201', 'Física Electromagnética', 'Estudio del electromagnetismo y sus aplicaciones', 4, 6, 2);

-- Insertar datos de ejemplo para asignaciones
INSERT INTO asignaciones (maestro_id, asignatura_id, periodo, año, horario, aula) VALUES
(1, 1, '2024-1', 2024, 'Lunes y Miércoles 8:00-10:00', 'Aula 101'),
(1, 4, '2024-1', 2024, 'Martes y Jueves 8:00-10:00', 'Aula 102'),
(2, 2, '2024-1', 2024, 'Martes y Jueves 10:00-12:00', 'Laboratorio Física'),
(2, 5, '2024-1', 2024, 'Viernes 14:00-18:00', 'Laboratorio Física'),
(3, 3, '2024-1', 2024, 'Lunes, Miércoles y Viernes 14:00-16:00', 'Laboratorio Química');
