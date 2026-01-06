<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-50 via-white to-cyan-50 dark:from-gray-950 dark:via-gray-900 dark:to-indigo-950">

        <div class="w-full sm:max-w-md px-8 py-10 bg-white dark:bg-gray-800 shadow-2xl shadow-indigo-200/50 dark:shadow-none overflow-hidden sm:rounded-3xl border border-gray-100 dark:border-gray-700">
            {{ $slot }}
        </div>

        <div class="mt-8 text-center text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>

</html>