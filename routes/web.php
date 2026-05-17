<?php
/**
 * routes/web.php
 * Tabla de rutas fijas del sistema.
 * Formato: 'uri' => ['NombreController', 'metodo']
 *
 * Las rutas no definidas aquí se resuelven dinámicamente por el Router
 * usando el patrón /controller/action/param1/param2
 */

return [
    // Raíz → login
    '/'                  => ['AuthController',        'index'],

    // Auth
    '/auth/login'        => ['AuthController',        'index'],
    '/auth/logout'       => ['AuthController',        'logout'],
    '/auth/forgot'       => ['AuthController',        'forgot'],
    '/auth/reset'        => ['AuthController',        'reset'],
    '/auth/doLogin'      => ['AuthController',        'doLogin'],
    '/auth/doForgot'     => ['AuthController',        'doForgot'],
    '/auth/doReset'      => ['AuthController',        'doReset'],

    // Admin
    '/admin'             => ['AdminController',        'dashboard'],
    '/admin/dashboard'   => ['AdminController',        'dashboard'],

    // Usuarios
    '/admin/users'       => ['UsersController',        'index'],
    '/admin/users/store' => ['UsersController',        'store'],
    '/admin/users/edit'  => ['UsersController',        'edit'],
    '/admin/users/delete'=> ['UsersController',        'delete'],
    '/admin/users/toggle'=> ['UsersController',        'toggleStatus'],
    '/admin/users/perms' => ['UsersController',        'updatePermissions'],

    // Libros
    '/admin/books'       => ['BooksController',        'index'],
    '/admin/books/store' => ['BooksController',        'store'],
    '/admin/books/edit'  => ['BooksController',        'edit'],
    '/admin/books/delete'=> ['BooksController',        'delete'],

    // Categorías
    '/admin/categories'       => ['CategoriesController', 'index'],
    '/admin/categories/store' => ['CategoriesController', 'store'],
    '/admin/categories/edit'  => ['CategoriesController', 'edit'],
    '/admin/categories/delete'=> ['CategoriesController', 'delete'],

    // Autores
    '/admin/authors'       => ['AuthorsController',    'index'],
    '/admin/authors/store' => ['AuthorsController',    'store'],
    '/admin/authors/edit'  => ['AuthorsController',    'edit'],
    '/admin/authors/delete'=> ['AuthorsController',    'delete'],

    // Editoriales
    '/admin/editorials'       => ['EditorialsController', 'index'],
    '/admin/editorials/store' => ['EditorialsController', 'store'],
    '/admin/editorials/edit'  => ['EditorialsController', 'edit'],
    '/admin/editorials/delete'=> ['EditorialsController', 'delete'],

    // Reportes
    '/admin/reports'     => ['AdminController',        'reports'],

    // API interna (AJAX)
    '/api/books/search'  => ['BooksController',        'search'],
    '/api/books/view'    => ['BooksController',        'registerView'],
    '/api/favorites/toggle' => ['FavoritesController', 'toggle'],
    '/api/comments/store'   => ['CommentsController',  'store'],
    '/api/comments/delete'  => ['CommentsController',  'delete'],
    '/api/history/add'      => ['HistoryController',   'add'],
    '/api/stats'            => ['AdminController',     'stats'],

    // Docente
    '/teacher'           => ['TeacherController',     'dashboard'],
    '/teacher/dashboard' => ['TeacherController',     'dashboard'],
    '/teacher/catalog'   => ['TeacherController',     'catalog'],
    '/teacher/favorites' => ['TeacherController',     'favorites'],
    '/teacher/history'   => ['TeacherController',     'history'],
    '/teacher/profile'   => ['TeacherController',     'profile'],
    '/teacher/read'      => ['TeacherController',     'read'],
    '/teacher/download'  => ['TeacherController',     'download'],

    // Estudiante
    '/student'           => ['StudentController',     'dashboard'],
    '/student/dashboard' => ['StudentController',     'dashboard'],
    '/student/catalog'   => ['StudentController',     'catalog'],
    '/student/favorites' => ['StudentController',     'favorites'],
    '/student/history'   => ['StudentController',     'history'],
    '/student/profile'   => ['StudentController',     'profile'],
    '/student/read'      => ['StudentController',     'read'],
    '/student/download'  => ['StudentController',     'download'],
];
