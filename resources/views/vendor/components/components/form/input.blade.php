@props([
    'prefix' => '',
    'suffix' => '',
    'translatable' => false,
    'languages' => array_keys(config('trackingcoach.languages')),
    'defaultLanguage' => Auth::user()?->locale ?? 'en',
    'translations' => '',
])

@if($translatable)
    <x-slot name="input">
        @foreach($languages as $language)
            <input name="{{ $attributes->get('name') }}[{{ $language }}]"
                   {{ $attributes->merge([
                   'class' => 'flex-1 block w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 sm:text-sm border-gray-300 ' . ((empty($prefix)) ? 'rounded-r-md' : '') . ' ' . ((empty($suffix)) ? 'rounded-l-md' : '') . ' ' . (($defaultLanguage !== $language) ? 'hidden' : ''),
                   'type' => 'text'
               ]) }}
                   value="{{ $translations[$language] ?? '' }}">
        @endforeach
    </x-slot>
@else
    <x-slot name="input">
        <div class="flex rounded-md shadow-sm">
            @if(!empty($prefix))
                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">{{ $prefix }}</span>
            @endif

            <input {{ $attributes->merge([
            'class' => 'flex-1 block w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 sm:text-sm border-gray-300 ' . ((empty($prefix)) ? 'rounded-r-md' : '') . ' ' . ((empty($suffix)) ? 'rounded-l-md' : ''),
            'type' => 'text'
        ]) }}>

            @if(!empty($suffix))
                <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">{{ $suffix }}</span>
            @endif
        </div>
    </x-slot>
@endif
