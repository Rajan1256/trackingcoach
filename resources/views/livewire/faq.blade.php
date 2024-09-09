<div>
    <x-slot name="header">
        <x-page-header>
            {{ __('FAQ') }}

            <x-slot name="actions">
                @can('create', App\Models\Faq::class)
                    <x-button.big
                            :outline-white="!current_team()->colorIsLight()"
                            :outline-black="current_team()->colorIsLight()"
                            href="{{ route('faq.manage.index') }}">{{ __('Manage FAQ') }}</x-button.big>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="p-6 faq">
        <x-components::inputs.input wire:model="filters.search" placeholder="{{ __('Search by title or answer') }}"
                                    class="mb-3"/>

        <x-components::accordion>
            @foreach($faqs as $faq)
                <x-components::accordion.item :title="$faq->question">
                    {!! $faq->answer !!}
                </x-components::accordion.item>
            @endforeach
        </x-components::accordion>

        @if ($faqs->count() === 0)
            {{ __('No FAQ available yet.') }}
        @endif
    </div>
</div>
</div>
