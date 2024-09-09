@props([
    'defaultRows' => 3,
    'defaultClass' => 'shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border border-gray-300 rounded-md',
    'translatable' => false,
    'languages' => array_keys(config('trackingcoach.languages')),
    'defaultLanguage' => Auth::user()?->locale ?? 'en',
    'translations' => '',
])

@if($translatable)
    <x-slot name="input">
        @foreach($languages as $language)
            <textarea name="{{ $attributes->get('name') }}[{{ $language }}]" {{ $attributes->merge([
                'class' => $defaultClass . ' ' . (($defaultLanguage !== $language) ? 'hidden' : ''),
                'rows' => $defaultRows,
            ]) }}>{{ $translations[$language] ?? '' }}</textarea>
        @endforeach
    </x-slot>
@else
    <x-slot name="input">
        <textarea {{ $attributes->merge([
            'class' => $defaultClass,
            'rows' => $defaultRows,
        ]) }}>{{ $slot }}</textarea>
    </x-slot>
@endif
