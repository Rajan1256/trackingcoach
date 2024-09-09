@props([
    'download' => false,
    'confirm' => false,
    'delete' => false,
    'name' => 'confirm',
    'title' => 'Are you sure?',
    'firstButton' => 'OK',
    'secondButton' => 'Cancel',
    'disabled' => false,
])

@if ($download)
    <a {{ $attributes->merge(['class' => 'mx-2 text-indigo-600 hover:text-indigo-900']) }} download>{{ $slot }}</a>
@elseif($confirm)
    <div x-data="{ {{ $name }}: false }" class="inline-block">
        <button type="button"
                title="{{ $attributes->get('tooltip') }}"
                class="mx-2 {{ $disabled ? 'text-gray-400 hover:text-gray-400' : 'text-indigo-600 hover:text-indigo-900' }}"
                {{ $disabled ? 'disabled' : '' }}
                @click="{{ $name }} = !{{$name}}">{{ $slot }}</button>
        <x-components::modal.confirm :name="$name" :title="$title" :firstButton="$firstButton"
                                     :secondButton="$secondButton"
                                     :action="$attributes->get('href')">
            {{ $modalMessage }}
        </x-components::modal.confirm>
    </div>
@elseif($delete)
    <div x-data="{ {{ $name }}: false }" class="inline-block">
        <button type="button"
                title="{{ $attributes->get('tooltip') }}"
                class="mx-2 {{ $disabled ? 'text-gray-400 hover:text-gray-400' : 'text-indigo-600 hover:text-indigo-900' }}"
                {{ $disabled ? 'disabled' : '' }}
                @click="{{ $name }} = !{{$name}}">{{ $slot }}</button>
        <x-components::modal.delete-confirm :name="$name" :title="$title" :firstButton="$firstButton"
                                            :secondButton="$secondButton"
                                            :action="$attributes->get('href')">
            {{ $modalMessage }}
        </x-components::modal.delete-confirm>
    </div>
@else
    <a {{ $attributes->merge(['class' => 'mx-2 text-indigo-600 hover:text-indigo-900']) }}>{{ $slot }}</a>
@endif
