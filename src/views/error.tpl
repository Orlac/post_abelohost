<div class="card shadow-sm mx-auto" style="max-width: 560px;">
    <div class="card-body text-center">
        <h1 class="card-title display-4 text-danger">{$ex->getCode()|default:'500'}</h1>
        <p class="card-text lead">{$text|default:'При обработке запроса возникла ошибка.'|escape}</p>
        {if $path}
            <p class="card-text text-muted small">Путь: {$path|escape}</p>
        {/if}
        <a href="{$router->url('home')}" class="btn btn-primary">← На главную</a>
    </div>
</div>