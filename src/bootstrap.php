<?php

declare(strict_types=1);
use app\components\Container;
use app\components\DatabaseInterface;
use app\components\MysqlDatabase;
use app\components\Pager;
use app\components\Request;
use app\components\Router;
use app\components\View;

Container::set(DatabaseInterface::class, fn (): DatabaseInterface => MysqlDatabase::fromEnv());

$database = Container::get(DatabaseInterface::class);

$router = Container::get(Router::class);
Container::set(View::class, fn () => (new View())->setRouter($router));
Container::set(Pager::class, fn () => new Pager(Container::get(DatabaseInterface::class)));