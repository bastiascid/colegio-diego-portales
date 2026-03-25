-- script para crear las tablas de la base de datos MySQL 
-- del Colegio Diego Portales

CREATE TABLE IF NOT EXISTS usuarios_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar usuario admin por defecto (contraseña: admin123)
-- Hash generado con password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO usuarios_admin (username, password_hash, nombre) 
VALUES ('admin', '$2y$10$WqB3D9G0oFMyBwzUu2T9sO1.uPZqG/YqN6.P/yA/uS7v1yS7rT.eO', 'Administrador Principal')
ON DUPLICATE KEY UPDATE id=id;

CREATE TABLE IF NOT EXISTS comunicados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    fecha_publicacion DATE NOT NULL,
    mes_anio_tag VARCHAR(50) NOT NULL, -- ej: "Enero 2026", "Diciembre 2025"
    color_gradient VARCHAR(100) DEFAULT 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50) NOT NULL, -- "reglamento", "guia_preb", "guia_basica"
    icono VARCHAR(50) DEFAULT 'fas fa-file-alt', 
    enlace VARCHAR(500) NOT NULL, -- url del PDF o carpeta drive
    color_gradient VARCHAR(100) DEFAULT 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    nivel VARCHAR(50) DEFAULT NULL, -- ej: "Pre-Kinder", "1º Básico"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
