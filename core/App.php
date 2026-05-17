<?php
/**
 * core/App.php
 * Router principal — parsea la URL y despacha al Controller@Method correcto.
 *
 * Formato de URL: BASE_URL/controller/action/param1/param2...
 * Ejemplo local:  http://localhost/biblioteca/auth/login
 * Ejemplo prod:   https://midominio.com/auth/login
 */
class App
{
    private string $controller = 'AuthController';
    private string $action     = 'index';
    private array  $params     = [];

    /** Tabla de rutas: alias → [Controller, action] */
    private array $routes = [];

    public function __construct()
    {
        $this->loadRoutes();
    }

    /**
     * Carga las rutas definidas en routes/web.php
     */
    private function loadRoutes(): void
    {
        $routeFile = BASE_PATH . 'routes' . DS . 'web.php';
        if (file_exists($routeFile)) {
            $this->routes = require $routeFile;
        }
    }

    /**
     * Despacha la petición al controller y método adecuado.
     */
    public function dispatch(): void
    {
        // Obtener la URI limpia relativa al subdirectorio del proyecto
        $requestUri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptDir   = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $uri         = '/' . trim(substr($requestUri, strlen(rtrim($scriptDir, '/'))), '/');

        // Intentar resolver mediante tabla de rutas
        if (array_key_exists($uri, $this->routes)) {
            [$controllerClass, $this->action] = $this->routes[$uri];
            $this->controller = $controllerClass;
            $this->params     = [];
        } else {
            // Parsear segmentos dinámicos: /controller/action/p1/p2
            $segments = explode('/', trim($uri, '/'));
            if (!empty($segments[0])) {
                $this->controller = ucfirst(strtolower($segments[0])) . 'Controller';
            }
            if (!empty($segments[1])) {
                $this->action = strtolower($segments[1]);
            }
            $this->params = array_slice($segments, 2);
        }

        // Cargar archivo del controlador
        $controllerFile = BASE_PATH . 'app' . DS . 'controllers' . DS . $this->controller . '.php';

        if (!file_exists($controllerFile)) {
            $this->notFound();
            return;
        }

        require_once $controllerFile;

        if (!class_exists($this->controller)) {
            $this->notFound();
            return;
        }

        $controllerObj = new $this->controller();

        if (!method_exists($controllerObj, $this->action)) {
            $this->notFound();
            return;
        }

        // Ejecutar el método con los parámetros
        call_user_func_array([$controllerObj, $this->action], $this->params);
    }

    /**
     * Página 404 personalizada.
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
              <title>404 — ' . APP_NAME . '</title>
              <style>body{font-family:Inter,sans-serif;background:#0f0f23;color:#e2e8f0;
              display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}
              .box{text-align:center;} h1{font-size:6rem;margin:0;color:#6366f1;}
              p{font-size:1.2rem;color:#94a3b8;}</style></head>
              <body><div class="box"><h1>404</h1>
              <p>La página que buscas no existe.</p>
              <a href="' . BASE_URL . '" style="color:#6366f1;">← Volver al inicio</a>
              </div></body></html>';
    }
}
