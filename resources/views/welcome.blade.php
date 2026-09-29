<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Secure Academic Portal') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen flex flex-col">
            <header class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-gray-800 dark:text-gray-200">
                            <x-application-logo class="h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            {{ config('app.name', 'Secure Academic Portal') }}
                        </a>

                        <nav class="flex items-center gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-gray-800 dark:bg-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white">
                                    Register
                                </a>
                            @endauth
                        </nav>
                    </div>
                </div>
            </header>

            <main class="flex-1">
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div class="max-w-3xl">
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100">
                            Selamat datang di {{ config('app.name', 'Secure Academic Portal') }}
                        </h1>
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                            Portal akademik sederhana untuk mengelola proposal rencana proyek AI, peninjauan serta
                            pemberian nilai dan feedback proposal oleh dosen dan asisten dosen.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md bg-gray-800 dark:bg-gray-200 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white">
                                    Buka Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center rounded-md bg-gray-800 dark:bg-gray-200 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-700 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    Register
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div class="mt-12 grid gap-6 sm:grid-cols-3">
                        <div class="rounded-lg bg-white dark:bg-gray-800 p-6 shadow-sm">
                            <h2 class="font-semibold text-gray-900 dark:text-gray-100">Mahasiswa</h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Membuat dan mengelola proposal rencana proyek AI milik sendiri.
                            </p>
                        </div>
                        <div class="rounded-lg bg-white dark:bg-gray-800 p-6 shadow-sm">
                            <h2 class="font-semibold text-gray-900 dark:text-gray-100">Asisten Dosen</h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Meninjau proposal serta memberi nilai dan feedback.
                            </p>
                        </div>
                        <div class="rounded-lg bg-white dark:bg-gray-800 p-6 shadow-sm">
                            <h2 class="font-semibold text-gray-900 dark:text-gray-100">Dosen</h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Meninjau proposal serta memberi nilai dan feedback.
                            </p>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-gray-100 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-500 dark:text-gray-400">
                    {{ config('app.name', 'Secure Academic Portal') }} &copy; {{ date('Y') }}
                </div>
            </footer>
        </div>
    </body>
</html>
