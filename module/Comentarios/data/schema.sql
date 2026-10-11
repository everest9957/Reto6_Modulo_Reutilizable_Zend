CREATE DATABASE IF NOT EXISTS zend_comentarios
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE zend_comentarios;

CREATE TABLE IF NOT EXISTS `comentarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `autor` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `contenido` TEXT NOT NULL,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `estado` ENUM('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  INDEX idx_post (post_id),
  INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `comentarios` (`post_id`, `autor`, `email`, `contenido`, `estado`) VALUES
(1, 'Ana López',    'ana@example.com',   'Excelente artículo, muy claro.', 'aprobado'),
(1, 'Carlos Ruiz',  'carlos@example.com','Me ayudó mucho, gracias.',       'aprobado'),
(2, 'María Pérez',  'maria@example.com', 'Tengo una duda sobre el paso 3.', 'pendiente');
