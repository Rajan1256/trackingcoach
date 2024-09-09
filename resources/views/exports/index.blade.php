<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Export') }}
            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-plus"
                              href="{{ route('exports.create') }}">
                    {{ __('Create new export') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <x-components::table>
        <x-components::table.header>
            <x-components::table.heading>{{ __('Year') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Customer') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Created at') }}</x-components::table.heading>
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        <x-components::table.body>
            @foreach($exports as $export)
                <x-components::table.row :iteration="$loop->iteration">
                    <x-components::table.column
                            class="!whitespace-normal">{{ $export->year }}</x-components::table.column>
                    <x-components::table.column
                            class="!whitespace-normal">{{ $export->user?->name }}</x-components::table.column>
                    <x-components::table.column
                            class="!whitespace-normal">{{ date_format_helper($export->created_at)->get_hidmy() }}</x-components::table.column>
                    <x-components::table.column actions>
                        @if ($export->status === 1)
                            <x-components::table.action
                                    href="{{ route('exports.show', [$export]) }}">{{ __('Download') }}</x-components::table.action>
                        @else
                            <x-components::table.action
                                    href="#">{{ __('Processing') }}</x-components::table.action>
                        @endif
                    </x-components::table.column>
                </x-components::table.row>
            @endforeach
        </x-components::table.body>
    </x-components::table>
    <div class="p-6">
        {{ $exports->links() }}
    </div>
</x-app-layout>
