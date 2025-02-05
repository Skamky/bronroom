Запуск приложения в фоне

```shell
sail up -d &&
sail npm run dev
```

остановка

````shell
sail stop
````

предварительно настройка [sail](https://laravel.su/docs/11.x/sail#nastroika-shell-psevdonimov)

создание модели с миграцией сидером и контроллером

```shell
sail artisan make:model Role -msc
```

| Параметр | Значение         |
|----------|------------------|
| name     | Test User        |
| email    | test@example.com |
| password | password         |


