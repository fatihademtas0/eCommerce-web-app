<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Inverizo' }}</title>
        <livewire:styles />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-200 dark:bg-slate-700">
        <main>
            {{ $slot }}
        </main>

        <livewire:scripts />
    </body>
</html>
