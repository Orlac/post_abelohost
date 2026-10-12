<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/styles/bootstrap.min.css">
    <title>{$title|default:'Post App'}</title>
    <style>
        body { background: #f4f6fb; }
        .navbar-brand { font-weight: 600; }
        .navbar-nav .nav-link { color: #4f46e5; font-weight: 500; }
        .navbar-nav .nav-link:hover { color: #3b34a8; }
        main { flex: 1; }
        .logo { width: 220px; height: 220px; display: block; margin: 0 auto 1rem; }
        small { color: #666; }
        code { background: #eef2ff; border-radius: 4px; padding: 2px 6px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-light bg-light border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{$router->url('home')}">Post App</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{$router->url('home')}">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{$router->url('info')}">Info</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container my-5">
    <main>
        {$content}
    </main>
</div>
</body>
</html>