<?php

namespace app\components;

use Smarty\Smarty;

class View
{
    public string $templateDir = __DIR__ . '/../views/';
    public string $layout = 'layouts/main.tpl';

    public string $compileDir;
    public string $cacheDir;

    private Smarty $smarty;

    public function __construct() 
    {

        $this->compileDir = $this->compileDir ?? sys_get_temp_dir() . '/smarty/compile';
        $this->cacheDir = $this->cacheDir ?? sys_get_temp_dir() . '/smarty/cache';

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($this->templateDir);
        $this->smarty->setCompileDir($this->compileDir);
        $this->smarty->setCacheDir($this->cacheDir);
    }

    /**
     * @param array<string, mixed> $params
     */
    public function render(string $view, array $params = []): string
    {
        foreach ($params as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        $content = $this->smarty->fetch($view);

        if ($this->layout === null) {
            foreach (array_keys($params) as $key) {
                $this->smarty->clearAssign($key);
            }

            return $content;
        }

        $this->smarty->assign('content', $content);
        $output = $this->smarty->fetch($this->layout);

        foreach (array_keys($params) as $key) {
            $this->smarty->clearAssign($key);
        }
        $this->smarty->clearAssign('content');

        return $output;
    }

}
