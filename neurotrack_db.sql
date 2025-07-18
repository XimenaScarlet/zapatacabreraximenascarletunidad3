-- Create database
CREATE DATABASE IF NOT EXISTS neurotrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE neurotrack;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('paciente', 'terapeuta', 'admin') NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    session_token VARCHAR(64) DEFAULT NULL,
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_expiry DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Emotional states table
CREATE TABLE estados_emocionales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    estado VARCHAR(50) NOT NULL,
    comentario TEXT,
    fecha DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Observations table
CREATE TABLE observaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estado_id INT NOT NULL,
    terapeuta_id INT NOT NULL,
    observacion TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (estado_id) REFERENCES estados_emocionales(id) ON DELETE CASCADE,
    FOREIGN KEY (terapeuta_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Assignments table
CREATE TABLE asignaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    terapeuta_id INT NOT NULL,
    UNIQUE KEY unique_assignment (paciente_id),
    FOREIGN KEY (paciente_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (terapeuta_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
