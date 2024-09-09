@props(['primary', 'outline-white', 'outline-black', 'icon'])

@if(isset($primary))
    @php($classes = 'ml-2 w-full text-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500')
@elseif(isset($outlineWhite) && (!isset($outlineBlack) || (bool) $outlineWhite === true))
    @php($classes = 'ml-2 w-full text-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white bg-white bg-opacity-0 hover:bg-opacity-100 hover:text-black transition duration-200')
@elseif(isset($outlineBlack) && (!isset($outlineWhite) || (bool) $outlineBlack === true))
    @php($classes = 'ml-2 w-full text-center px-6 py-3 border border-black text-base font-medium rounded-md shadow-sm text-black bg-black bg-opacity-0 hover:bg-opacity-100 hover:text-white transition duration-200')
@else
    @php($classes = '')
@endif

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <i class="{{ $icon }} mr-1.5"></i>
    @endif
    {{ $slot }}
</a>
