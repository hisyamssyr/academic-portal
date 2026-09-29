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
                            <dt class="text-gray-500 dark:text-gray-400">Dibuat</dt>
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

            <!-- Review Status -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold">Ubah Status Review</h3>

                    <form method="POST" action="{{ route('admin.proposals.update', $proposal) }}" class="mt-4 flex flex-wrap items-end gap-4">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" name="status" class="mt-1 block rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                @foreach (App\Enums\ProposalStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected(old('status', $proposal->status->value) === $status->value)>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <x-primary-button>Simpan Status</x-primary-button>
                    </form>
                </div>
            </div>

            <div>
                <a href="{{ route('admin.proposals.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                    &larr; Kembali ke daftar proposal
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
