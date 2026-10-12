<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h1 class="card-title mb-3">{$category->title|escape}</h1>

        <p class="card-text lead">{$category->description|escape}</p>

        <p class="card-text">
            <small class="text-muted">ID: {$category->id}</small>
            <span class="badge badge-secondary ml-2">{$category->articlesCount} статей</span>
            <span class="badge badge-info ml-2">Создано: {$category->createdAt}</span>
            {if $category->updatedAt}
                <span class="badge badge-warning ml-2">Обновлено: {$category->updatedAt}</span>
            {/if}
        </p>

        <p class="mt-3">
            <a href="{$router->url('home')}" class="badge badge-primary">← Все категории</a>
        </p>

        <form class="form-inline mt-3" method="get">
            <label class="mr-2 text-muted small" for="sortSelect">Сортировка статей:</label>
            <select name="sort" id="sortSelect" class="form-control form-control-sm" onchange="this.form.submit()">
                <option value="created_at" {if $sort === 'created_at'}selected="selected"{/if}>По дате создания (новые)</option>
                <option value="views" {if $sort === 'views'}selected="selected"{/if}>По просмотрам</option>
            </select>
            <input type="hidden" name="id" value="{$category->id}">
            <input type="hidden" name="page" value="{$pager->getCurrentPage()}">
        </form>
    </div>
</div>

{if $articles}
    <h4 class="mb-3">Статьи в этой категории ({$pager->getTotal()})</h4>
    <div class="row">
        {foreach $articles as $article}
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    {if $article->image}
                        <img src="{$article->image}" class="card-img-top" alt="{$article->title|escape}" style="height: 160px; object-fit: cover;">
                    {/if}
                    <div class="card-body">
                        <h5 class="card-title">{$article->title|escape}</h5>
                        <p class="card-text text-muted small">{$article->description|escape}</p>
                        <p class="card-text">
                            <small class="text-muted">Просмотры: {$article->views}</small>
                            <small class="text-muted ml-2">{$article->createdAt}</small>
                        </p>
                        <a href="{$router->url('article', ['id' => $article->id])}" class="btn btn-outline-primary btn-sm">Читать</a>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>

    {if $pager->getTotalPages() > 1}
        <nav aria-label="Страницы категории" class="mt-4">
            <ul class="pagination justify-content-center">
                {if $pager->hasPrev()}
                    <li class="page-item">
                        <a class="page-link" href="?id={$category->id}&sort={$sort}&page={$prevPage}">←</a>
                    </li>
                {/if}

                {for $i=1 to $pager->getTotalPages()}
                    <li class="page-item {if $i === $pager->getCurrentPage()}active{/if}">
                        <a class="page-link" href="?id={$category->id}&sort={$sort}&page={$i}">{$i}</a>
                    </li>
                {/for}

                {if $pager->hasNext()}
                    <li class="page-item">
                        <a class="page-link" href="?id={$category->id}&sort={$sort}&page={$nextPage}">→</a>
                    </li>
                {/if}
            </ul>
        </nav>
    {/if}
{else}
    <div class="alert alert-info">
        В этой категории пока нет статей.
    </div>
{/if}

<p class="mt-4">
    <a href="{$router->url('home')}" class="text-decoration-none">← Назад ко всем статьям</a>
</p>