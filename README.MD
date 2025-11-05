Запуск приложения в фоне (для работы приложения обязательно нужен docker)

### Установка (если отсутствует папка vendor и node_modules)
```shell
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```
```shell
  ./vendor/bin/sail npm i
```
### Настройка alias [sail](https://laravel.su/docs/11.x/sail#nastroika-shell-psevdonimov) для удобства

### Поднятие приложения
```shell
  make up
```
Генерация секретного ключа (нужно при первом запуске)
```shell
  ./vendor/bin/sail artisan key:generate
```

### Laravel будет доступен по этому адресу:
```
http://localhost:8000
```


### Миграция бд
```shell
  ./vendor/bin/sail artisan migrate --seed
```

### Остановка приложения

````shell
  ./vendor/bin/sail stop
````

### Пример создания модели с миграцией сидером и контроллером

```shell
  ./vendor/bin/sail artisan make:model Role -msc
```

## Тестовый пользователь
| Параметр | Значение         |
|----------|------------------|
| name     | Test User        |
| email    | test@example.com |
| password | password         |

## Админ пользователь
| Параметр | Значение          |
|----------|-------------------|
| name     | Admin User        |
| email    | admin@example.com |
| password | password          |

| Параметр | Значение        |
|----------|-----------------|
| name     | admin           |
| email    | admin@admin.com |
| password | password        |

