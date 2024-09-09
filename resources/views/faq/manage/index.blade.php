<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Manage FAQ') }}

            <x-slot name="actions">
                @can('create', App\Models\Faq::class)
                    <x-button.big
                            :outline-white="!current_team()->colorIsLight()"
                            :outline-black="current_team()->colorIsLight()"
                            href="{{ route('faq.manage.create') }}">{{ __('New FAQ') }}</x-button.big>
                @endcan
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('faq') }}">
                    {{ __('Go back') }}</x-button.big>
            </x-slot>
        </x-page-header>

    </x-slot>

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <x-components::table>
        <x-components::table.header>
            <x-components::table.heading>{{ __('Question') }}</x-components::table.heading>
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        @forelse($faq as $f)
            <x-components::table.row :iteration="$loop->iteration">
                <x-components::table.column>{{ $f->question }}</x-components::table.column>
                <x-components::table.column actions>
                    <x-components::table.action
                            href="{{ route('faq.manage.edit', [$f]) }}">{{ __('Edit') }}</x-components::table.action>
                    <x-components::table.action delete
                                                href="{{ route('faq.manage.destroy', [$f]) }}">
                        {{ __('Delete') }}
                        <x-slot name="modalMessage">{{ __('Are you sure you want to delete the faq?') }}</x-slot>
                    </x-components::table.action>
                </x-components::table.column>
            </x-components::table.row>
        @empty
            <x-components::table.row>
                <x-components::table.column
                        colspan="4" class="text-center italic">
                    {{ __('No FAQ yet') }}
                </x-components::table.column>
            </x-components::table.row>
        @endforelse
    </x-components::table>
</x-app-layout>
