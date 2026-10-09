<!DOCTYPE html>
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
        {$content}
    </main>
</body>
</html>