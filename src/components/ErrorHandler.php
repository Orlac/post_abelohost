<?php

declare(strict_types=1);
namespace app\components;

use Exception;


class ErrorHandler
{

    public string $tpl = 'error.tpl';

    public ?View $view = null;

    public function renderError(Exception $ex, array $params = []): string
    {
        /** @var View $view */
        $view = $this->view ?? Container::get(View::class);

        return $view->render($this->tpl, [
            'ex' => $ex, 
            'title' => $ex->getMessage(),
            ...$params
        ]);
    }
}
