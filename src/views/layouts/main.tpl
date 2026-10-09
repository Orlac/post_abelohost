<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
    <script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
    <title>{$title}</title>
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