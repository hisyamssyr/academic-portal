<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Statistics -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Proposal</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalProposals }}</p>
                    </div>
                </div>

                @foreach ($proposalCounts as $status => $count)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ \App\Enums\ProposalStatus::from($status)->label() }}</p>
                            <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $count }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Nilai</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalGrades }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Proposals -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold">Proposal Terbaru</h3>

                        <a href="{{ route('admin.proposals.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            Lihat semua proposal
                        </a>
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Judul</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Mahasiswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($recentProposals as $proposal)
                                    <tr>
                                        <td class="px-4 py-4 font-medium">{{ $proposal->title }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $proposal->user->name }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <x-proposal-status :status="$proposal->status" />
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('admin.proposals.show', $proposal) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Tinjau
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Belum ada proposal.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
