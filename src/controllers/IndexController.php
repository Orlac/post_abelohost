<?php

namespace app\controllers;

use app\components\Container;
use app\components\View;

class IndexController
{

    public function home(): string
    {
        /**
         * @var View $view
         */
        $view = Container::get(View::class);
        return $view->render('home.tpl', [
            'title' => 'Главная'
        ]);
    }


}
