@props([
    'delete' => false,
    'confirm' => false,
    'disabled' => false,
    'name' => 'confirm',
    'title' => 'Are you sure?',
    'firstButton' => 'OK',
    'secondButton' => 'Cancel',
    'method' => 'POST',
    'py' => 4,
    'px' => 0
])

@if($delete)
    <div x-data="{ {{ $name }}: false }" class="inline-block w-full">
        <button type="button"
                {{ $disabled ? 'disabled' : '' }}
                class="w-full relative flex-1 inline-flex items-center justify-center py-{{ $py }} px-{{ $px }} text-sm font-medium border border-transparent rounded-br-lg {{ $disabled ? 'text-gray-200' : 'text-gray-700 hover:text-gray-500 ' }}"
                @click="{{ $name }} = !{{$name}}">
            {{ $slot }}
        </button>
        <x-components::modal.delete-confirm :name="$name" :title="$title" :firstButton="$firstButton"
                                            :secondButton="$secondButton"
                                            :action="$attributes->get('href')">
            {{ $modalMessage }}
        </x-components::modal.delete-confirm>
    </div>
@elseif($confirm)
    <div x-data="{ {{ $name }}: false }" class="inline-block w-full">
        <button type="button"
                {{ $disabled ? 'disabled' : '' }}
                class="w-full relative flex-1 inline-flex items-center justify-center py-{{ $py }} px-{{ $px }} text-sm font-medium border border-transparent rounded-br-lg {{ $disabled ? 'text-gray-200' : 'text-gray-700 hover:text-gray-500 ' }}"
                @click="{{ $name }} = !{{$name}}">
            {{ $slot }}
        </button>
        <x-components::modal.confirm :name="$name" :title="$title" :firstButton="$firstButton"
                                     :secondButton="$secondButton"
                                     :action="$attributes->get('href')"
                                     :method="$method">
            {{ $modalMessage }}
        </x-components::modal.confirm>
    </div>
@else
    <a {{ $attributes->merge([
                    'class' => "relative w-0 flex-1 inline-flex items-center justify-center py-{$py} px-{$px} text-sm text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500"
                ]) }}>
        {{ $slot }}
    </a>
@endif
