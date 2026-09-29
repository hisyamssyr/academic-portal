@props(['status'])

@php
    $classes = match ($status) {
        \App\Enums\ProposalStatus::Draft => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        \App\Enums\ProposalStatus::Submitted => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        \App\Enums\ProposalStatus::Revised => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
        \App\Enums\ProposalStatus::Reviewed => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$classes]) }}>
    {{ $status->label() }}
</span>
