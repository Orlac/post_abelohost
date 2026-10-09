<?php

namespace app\components;

class Response
{

    public string $basePath = __DIR__ . '/../views/';
    public string $layout = 'main'


    /**
     * используя Smarty\Smarty Рендерит файл view с параметрами и вставляет в layout
     * 
     * @param string $view $name относительный путь в basePath к файлу php
     * @param array $params
     * 
     */
    public function render(string $view, $params = []): string
    {

    }
}
