@props([
    'delete' => false,
    'name' => 'confirm',
    'title' => 'Are you sure?',
    'firstButton' => 'OK',
    'secondButton' => 'Cancel',
])

@if(!$delete)
    <a {{ $attributes->merge([
                    'class' => 'font-medium text-indigo-600 hover:text-indigo-500 ml-3'
                ]) }}>
        {{ $slot }}
    </a>
@else
    <div x-data="{ {{ $name }}: false }" class="inline-block">
        <button type="button" class="font-medium text-indigo-600 hover:text-indigo-500"
                @click="{{ $name }} = !{{$name}}">{{ $slot }}</button>
        <x-components::modal.delete-confirm :name="$name" :title="$title" :firstButton="$firstButton"
                                            :secondButton="$secondButton"
                                            :action="$attributes->get('href')">
            {{ $modalMessage }}
        </x-components::modal.delete-confirm>
    </div>
@endif
