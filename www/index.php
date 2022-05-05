<?php

declare(strict_types=1);

// Start in external scope
use System\HTTP\FileCache;
use System\HTTP\FileCacheType;

(function ($get, $post, $request, $server) {

    define('BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
    define('WWW_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

    session_start();

    #error_reporting(E_ALL);
    #ini_set('display_errors', "1");

    require_once BASE_PATH . 'System/Autoloader.php';

    $autoloader = new \System\Autoloader(BASE_PATH);

    $autoloader->loadCalledClasses();

    $config = new \System\Config(BASE_PATH . 'config.ini');
    $request = new \System\HTTP\Request\Request($get, $post, $request, $server["REQUEST_METHOD"], $server["REQUEST_URI"], $server["REQUEST_TIME"]);

    $cacheMapping = [];
    foreach ([
                 FileCacheType::CSS,
                 FileCacheType::JS
             ] as $type) {
        $cacheMapping['.' . $type] = FileCacheType::getFileType($type);
    }

    $fileCache = new FileCache($cacheMapping, $request, [
        'requestDir' => $config->get('site', 'requestDir'),
        'resourceDir' => $config->get('site', 'resourceDir'),
        'cacheDir' => $config->get('site', 'cacheDir') . $config->get('site', 'version') . '/',
        'version' => $config->get('site', 'version'),
    ]);

    if ($fileCache->isFileRequested()) {
        return;
    }

    $pdo = new PDO($config->get("mysql", "dsn"), $config->get("mysql", "user"), $config->get("mysql", "password"), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $uri = rtrim($request->getParam($config->get("site", "token")) == null ? '/' : $request->getParam($config->get("site", "token")), '/');
    if (empty($uri)) {
        $uri = '/';
    }

    require BASE_PATH . "System/ErrorHandling.php";

    $connection = new \System\App\Connection($pdo);
    $app = new \System\App\App();

    $view = new \System\App\View\View();
    $view->setDirectory(BASE_PATH . "App/View");

    $factoryManager = new \System\App\Model\FactoryManager($connection, $config);

    $session = new \System\Session\Session($factoryManager);

    \System\Security\CSRF::init($session, $config);
    \System\Security\CSRF::setRequest($request);

    $app->setView($view);
    $app->setConfig($config);
    $app->setFactoryManager($factoryManager);
    $app->setSession($session);
    $app->setServiceCollector(new \System\App\Service\ServiceCollector($app));

    $controllerCollector = new \System\App\Controller\ControllerCollector($app);

    require_once BASE_PATH . "controllers.php";

    require_once BASE_PATH . "System/HTTP/FastRoute/bootstrap.php";

    $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $routeCollector) use ($controllerCollector) {

        foreach ($controllerCollector->getRoutableControllers() as $controller) {
            foreach ($controller->getRoutes() as $route) {
                $routeCollector->addRoute($route->getMethod(), $route->getRoute(), (object)[
                    'controller' => $controller,
                    'route' => $route
                ]);
            }
        }
    });

    $routeInfo = $dispatcher->dispatch($request->getMethod(), $uri);

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            header("HTTP/1.0 404 Not Found");

            $errorController = $controllerCollector->get(\App\Controller\ErrorController::class);

            $errorController->onRequest($request, new \System\HTTP\Route(\System\HTTP\Request\RequestType::GET, '/route-not-found', ''), [
                "type" => FastRoute\Dispatcher::NOT_FOUND
            ]);
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            header("HTTP/1.0 405 Method Not Allowed");

            $allowedMethods = $routeInfo[1];

            /**
             * @var $errorController \App\Controller\ErrorController
             */
            $errorController = $controllerCollector->get(\App\Controller\ErrorController::class);

            $errorController->onRequest($request, new \System\HTTP\Route(\System\HTTP\Request\RequestType::GET, '/method-not-allowed', ''), [
                "type" => FastRoute\Dispatcher::METHOD_NOT_ALLOWED,
                "allowedMethods" => $allowedMethods
            ]);
            break;
        case FastRoute\Dispatcher::FOUND:
            $vars = $routeInfo[2];
            /**
             * @var $controller \System\HTTP\IRoutable
             */
            $controller = $routeInfo[1]->controller;
            $route = $routeInfo[1]->route;

            $controller->onRequest($request, $route, $vars);

            break;
    }

    define('RETROWEB_EXECUTION_SUCCESSFUL', true);
})($_GET, $_POST, $_REQUEST, $_SERVER);