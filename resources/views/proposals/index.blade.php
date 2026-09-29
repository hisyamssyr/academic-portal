<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Proposal Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold">Daftar Proposal</h3>

                        <a href="{{ route('proposals.create') }}" class="inline-flex items-center rounded-md bg-gray-800 dark:bg-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white">
                            Buat Proposal
                        </a>
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Judul</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Nilai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Dibuat</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($proposals as $proposal)
                                    <tr>
                                        <td class="px-4 py-4">
                                            <p class="font-medium">{{ $proposal->title }}</p>
                                            <p class="mt-1 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                                                {{ Str::limit($proposal->description, 100) }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <x-proposal-status :status="$proposal->status" />
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            @if ($proposal->score !== null)
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $proposal->score }}</p>
                                                @if ($proposal->feedback)
                                                    <p class="mt-1 max-w-xs text-gray-500 dark:text-gray-400">{{ $proposal->feedback }}</p>
                                                @endif
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $proposal->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center justify-end gap-3">
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Belum ada proposal.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $proposals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
