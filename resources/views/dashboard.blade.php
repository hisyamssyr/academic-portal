<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- User Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Masuk sebagai</p>
                    <p class="mt-1 text-lg font-semibold">{{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ Auth::user()->email }}</p>
                    <span class="mt-3 inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-900 px-3 py-1 text-xs font-medium text-indigo-800 dark:text-indigo-300">
                        {{ Auth::user()->role->label() }}
                    </span>
                </div>
            </div>

            @if (Auth::user()->role === \App\Enums\UserRole::Mahasiswa)
                <!-- Student Proposals -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <h3 class="text-lg font-semibold">Proposal Saya</h3>

                            <a href="{{ route('proposals.create') }}" class="inline-flex items-center rounded-md bg-gray-800 dark:bg-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white">
                                Buat Proposal
                            </a>
                        </div>

                        @if ($proposals->isEmpty())
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                Belum ada proposal. Mulai dengan membuat proposal rencana proyek AI.
                            </p>
                        @else
                            <ul class="mt-4 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($proposals as $proposal)
                                    <li class="py-4 flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <p class="font-medium">{{ $proposal->title }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Dibuat {{ $proposal->created_at->diffForHumans() }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <x-proposal-status :status="$proposal->status" />

                                            @can('update', $proposal)
                                                <a href="{{ route('proposals.edit', $proposal) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    Edit
                                                </a>
                                            @endcan

                                            @can('submit', $proposal)
                                                <form method="POST" action="{{ route('proposals.submit', $proposal) }}">
                                                    @csrf
                                                    @method('patch')

                                                    <button type="submit" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                                        Ajukan
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @else
                <!-- Staff Shortcuts -->
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold">Panel Admin</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Pantau proposal mahasiswa dan data nilai.
                            </p>
                            <a href="{{ route('admin.dashboard') }}" class="mt-4 inline-flex items-center rounded-md bg-gray-800 dark:bg-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white">
                                Buka Panel Admin
                            </a>
                        </div>
                    </div>

                    @can('input-nilai')
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <h3 class="text-lg font-semibold">Input Nilai</h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Berikan nilai dan feedback untuk mahasiswa.
                                </p>
                                <a href="{{ route('admin.grades.create') }}" class="mt-4 inline-flex items-center rounded-md bg-gray-800 dark:bg-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white">
                                    Input Nilai
                                </a>
                            </div>
                        </div>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
