<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Mini-survey') }}</x-slot>
        </x-page-header>

        <x-slot name="actions">
            <x-button.big
                    :outline-white="!current_team()->colorIsLight()"
                    :outline-black="current_team()->colorIsLight()"
                    href="{{ route('customers.reviews.questions.templates.index', [$customer]) }}">{{ __('Templates') }}</x-button.big>
        </x-slot>
    </x-slot>


    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    @include('customers.menu')
    <div class="p-6">
        <x-components::table>
            <x-components::table.intro>
                <x-slot name="title">{{ __('Periods') }}</x-slot>
                <x-components::intro.button-group>
                    @can('create', App\Models\Review::class)
                        <x-components::intro.button
                                :href="route('customers.reviews.create', [$customer])">{{ __('New review period') }}</x-components::intro.button>
                    @endcan
                </x-components::intro.button-group>
            </x-components::table.intro>
            <x-components::table.header>
                <x-components::table.heading>{{ __('Review name') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Opens at') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Closes at') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Results visible at') }}</x-components::table.heading>
                <x-components::table.heading></x-components::table.heading>
            </x-components::table.header>
            <x-components::table.body>
                @forelse($reviews->reverse() as $review)
                    <x-components::table.row :iteration="$loop->iteration">
                        <x-components::table.column>{{ $review->name }}</x-components::table.column>
                        <x-components::table.column>{{ $review->opens_at->format('d-m-Y') }}</x-components::table.column>
                        <x-components::table.column>{{ $review->closes_at->format('d-m-Y') }}</x-components::table.column>
                        <x-components::table.column>{{ $review->visible_at->format('d-m-Y') }}</x-components::table.column>
                        <x-components::table.column actions>
                            @can('view', $review)
                                <x-components::table.action
                                        href="{{ route('customers.reviews.show', [$customer, $review]) }}">{{ __('Show') }}</x-components::table.action>
                            @endcan
                            @can('update', $review)
                                <x-components::table.action
                                        href="{{ route('customers.reviews.edit', [$customer, $review]) }}">{{ __('Edit') }}</x-components::table.action>
                            @endcan
                            @can('delete', $review)
                                <x-components::table.action
                                        href="{{ route('customers.reviews.destroy', [$customer, $review]) }}"
                                        delete>
                                    {{ __('Delete') }}
                                    <x-slot name="modalMessage">{{ __('Are you sure you want to delete the review?') }}</x-slot>
                                </x-components::table.action>
                            @endcan
                        </x-components::table.column>
                    </x-components::table.row>
                @empty
                    <x-components::table.row>
                        <x-components::table.column colspan="5">
                            {{ __("No reviews yet") }}.
                            <a href="{{ route('customers.reviews.create', [$customer]) }}">
                                {{ __("Create one") }}.
                            </a>
                        </x-components::table.column>
                    </x-components::table.row>
                @endforelse
            </x-components::table.body>
        </x-components::table>

        <x-components::table>
            <x-components::table.intro>
                <x-slot name="title">{{ __('Supporters') }}</x-slot>
                <x-components::intro.button-group>
                    <x-components::intro.button
                            href="{{ route('customers.supporters.create', [$customer]) }}">{{ __('Add supporter') }}</x-components::intro.button>
                </x-components::intro.button-group>
            </x-components::table.intro>
            <x-components::table.header>
                <x-components::table.heading>{{ __('Name') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Notification to') }}</x-components::table.heading>
                <x-components::table.heading></x-components::table.heading>
            </x-components::table.header>
            <x-components::table.body>
                @forelse($customer->supporters as $supporter)
                    <x-components::table.row :iteration="$loop->iteration">
                        <x-components::table.column>{{ $supporter->name }}</x-components::table.column>
                        <x-components::table.column>
                            @if ( $supporter->notification_method == 'both' )
                                <i class="fas fa-envelope-open-text"></i> {{ $supporter->email }}
                                ,<br/><i class="fas fa-phone"></i> {{ $supporter->phone }}
                            @elseif( $supporter->notification_method == 'mail')
                                <i class="fas fa-envelope-open-text"></i> {{ $supporter->email }}
                            @else
                                <i class="fas fa-phone"></i> {{ $supporter->phone }}
                            @endif
                        </x-components::table.column>
                        <x-components::table.column actions>
                            <x-components::table.action
                                    href="{{ route('customers.supporters.edit', [$customer, $supporter]) }}">{{ __('Edit') }}</x-components::table.action>
                            <x-components::table.action
                                    href="{{ route('customers.supporters.destroy', [$customer, $supporter]) }}"
                                    delete>
                                {{ __('Delete') }}
                                <x-slot name="modalMessage">{{ __('Are you sure you want to delete the supporter?') }}</x-slot>
                            </x-components::table.action>
                        </x-components::table.column>
                    </x-components::table.row>
                @empty
                    <x-components::table.row>
                        <x-components::table.column colspan="3">
                            {{ __("No supporters yet") }}.
                        </x-components::table.column>
                    </x-components::table.row>
                @endforelse
            </x-components::table.body>
        </x-components::table>

        <x-components::table>
            <x-components::table.intro>
                <x-slot name="title">{{ __('Goals') }}</x-slot>
                <x-components::intro.button-group>
                    <x-components::intro.button
                            href="{{ route('customers.goals.create', [$customer]) }}">{{ __('Add goal') }}</x-components::intro.button>
                </x-components::intro.button-group>
            </x-components::table.intro>
            <x-components::table.body>
                @forelse($customer->goals as $goal)
                    <x-components::table.row :iteration="$loop->iteration">
                        <x-components::table.column>{{ $goal->getTranslation('name', Auth::user()->locale) }}</x-components::table.column>
                        <x-components::table.column actions>
                            <x-components::table.action
                                    href="{{ route('customers.goals.edit', [$customer, $goal]) }}">{{ __('Edit') }}</x-components::table.action>
                            <x-components::table.action
                                    href="{{ route('customers.goals.destroy', [$customer, $goal]) }}"
                                    delete>
                                {{ __('Delete') }}
                                <x-slot name="modalMessage">{{ __('Are you sure you want to delete the goal?') }}</x-slot>
                            </x-components::table.action>
                        </x-components::table.column>
                    </x-components::table.row>
                @empty
                    <x-components::table.row>
                        <x-components::table.column colspan="2">
                            {{ __("No goals yet") }}.
                        </x-components::table.column>
                    </x-components::table.row>
                @endforelse
            </x-components::table.body>
        </x-components::table>

        <div class="bg-white px-4 py-5 sm:px-6">
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                <div class="ml-4 mt-4 w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('Questionnaire') }}
                    </h3>
                </div>
            </div>
        </div>
        @livewire('review-questionnaire', ['customer' => $customer])
    </div>
</x-app-layout>
