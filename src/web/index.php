<?php

declare(strict_types=1);
use app\components\AppException;
use app\components\Container;
use app\components\DatabaseInterface;
use app\components\ErrorHandler;
use app\components\MysqlDatabase;
use app\components\Request;
use app\components\Response;
use app\components\Router;
use app\components\View;
use app\components\Pager;
use app\Controllers\ArticlesController;
use app\Controllers\IndexController;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/routes.php';

Container::set(DatabaseInterface::class, static fn (): DatabaseInterface => MysqlDatabase::fromEnv());

$database = Container::get(DatabaseInterface::class);

$router = new Router(
    Closure::fromCallable('pageNotFound'),
    Closure::fromCallable('pageMethodNotAllowed'),
);

Container::set(Router::class, static fn () => $router);
Container::set(View::class, static fn () => (new View())->setRouter($router));
Container::set(Request::class, static fn () => new Request());
Container::set(Pager::class, static fn () => new Pager(Container::get(DatabaseInterface::class)));

// $router->get('/', static fn (): string => pageHome($database));
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

$router->get('/info', 'pageInfo', 'info');
$router->get('/health', static fn (): string => pageHealth($database), 'health');

// try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
// } catch (AppException $ex) {
//     /**
//      * @var ErrorHandler $handler
//      */
//     $handler = Container::get(ErrorHandler::class);
//     echo $handler->renderError($ex);
// }

