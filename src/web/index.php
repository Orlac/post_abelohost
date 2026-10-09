<?php
$dbStatus = 'ok';
try {
    new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s', getenv('DB_HOST'), getenv('DB_PORT'), getenv('DB_DATABASE')),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
} catch (Throwable $e) {
    $dbStatus = 'ошибка: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post App</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f6fb; color: #222;
               display: flex; flex-direction: column; align-items: center; margin-top: 8vh; }
        img { width: 220px; height: 220px; }
        small { color: #666; }
    </style>
</head>
<body>
    <img alt="Логотип" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'><rect width='200' height='200' rx='36' fill='%234f46e5'/><path d='M40 70l60 45 60-45v70a10 10 0 0 1-10 10H50a10 10 0 0 1-10-10z' fill='white'/><path d='M40 60a10 10 0 0 1 10-10h100a10 10 0 0 1 10 10l-60 45z' fill='%23c7d2fe'/></svg>">
    <h1>Привет! Приложение запущено 🚀</h1>
    <p>PHP <?= htmlspecialchars(PHP_VERSION) ?> + Nginx + MySQL в Docker</p>
    <small>Подключение к БД: <?= htmlspecialchars($dbStatus) ?></small>
</body>
</html>
