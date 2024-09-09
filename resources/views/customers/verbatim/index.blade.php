<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Verbatims') }}</x-slot>
        </x-page-header>
    </x-slot>

    @include('customers.menu')
    <div>
        <x-components::table>
            <x-components::table.body>
                @php($week = null)
                @forelse($verbatims as $verbatim)
                    @if ($verbatim->date->format('oW') !== $week)
                        @php($week = $verbatim->date->format('oW'))
                        <x-components::table.row class="border-b-8">
                            <x-components::table.column colspan="4" class="pb-2 pt-12 text-xl">
                                {{ __('Week :number - :year', ['number' => $verbatim->date->format('W'), 'year' => $verbatim->date->format('o')]) }}
                            </x-components::table.column>
                        </x-components::table.row>
                    @endif
                    <x-components::table.row :iteration="$loop->iteration">
                        <x-components::table.column>
                            <div class="items-center text-center">
                                <div>
                                    {{ date_format_helper($verbatim->date)->get_dmy() }}
                                </div>
                                <div class="pt-1">
                                    <x-components::badges.basic :color="scoreToColorClass($verbatim->score)">
                                        {{ $verbatim->score }}
                                    </x-components::badges.basic>
                                </div>
                            </div>
                        </x-components::table.column>
                        <x-components::table.column class="!whitespace-normal">
                            {{ $verbatim->answer_text }}
                        </x-components::table.column>
                        <x-components::table.column>
                            <x-components::table.action
                                    href="{{ route('customers.history.show', [$customer, $verbatim->date->format('Y-m-d')]) }}">
                                {{ __('Show') }}
                            </x-components::table.action>
                        </x-components::table.column>
                    </x-components::table.row>
                @empty
                    <x-components::table.row>
                        <x-components::table.column
                                colspan="4">{{ __('No verbatims yet') }}</x-components::table.column>
                    </x-components::table.row>
                @endforelse
            </x-components::table.body>
        </x-components::table>
        <div class="p-6">
            {{ $verbatims->links() }}
        </div>
    </div>
</x-app-layout>
