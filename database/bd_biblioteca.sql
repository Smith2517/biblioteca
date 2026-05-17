-- ============================================================
--  bd_biblioteca — Script SQL Completo
--  Sistema de Biblioteca Virtual Académica
--  Generado: 2026-05-17
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

-- Crear y seleccionar la base de datos
CREATE DATABASE IF NOT EXISTS `bd_biblioteca`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `bd_biblioteca`;

-- ──────────────────────────────────────────────
--  TABLA: tb_roles
-- ──────────────────────────────────────────────
DROP TABLE IF EXISTS `tb_roles`;
CREATE TABLE `tb_roles` (
    `id_role`    TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(50)      NOT NULL COMMENT 'Nombre legible del rol',
    `slug`       VARCHAR(20)      NOT NULL UNIQUE COMMENT 'Identificador interno: admin, teacher, student',
    `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tb_roles` (`name`, `slug`) VALUES
    ('Administrador', 'admin'),
    ('Docente',       'teacher'),
    ('Estudiante',    'student');

-- ──────────────────────────────────────────────
--  TABLA: tb_users
-- ──────────────────────────────────────────────
DROP TABLE IF EXISTS `tb_users`;
CREATE TABLE `tb_users` (
    `id_user`       INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `names`         VARCHAR(100)    NOT NULL,
    `surnames`      VARCHAR(100)    NOT NULL,
    `email`         VARCHAR(150)    NOT NULL UNIQUE,
    `password`      VARCHAR(255)    NOT NULL,
    `role_id`       TINYINT UNSIGNED NOT NULL DEFAULT 3,
    `photo`         VARCHAR(255)    NOT NULL DEFAULT 'default.png',
    -- Permisos gestionados por el Administrador
    `can_read`      TINYINT(1)      NOT NULL DEFAULT 1  COMMENT '1 = puede leer PDFs online',
    `can_download`  TINYINT(1)      NOT NULL DEFAULT 0  COMMENT '1 = puede descargar PDFs',
    -- Control de cuenta
    `status`        TINYINT(1)      NOT NULL DEFAULT 1  COMMENT '1=activo, 0=inactivo',
    `reset_token`   VARCHAR(64)     DEFAULT NULL,
    `reset_expires` DATETIME        DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_user`),
    KEY `idx_role` (`role_id`),
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `tb_roles`(`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario Admin por defecto — password: admin123
-- Hash generado con: password_hash('admin123', PASSWORD_BCRYPT, ['cost'=>10])
INSERT INTO `tb_users` (`names`, `surnames`, `email`, `password`, `role_id`, `can_read`, `can_download`, `status`) VALUES
    ('Administrador', 'Sistema', 'admin@biblioteca.com',
     '$2y$10$joapsNMKW5EJYwIo4Ma7oObSijveyw4ZpDg0RilNaif/oxGWzOHgO',
     1, 1, 1, 1),
    ('María', 'López Ríos', 'teacher@biblioteca.com',
     '$2y$10$joapsNMKW5EJYwIo4Ma7oObSijveyw4ZpDg0RilNaif/oxGWzOHgO',
     2, 1, 0, 1),
    ('Carlos', 'García Torres', 'student@biblioteca.com',
     '$2y$10$joapsNMKW5EJYwIo4Ma7oObSijveyw4ZpDg0RilNaif/oxGWzOHgO',
     3, 1, 0, 1);

-- ──────────────────────────────────────────────
--  TABLA: tb_categories
-- ──────────────────────────────────────────────
DROP TABLE IF EXISTS `tb_categories`;
CREATE TABLE `tb_categories` (
    `id_category` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)      NOT NULL,
    `description` VARCHAR(255)      DEFAULT NULL,
    `color`       VARCHAR(7)        NOT NULL DEFAULT '#6366f1' COMMENT 'Color HEX para la UI',
    `status`      TINYINT(1)        NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tb_categories` (`name`, `description`, `color`) VALUES
    ('Ciencias Exactas',     'Matemáticas, Física, Química',           '#6366f1'),
    ('Humanidades',          'Historia, Filosofía, Literatura',        '#f59e0b'),
    ('Ingeniería',           'Ingeniería de sistemas, Civil, etc.',    '#10b981'),
    ('Ciencias Sociales',    'Sociología, Psicología, Economía',       '#3b82f6'),
    ('Tecnología',           'Informática, Programación, IA',          '#8b5cf6'),
    ('Medicina y Salud',     'Medicina, Farmacología, Enfermería',     '#ef4444'),
    ('Derecho',              'Legislación, Jurisprudencia',            '#f97316'),
    ('Arte y Diseño',        'Diseño gráfico, Arquitectura, Arte',     '#ec4899');

-- ──────────────────────────────────────────────
--  TABLA: tb_authors
-- ──────────────────────────────────────────────
DROP TABLE IF EXISTS `tb_authors`;
CREATE TABLE `tb_authors` (
    `id_author`  SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150)      NOT NULL,
    `bio`        TEXT              DEFAULT NULL,
    `status`     TINYINT(1)        NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tb_authors` (`name`, `bio`) VALUES
    ('Donald E. Knuth',      'Matemático y científico informático, autor de The Art of Computer Programming.'),
    ('Robert C. Martin',     'Ingeniero de software, autor de Clean Code y principios SOLID.'),
    ('Martin Fowler',        'Arquitecto de software, especialista en refactoring y patrones de diseño.'),
    ('Steve McConnell',      'Autor de Code Complete, referencia en ingeniería de software.'),
    ('Erich Gamma',          'Co-autor de Design Patterns, uno de los Gang of Four.');

-- ──────────────────────────────────────────────
--  TABLA: tb_editorials
-- ──────────────────────────────────────────────
DROP TABLE IF EXISTS `tb_editorials`;
CREATE TABLE `tb_editorials` (
    `id_editorial` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(150)      NOT NULL,
    `country`      VARCHAR(80)       DEFAULT NULL,
    `website`      VARCHAR(255)      DEFAULT NULL,
    `status`       TINYINT(1)        NOT NULL DEFAULT 1,
    `created_at`   TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_editorial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tb_editorials` (`name`, `country`, `website`) VALUES
    ('O\'Reilly Media',      'Estados Unidos', 'https://www.oreilly.com'),
    ('Pearson Education',    'Reino Unido',    'https://www.pearson.com'),
    ('Addison-Wesley',       'Estados Unidos', 'https://www.informit.com'),
    ('McGraw-Hill',          'Estados Unidos', 'https://www.mheducation.com'),
    ('Springer',             'Alemania',       'https://www.springer.com');

-- ──────────────────────────────────────────────
--  TABLA: tb_books
-- ──────────────────────────────────────────────
DROP TABLE IF EXISTS `tb_books`;
CREATE TABLE `tb_books` (
    `id_book`      INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `title`        VARCHAR(255)      NOT NULL,
    `isbn`         VARCHAR(20)       DEFAULT NULL,
    `description`  TEXT              DEFAULT NULL,
    `pdf_file`     VARCHAR(255)      NOT NULL,
    `cover_image`  VARCHAR(255)      NOT NULL DEFAULT 'no-cover.png',
    `pages`        SMALLINT UNSIGNED DEFAULT NULL,
    `year`         YEAR              DEFAULT NULL,
    `language`     VARCHAR(50)       NOT NULL DEFAULT 'Español',
    `author_id`    SMALLINT UNSIGNED NOT NULL,
    `category_id`  SMALLINT UNSIGNED NOT NULL,
    `editorial_id` SMALLINT UNSIGNED NOT NULL,
    `views`        INT UNSIGNED      NOT NULL DEFAULT 0,
    `downloads`    INT UNSIGNED      NOT NULL DEFAULT 0,
    `status`       TINYINT(1)        NOT NULL DEFAULT 1,
    `created_at`   TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_book`),
    KEY `idx_category`  (`category_id`),
    KEY `idx_author`    (`author_id`),
    KEY `idx_editorial` (`editorial_id`),
    FULLTEXT KEY `ft_search` (`title`, `description`),
    CONSTRAINT `fk_books_author`    FOREIGN KEY (`author_id`)    REFERENCES `tb_authors`(`id_author`),
    CONSTRAINT `fk_books_category`  FOREIGN KEY (`category_id`)  REFERENCES `tb_categories`(`id_category`),
    CONSTRAINT `fk_books_editorial` FOREIGN KEY (`editorial_id`) REFERENCES `tb_editorials`(`id_editorial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────
--  TABLA: tb_favorites
-- ──────────────────────────────────────────────
DROP TABLE IF EXISTS `tb_favorites`;
CREATE TABLE `tb_favorites` (
    `id_favorite` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED NOT NULL,
    `book_id`     INT UNSIGNED NOT NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_favorite`),
    UNIQUE KEY `uq_user_book` (`user_id`, `book_id`),
    CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `tb_users`(`id_user`) ON DELETE CASCADE,
    CONSTRAINT `fk_fav_book` FOREIGN KEY (`book_id`) REFERENCES `tb_books`(`id_book`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────
--  TABLA: tb_history
-- ──────────────────────────────────────────────
DROP TABLE IF EXISTS `tb_history`;
CREATE TABLE `tb_history` (
    `id_history` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `book_id`    INT UNSIGNED NOT NULL,
    `action`     ENUM('read','download') NOT NULL DEFAULT 'read',
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_history`),
    KEY `idx_hist_user` (`user_id`),
    KEY `idx_hist_book` (`book_id`),
    CONSTRAINT `fk_hist_user` FOREIGN KEY (`user_id`) REFERENCES `tb_users`(`id_user`) ON DELETE CASCADE,
    CONSTRAINT `fk_hist_book` FOREIGN KEY (`book_id`) REFERENCES `tb_books`(`id_book`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────
--  TABLA: tb_comments
-- ──────────────────────────────────────────────
DROP TABLE IF EXISTS `tb_comments`;
CREATE TABLE `tb_comments` (
    `id_comment` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `book_id`    INT UNSIGNED NOT NULL,
    `comment`    TEXT         NOT NULL,
    `rating`     TINYINT UNSIGNED DEFAULT NULL COMMENT 'Calificación 1-5',
    `status`     TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_comment`),
    KEY `idx_cmt_book` (`book_id`),
    CONSTRAINT `fk_cmt_user` FOREIGN KEY (`user_id`) REFERENCES `tb_users`(`id_user`) ON DELETE CASCADE,
    CONSTRAINT `fk_cmt_book` FOREIGN KEY (`book_id`) REFERENCES `tb_books`(`id_book`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET foreign_key_checks = 1;

-- ══════════════════════════════════════════════════════════════
--  NOTA: Contraseña por defecto de los 3 usuarios semilla:
--        password → "password"
--  ⚠️  Cambiar inmediatamente en producción.
--  Credenciales:
--    admin@biblioteca.com   / password  (rol: Administrador)
--    teacher@biblioteca.com / password  (rol: Docente)
--    student@biblioteca.com / password  (rol: Estudiante)
-- ══════════════════════════════════════════════════════════════
