<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Daftar Nilai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold">Nilai Mahasiswa</h3>

                        @can('input-nilai')
                            <a href="{{ route('admin.grades.create') }}" class="inline-flex items-center rounded-md bg-gray-800 dark:bg-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white">
                                Input Nilai
                            </a>
                        @endcan
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Mahasiswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Mata Kuliah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Nilai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Pemberi Nilai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Feedback</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($grades as $grade)
                                    <tr>
                                        <td class="px-4 py-4">
                                            <p class="font-medium">{{ $grade->student->name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $grade->student->email }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-sm">{{ $grade->course }}</td>
                                        <td class="px-4 py-4 font-semibold">{{ $grade->score }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $grade->grader->name }}
                                        </td>
                                        <td class="px-4 py-4 max-w-xs text-sm text-gray-500 dark:text-gray-400">
                                            {{ $grade->feedback ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $grade->created_at->format('d M Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Belum ada nilai.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $grades->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
