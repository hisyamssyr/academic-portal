@php($proposal = $proposal ?? null)

<div>
    <x-input-label for="title" value="Judul" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $proposal?->title)" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mt-6">
    <x-input-label for="description" value="Deskripsi" />
    <textarea id="description" name="description" rows="6" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description', $proposal?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
