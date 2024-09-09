<div {{ $attributes->merge([
    'class' => 'bg-white'
]) }}>
    {{ $intro ?? '' }}
    <div class="overflow-x-auto w-full">
        <table class="divide-y divide-gray-200 min-w-full">
            {{ $slot }}
        </table>
    </div>
</div>
