<?php

declare(strict_types=1);

namespace app\Controllers;

use app\components\Container;
use app\components\Response;
use app\components\Storage;
use app\components\View;

class ArticlesController
{
    public function index(): string
    {
        /** @var Storage $storage */
        $storage = Container::get(Storage::class);
        /** @var View $view */
        $view = Container::get(View::class);

        return $view->render('home.tpl', [
            'title' => 'Главная',
            'categories' => $storage->getCategories(),
        ]);
    }

    public function article(int $id): string|Response
    {
        /** @var Storage $storage */
        $storage = Container::get(Storage::class);
        /** @var View $view */
        $view = Container::get(View::class);

        $article = $storage->getArticle($id);

        if ($article === null) {
            $title = 'Статья не найдена';
            return Response::html($view->render('home.tpl', [
                'title' => $title,
                'categories' => [],
            ]), 404);
        }

        $title = (string) getenv('NAME_APP') . ' / ' . $article->title;

        $categories = $storage->getArticleCategories($id);

        return $view->render('article.tpl', [
            'title' => $title,
            'article' => $article,
            'categories' => $categories,
        ]);
    }

    public function category(int $id): string|Response
    {
        /** @var Storage $storage */
        $storage = Container::get(Storage::class);
        /** @var View $view */
        $view = Container::get(View::class);

        $category = $storage->getCategory($id);

        if ($category === null) {
            $title = 'Категория не найдена';
            return Response::html($view->render('home.tpl', [
                'title' => $title,
                'categories' => [],
            ]), 404);
        }

        $title = (string) getenv('NAME_APP') . ' / ' . $category->title;

        $articles = $storage->getArticlesByCategory($id);

        return $view->render('category.tpl', [
            'title' => $title,
            'category' => $category,
            'articles' => $articles,
        ]);
    }
}