<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>{{ $title ?? 'Laravel' }}</title>


        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance

    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">

        <div class="flex flex-row gap-1.5">

            <livewire:chat.sidebar  />

                <livewire:chat.chatbox />

        </div>

        @fluxScripts
    </body>
</html>
