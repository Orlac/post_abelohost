<?php

use app\components\Container;
use app\components\DatabaseInterface;
use app\components\MysqlDatabase;
use app\components\Router;
use app\controllers\IndexController;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/routes.php';

Container::set(DatabaseInterface::class, static fn (): DatabaseInterface => MysqlDatabase::fromEnv());

$database = Container::get(DatabaseInterface::class);

$router = new Router(
    Closure::fromCallable('pageNotFound'),
    Closure::fromCallable('pageMethodNotAllowed')
);

// $router->get('/', static fn (): string => pageHome($database));
$router->get('/', function (): string {
    /** @var IndexController $ctrl */
    $ctrl = Container::get(IndexController::class);
    return $ctrl->home();
});
$router->get('/article/{id:(\d+)}/', function (int $id, int $page = 1): string {
    return $id . ' - ' . $page;
});
$router->get('/info', 'pageInfo');
$router->get('/health', static fn (): string => pageHealth($database));

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
