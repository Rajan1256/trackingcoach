@props([
    'image' => ''
])
<div {{ $attributes->merge([
    'class' => 'w-full h-80'
]) }} style="background-image: url('{{ $image }}'); background-repeat: no-repeat; background-size: cover; ">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ $slot }}
    </div>
</div>
