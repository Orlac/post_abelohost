<?php

declare(strict_types=1);

namespace app\Controllers;

use app\components\AppException;
use app\components\Container;
use app\components\Pager;
use app\components\Request;
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

    public function article(int $id): string
    {
        /** @var Storage $storage */
        $storage = Container::get(Storage::class);
        /** @var View $view */
        $view = Container::get(View::class);

        $article = $storage->getArticle($id);

        if ($article === null) {
            throw new AppException('Статья не найдена', 404);
        }

        $title = (string) getenv('NAME_APP') . ' / ' . $article->title;

        $categories = $storage->getArticleCategories($id);

        return $view->render('article.tpl', [
            'title' => $title,
            'article' => $article,
            'categories' => $categories,
        ]);
    }

    public function category(int $id): string
    {
        /** @var Storage $storage */
        $storage = Container::get(Storage::class);
        /** @var Request $request */
        $request = Container::get(Request::class);
        /** @var Pager $pager */
        $pager = Container::get(Pager::class);
        /** @var View $view */
        $view = Container::get(View::class);

        $category = $storage->getCategory($id);

        if ($category === null) {
            throw new AppException('Категория не найдена', 404);
        }

        $title = (string) getenv('NAME_APP') . ' / ' . $category->title;

        $sort = (string) $request->getQuery('sort', 'created_at');
        $page = max(1, (int) $request->getQuery('page', 1));

        $pager = $storage->getArticlesByCategory(
            cid: $id,
            sort: $sort,
            page: $page
        );

        return $view->render('category.tpl', [
            'title' => $title,
            'category' => $category,
            'articles' => $pager->getItems(),
            'sort' => $sort,
            'pager' => $pager,
            'prevPage' => $pager->getCurrentPage() - 1,
            'nextPage' => $pager->getCurrentPage() + 1,
        ]);
    }
}