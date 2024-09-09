@props([
    'required' => false,
    'optional' => false,
    'small' => true,
    'translatable' => false,
    'languages' => array_keys(config('trackingcoach.languages')),
    'defaultLanguage' => Auth::user()?->locale ?? 'en',
])

<x-slot name="label">
    <div class="flex justify-between mt-1">
        <label name="{{ $attributes->get('for') }}Label" {{ $attributes->merge(['class' => 'block font-medium text-gray-700 sm:mt-px sm:pt-2 font-bold' . ($small ? ' text-sm' : '')]) }}>
            {{ $slot }}
            @if($translatable)
                <span class="language">{{ Str::upper($defaultLanguage) }}</span>
            @endif
            @if($required)
                <span class="text-red-600">*</span>
            @endif
            @if($optional)
                <span class="italic text-xs text-gray-500">({{ __('optional') }})</span>
            @endif
        </label>

        @if($translatable)
            <script type="text/javascript">
                @foreach ($languages as $language)
                document.addEventListener('click', (e) => {
                    if (e.target.getAttribute('name') !== "change{{ Str::ucfirst($attributes->get('for')) }}Language['{{ $language }}']") {
                        return;
                    }

                    @foreach ($languages as $l)
                    document.getElementsByName("{{ $attributes->get('for') }}[{{ $l }}]").forEach((input) => {
                        input.classList.add('hidden');
                    });
                    document.getElementsByName("change{{ Str::ucfirst($attributes->get('for')) }}Language['{{ $l }}']").forEach((button) => {
                        button.classList.remove('!bg-blue-600');
                        button.classList.remove('!text-white');
                        button.classList.remove('hover:!bg-blue-700');
                    });
                    @endforeach
                    e.target.classList.add('!bg-blue-600');
                    e.target.classList.add('!text-white');
                    e.target.classList.add('hover:!bg-blue-700');

                    document.getElementsByName("{{ $attributes->get('for') }}Label")[0].getElementsByClassName('language')[0].innerHTML = '{{ Str::upper($language) }}';
                    let input = document.getElementsByName("{{ $attributes->get('for') }}[{{ $language }}]")[0];
                    input.classList.remove('hidden');
                });
                @endforeach
            </script>

            <div>
                @foreach ($languages as $language)
                    <button name="change{{ Str::ucfirst($attributes->get('for')) }}Language['{{ $language }}']"
                            class="ml-1 inline-flex justify-center py-1 px-2 shadow-sm text-sm font-medium rounded-md text-black border border-gray-300 bg-white hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 @if ($language === $defaultLanguage) !bg-blue-600 !text-white hover:!bg-blue-700 @endif"
                            type="button">{{ Str::upper($language) }}</button>
                @endforeach
            </div>
        @endif
    </div>
</x-slot>
