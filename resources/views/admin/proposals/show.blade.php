<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Proposal
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <h3 class="text-lg font-semibold">{{ $proposal->title }}</h3>

                        <x-proposal-status :status="$proposal->status" />
                    </div>

                    <p class="mt-4 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $proposal->description }}</p>

                    <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Diajukan</dt>
                            <dd class="mt-1">{{ $proposal->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Terakhir diperbarui</dt>
                            <dd class="mt-1">{{ $proposal->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Student Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold">Informasi Mahasiswa</h3>

                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Nama</dt>
                            <dd class="mt-1">{{ $proposal->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1">{{ $proposal->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Peran</dt>
                            <dd class="mt-1">{{ $proposal->user->role->label() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if ($proposal->reviewed_at !== null)
                <!-- Review Result -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold">Hasil Review</h3>

                        <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Nilai</dt>
                                <dd class="mt-1 text-2xl font-semibold">{{ $proposal->score }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Direview oleh</dt>
                                <dd class="mt-1">{{ $proposal->grader?->name ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-gray-500 dark:text-gray-400">Feedback</dt>
                                <dd class="mt-1 whitespace-pre-line">{{ $proposal->feedback ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Tanggal review</dt>
                                <dd class="mt-1">{{ $proposal->reviewed_at?->format('d M Y H:i') ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Review Form -->
            @can('input-nilai')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold">{{ $proposal->isReviewed() ? 'Koreksi Review' : 'Review Proposal' }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Menyimpan review akan mengubah status proposal menjadi <strong>Reviewed</strong>.
                        </p>

                        <form method="POST" action="{{ route('admin.proposals.review', $proposal) }}" class="mt-4 space-y-6">
                            @csrf
                            @method('patch')

                            <div class="grid gap-6 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="score" value="Nilai Proposal (0-100)" />
                                    <x-text-input id="score" name="score" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('score', $proposal->score)" required />
                                    <x-input-error :messages="$errors->get('score')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="feedback" value="Feedback" />
                                    <textarea id="feedback" name="feedback" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('feedback', $proposal->feedback) }}</textarea>
                                    <x-input-error :messages="$errors->get('feedback')" class="mt-2" />
                                </div>
                            </div>

                            <x-primary-button>Simpan Review</x-primary-button>
                        </form>
                    </div>
                </div>
            @endcan

            <div>
                <a href="{{ route('admin.proposals.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                    &larr; Kembali ke daftar proposal
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
