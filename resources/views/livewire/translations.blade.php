<div>
    <x-slot name="header">
        <x-page-header>
            {{ __('Translations') }}
        </x-page-header>
    </x-slot>

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-components::inputs.input wire:model="filters.search" placeholder="{{ __('Translation text') }}"
                                        class="mb-3"/>
        </div>
    </div>

     <x-components::table>
        <x-components::table.header>
            @foreach(config('trackingcoach.languages') as $short => $full)
                <x-components::table.heading>{{ __($full) }}</x-components::table.heading>
            @endforeach
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        <x-components::table.body>
            @foreach($lines as $line)
                <x-components::table.row :iteration="$loop->iteration">
                    @foreach(config('trackingcoach.languages') as $short => $full)
                        <x-components::table.column
                                class="!whitespace-normal">{{ $line->getTranslation($short) }}</x-components::table.column>
                    @endforeach
                    <x-components::table.column actions>
                        <x-components::table.action
                                href="{{ route('translations.edit', [$line]) }}">{{ __('Edit') }}</x-components::table.action>
                    </x-components::table.column>
                </x-components::table.row>
            @endforeach
        </x-components::table.body>
    </x-components::table>
    <div class="p-6">
        {{ $lines->links() }}
    </div>
</div>
