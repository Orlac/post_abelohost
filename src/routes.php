<?php

declare(strict_types=1);
use app\components\AppException;
use app\components\Container;
use app\components\ErrorHandler;
use app\components\Response;
use app\components\Router;
use app\Controllers\ArticlesController;

/**
 * @var Router $router
 */
$router = Container::get(Router::class);

$router->get('/', function (): string {
    /** @var ArticlesController $ctrl */
    $ctrl = Container::get(ArticlesController::class);
    return $ctrl->index();
}, 'home');

$router->get('/articles/{id:(\d+)}/', function (string $id): string|Response {
    /** @var ArticlesController $ctrl */
    $ctrl = Container::get(ArticlesController::class);
    return $ctrl->article((int) $id);
}, 'article');

$router->get('/category/{id:(\d+)}/', function (string $id): string|Response {
    /** @var ArticlesController $ctrl */
    $ctrl = Container::get(ArticlesController::class);
    return $ctrl->category((int) $id);
}, 'category');

$router->get('/info', function () {
    ob_start();
    phpinfo();

    return (string) ob_get_clean();
}, 'info');

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (AppException $ex) {
    /** @var Response $response */
    $response = Container::get(Response::class);
    $response->setStatusCode($ex->getCode())
        ->send();
    /**
     * @var ErrorHandler $handler
     */
    $handler = Container::get(ErrorHandler::class);
    echo $handler->renderError($ex);
}
