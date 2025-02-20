<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/Pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <div class="preloader load-only">
        <div class="preloader__row">
            <div class="preloader__item"></div>
            <div class="preloader__item"></div>
        </div>
    </div>
<style class="load-only">
    .preloader {
        /*фиксированное позиционирование*/
        position: fixed;
        /* координаты положения */
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        /* фоновый цвет элемента */
        background: rgba(2, 2, 2, 0.69);
        /* размещаем блок над всеми элементами на странице (это значение должно быть больше, чем у любого другого позиционированного элемента на странице) */
        z-index: -1;
    }

    .preloader__row {
        position: relative;
        top: 50%;
        left: 50%;
        width: 70px;
        height: 70px;
        margin-top: -35px;
        margin-left: -35px;
        text-align: center;
        animation: preloader-rotate 2s infinite linear;
    }

    .preloader__item {
        position: absolute;
        display: inline-block;
        top: 0;
        background-color: #b76133;
        border-radius: 100%;
        width: 35px;
        height: 35px;
        animation: preloader-bounce 2s infinite ease-in-out;
    }

    .preloader__item:last-child {
        top: auto;
        bottom: 0;
        animation-delay: -1s;
    }

    @keyframes preloader-rotate {
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes preloader-bounce {

        0%,
        100% {
            transform: scale(0);
        }

        50% {
            transform: scale(1);
        }
    }

    .loaded_hiding .preloader {
        transition: 0.3s opacity;
        opacity: 0;
    }

    .loaded .preloader {
        display: none;
    }
</style>


    <body class="font-sans antialiased">
        @inertia
    </body>

    <script class="load-only">
        window.addEventListener('load', function() {
            let loadOnlyElements = document.querySelectorAll('.load-only');
            loadOnlyElements.forEach(function(element) {
                element.remove();
            });
        });
    </script>
</html>
