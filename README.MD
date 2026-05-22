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

### Настройка alias [sail](https://laravel.su/docs/11.x/sail#nastroka-shell-psevdonimov) для удобства

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

```shell
sail artisan orchid:admin admin admin@admin.com password
```
| Параметр | Значение        |
|----------|-----------------|
| name     | admin           |
| email    | admin@admin.com |
| password | password        |

## Деплой в продакшен

Этот проект использует Laravel + React + Inertia.js. Ниже описан процесс деплоя на сервер.

### Требования к серверу:

- PHP 8.2 или выше
- MySQL 8.0 или совместимая база данных
- Nginx или Apache с поддержкой перезаписи URL (mod_rewrite)
- Node.js 18+ и npm (только для компиляции фронтенда локально)

### Процесс деплоя:

#### 1. Подготовка на локальной машине:

```bash
# Скомпилировать фронтенд для продакшена
npm run build

# Установить PHP зависимости без dev-пакетов
composer install --optimize-autoloader --no-dev

# Очистить кэш (если был)
php artisan config:clear
php artisan cache:clear
```

#### 2. Загрузка файлов на сервер:

Загрузите **все файлы проекта** через FTP/SFTP/SCP, **НО**:

- ✅ Загружать: все файлы и папки проекта
- ❌ НЕ загружать: `node_modules/` (не нужна на сервере)
- ❌ НЕ загружать: `.git/` (скрытая директория Git)
- ⚠️ Создать новый файл `.env` на сервере с production настройками

- УДАЛИТЬ ФАЙЛ HOT

#### 3. Настройка .env на сервере:

Создайте файл `.env` с содержимым:

```env
APP_NAME="TJ Peregovorka"
APP_ENV=production
APP_KEY=base64:ВАШ_ГЕНЕРИРОВАННЫЙ_КЛЮЧ
APP_DEBUG=false
APP_URL=https://ваш-домен.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=имя_базы_данных
DB_USERNAME=пользователь_базы
DB_PASSWORD=пароль_базы

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

#### 4. Генерация ключа приложения:

```bash
php artisan key:generate
```

#### 5. Запуск миграций базы данных:

```bash
php artisan migrate --force
```

#### 6. Опциональная оптимизация для продакшена:

```bash
# Кэширование конфигурации и маршрутов (ускоряет работу)
php artisan config:cache
php artisan route:cache

# Если используете Blade-шаблоны (включая Orchestra Platform)
php artisan view:cache
```

#### 7. Настройка веб-сервера (Nginx):

Пример конфигурации Nginx для Laravel + Inertia.js:

```nginx
server {
    listen 80;
    server_name ваш-домен.com;
    root /path/to/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_intercept_errors on;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 8. Настройка веб-сервера (Apache):

Если используете Apache, убедитесь что включен модуль `mod_rewrite` и файл `.htaccess` в папке `public` работает
корректно:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Настройте `RewriteBase` если Laravel находится в поддиректории
    RewriteBase /

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Проверка деплоя:

1. Откройте сайт в браузере — должна загрузиться главная страница
2. Проверьте логи ошибок: `tail -f storage/logs/laravel.log`
3. Убедитесь что статические файлы (CSS/JS) загружаются из `/build/`

### Частые проблемы:

| Проблема                     | Решение                                                 |
|------------------------------|---------------------------------------------------------|
| 404 на всех страницах        | Проверьте настройки `root` в Nginx/Apache и `.htaccess` |
| Ошибка базы данных           | Проверьте параметры в `.env`, убедитесь что БД создана  |
| Страница без стилей/скриптов | Запустите `npm run build` перед деплоем                 |
| 500 ошибка                   | Проверьте логи: `storage/logs/laravel.log`              |

### Обновление проекта на сервере:

```bash
# Через Git (рекомендуется)
cd /path/to/project
git pull origin main

# Пересобрать фронтенд если были изменения
npm run build

# Установить зависимости если composer.json изменился
composer install --optimize-autoloader --no-dev

# Очистить кэш
php artisan config:clear
php artisan cache:clear

# Запустить миграции если есть новые
php artisan migrate --force
```

