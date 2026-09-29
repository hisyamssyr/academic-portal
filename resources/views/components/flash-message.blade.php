@php
    $status = session('status');

    $messages = [
        'proposal-created' => 'Proposal berhasil dibuat dan disimpan sebagai draft.',
        'proposal-updated' => 'Proposal berhasil diperbarui.',
        'proposal-submitted' => 'Proposal berhasil diajukan untuk ditinjau.',
        'proposal-reviewed' => 'Review berhasil disimpan. Nilai dan feedback sudah tercatat.',
    ];
@endphp

@if ($status && isset($messages[$status]))
    <div {{ $attributes->merge(['class' => 'rounded-md bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 p-4 text-sm font-medium text-green-700 dark:text-green-300']) }}>
        {{ $messages[$status] }}
    </div>
@endif
