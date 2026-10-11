<img class="logo mb-3" alt="{$article->title|escape}" src="{$article->image|default:'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 200 200\'><rect width=\'200\' height=\'200\' rx=\'36\' fill=\'%234f46e5\'/><text x=\'100\' y=\'110\' text-anchor=\'middle\' fill=\'white\' font-size=\'14\' font-family=\'sans-serif\'>Article</text></svg>'}">
<h1 class="mb-3">{$article->title|escape}</h1>

<div class="mb-3 text-muted">
    <small>Просмотры: {$article->views}</small>
    <span class="mx-2">·</span>
    <small>Добавлено: {$article->createdAt}</small>
    {if $article->updatedAt}
        <span class="mx-2">·</span>
        <small>Обновлено: {$article->updatedAt}</small>
    {/if}
</div>

{if $article->description}
    <p class="lead">{$article->description|escape}</p>
{/if}

{if $article->text}
    <div class="mt-4">{$article->text|escape}</div>
{/if}

{if $categories}
    <div class="mt-4">
        <strong>Категории:</strong>
        {foreach $categories as $category}
            <a href="{$router->url('home')}" class="badge badge-primary mr-1">{$category->title|escape}</a>
        {/foreach}
    </div>
{/if}

<p class="mt-4 text-muted">
    <a href="{$router->url('home')}" class="text-decoration-none">← Назад ко всем статьям</a>
</p>