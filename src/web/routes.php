<?php

function layout(string $title, string $content): string
{
    return sprintf(
        '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>%s</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f6fb; color: #222;
               display: flex; flex-direction: column; align-items: center; margin-top: 8vh; }
        nav { margin-bottom: 24px; }
        nav a { margin: 0 10px; color: #4f46e5; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        img { width: 220px; height: 220px; }
        small { color: #666; }
        code { background: #eef2ff; border-radius: 4px; padding: 2px 6px; }
    </style>
</head>
<body>
    <nav>
        <a href="/">Главная</a>
        <a href="/info">Info</a>
        <a href="/health">Health</a>
    </nav>
    <main>
        %s
    </main>
</body>
</html>',
        htmlspecialchars($title),
        $content
    );
}

function pageHome(DatabaseInterface $database): string
{
    return layout('Post App', sprintf(
        '<img alt="Логотип" src="data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 200 200\'><rect width=\'200\' height=\'200\' rx=\'36\' fill=\'%%234f46e5\'/><path d=\'M40 70l60 45 60-45v70a10 10 0 0 1-10 10H50a10 10 0 0 1-10-10z\' fill=\'white\'/><path d=\'M40 60a10 10 0 0 1 10-10h100a10 10 0 0 1 10 10l-60 45z\' fill=\'%%23c7d2fe\'/></svg>">
        <h1>Привет! Приложение запущено 🚀</h1>
        <p>PHP %s + Nginx + MySQL в Docker</p>
        <small>Подключение к БД: %s</small>',
        htmlspecialchars(PHP_VERSION),
        htmlspecialchars($database->check())
    ));
}

function pageInfo(): string
{
    ob_start();
    phpinfo();

    return (string) ob_get_clean();
}

function pageHealth(DatabaseInterface $database): string
{
    $db = $database->check();
    $ok = !str_starts_with($db, 'ошибка');

    http_response_code($ok ? 200 : 503);
    header('Content-Type: application/json; charset=utf-8');

    return (string) json_encode(
        [
            'status' => $ok ? 'ok' : 'fail',
            'php' => PHP_VERSION,
            'database' => $db,
        ],
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
}

function pageNotFound(string $path): string
{
    return layout('404 — Не найдено', sprintf(
        '<h1>404</h1>
        <p>Страница <code>%s</code> не найдена.</p>
        <p><a href="/">Вернуться на главную</a></p>',
        htmlspecialchars($path)
    ));
}

function pageMethodNotAllowed(string $path): string
{
    return layout('405 — Метод не разрешён', sprintf(
        '<h1>405</h1>
        <p>Метод не разрешён для страницы <code>%s</code>.</p>
        <p><a href="/">Вернуться на главную</a></p>',
        htmlspecialchars($path)
    ));
}
