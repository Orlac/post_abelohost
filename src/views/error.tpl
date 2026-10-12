<div class="card shadow-sm mx-auto">
    <div class="card-body ">
        <h1 class="card-title text-center display-4 text-danger">{$ex->getCode()|default:'500'}</h1>
        <p class="card-text lead">{$ex->getMessage()|default:'При обработке запроса возникла ошибка.'|escape}</p>
        <pre>{$ex->getTraceAsString()}</pre>
        <a href="{$router->url('home')}" class="btn btn-primary">← На главную</a>
    </div>
</div>