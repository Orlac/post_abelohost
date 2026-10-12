# post_abelohost

```
cp ./.env.example ./.env
```

// Запуск контейнера
```
docker compose up -d --build
```

// Применить миграции
```
docker exec -it post-app php ./bin/migrate up
```

// Откатить миграции, если нужно
```
docker exec -it post-app php ./bin/migrate down
```

// Выполнить сид
```
docker exec -it post-app php ./bin/seed
```

// Ссылка на главную страницу:
```
http://localhost:8081
```

// Команда для входа в контейнер
```
docker exec -it post-app bash
```

AI агенты: claude (помощь при создании контейнера), kilo.ai (выполнение рутинных задач, создание каркаса компонентов, рефакторинг)
