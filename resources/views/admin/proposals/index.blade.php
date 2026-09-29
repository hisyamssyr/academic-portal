<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Review Proposal
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.proposals.index') }}" class="rounded-md px-3 py-1.5 text-sm font-medium {{ $currentStatus === '' ? 'bg-gray-800 text-white dark:bg-gray-200 dark:text-gray-800' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            Semua
                        </a>

                        @foreach ($statuses as $status)
                            <a href="{{ route('admin.proposals.index', ['status' => $status->value]) }}" class="rounded-md px-3 py-1.5 text-sm font-medium {{ $currentStatus === $status->value ? 'bg-gray-800 text-white dark:bg-gray-200 dark:text-gray-800' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                {{ $status->label() }}
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Judul</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Mahasiswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Diajukan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($proposals as $proposal)
                                    <tr>
                                        <td class="px-4 py-4 font-medium">{{ $proposal->title }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <p>{{ $proposal->user->name }}</p>
                                            <p>{{ $proposal->user->email }}</p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <x-proposal-status :status="$proposal->status" />
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $proposal->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('admin.proposals.show', $proposal) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Tinjau
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Tidak ada proposal untuk filter ini.
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
