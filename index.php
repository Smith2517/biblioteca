<?php
/**
 * Front Controller — Punto de entrada único del sistema
 * Toda petición HTTP pasa por aquí.
 */

// Iniciar sesión de forma segura
session_start();

// Cargar configuración global
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Cargar clases del núcleo
require_once BASE_PATH . 'core/Session.php';
require_once BASE_PATH . 'core/Model.php';
require_once BASE_PATH . 'core/Controller.php';
require_once BASE_PATH . 'core/App.php';

// Inicializar el router y despachar la petición
$app = new App();
$app->dispatch();
