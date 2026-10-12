{if $categories}
    <div class="row">
        {foreach $categories as $category}
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <a href="{$router->url('category', ['id' => $category->id])}" class="text-decoration-none">
                            <h5 class="card-title">{$category->title|escape}</h5>
                            <p class="card-text text-muted small">{$category->description|escape}</p>
                            <p class="card-text">
                                <small class="text-muted">ID: {$category->id}</small>
                                <span class="badge badge-secondary ml-2">{$category->articlesCount} статей</span>
                            </p>
                        </a>
                        
                        {if $category->lastArticles}
                            <div class="mt-2">
                                <small class="text-muted">Последние статьи:</small>
                                {foreach $category->lastArticles as $article}
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        <a href="{$router->url('article', ['id' => $article->id])}" class="badge badge-secondary text-decoration-none">
                                        {$article->title|escape|truncate:50}
                                        </a>
                                    </div>
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
{else}
    <p class="text-muted">Категории отсутствуют.</p>
{/if}