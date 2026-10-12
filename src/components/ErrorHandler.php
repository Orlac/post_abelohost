<?php

declare(strict_types=1);
namespace app\components;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

class ErrorHandler extends Exception
{

    public string $tpl = 'error.tpl';

    public ?View $view = null;

    public function renderError(Exception $ex, array $params = []): string
    {
        /** @var View $view */
        $view = $this->view ?? Container::get(View::class);
        /** @var Response $response */
        $response = Container::get(Response::class);

        $response->setStatusCode($ex->code);

        return $view->render($this->tpl, [
            'ex' => $ex, 
            'title' => $ex->message,
            ...$params
        ]);
    }
}
