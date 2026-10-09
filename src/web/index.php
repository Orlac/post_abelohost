<?php

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/routes.php';

Container::set(DatabaseInterface::class, static fn (): DatabaseInterface => MysqlDatabase::fromEnv());

$database = Container::get(DatabaseInterface::class);

$router = new Router(
    Closure::fromCallable('pageNotFound'),
    Closure::fromCallable('pageMethodNotAllowed')
);

$router->get('/', static fn (): string => pageHome($database));
$router->get('/info', 'pageInfo');
$router->get('/health', static fn (): string => pageHealth($database));

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
