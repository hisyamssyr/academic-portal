<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Input Nilai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.grades.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="student_id" value="Mahasiswa" />
                            <select id="student_id" name="student_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                                        {{ $student->name }} ({{ $student->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="course" value="Mata Kuliah" />
                            <select id="course" name="course" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course }}" @selected(old('course') === $course)>
                                        {{ $course }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="score" value="Nilai (0-100)" />
                            <x-text-input id="score" name="score" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('score')" required />
                            <x-input-error :messages="$errors->get('score')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="feedback" value="Feedback" />
                            <textarea id="feedback" name="feedback" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('feedback') }}</textarea>
                            <x-input-error :messages="$errors->get('feedback')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <x-primary-button>Simpan Nilai</x-primary-button>

                            <a href="{{ route('admin.grades.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
