<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
        </x-page-header>
    </x-slot>
    @include('customers.menu')
    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif


    <div class="p-6">
        <x-components::accordion>
            <x-components::accordion.item :title="__('Supporters')" itemClasses="px-8">
                <x-components::table>
                    <x-components::table.header>
                        <x-components::table.heading>{{ __('Name') }}</x-components::table.heading>
                        <x-components::table.heading>{{ __('Notification method') }}</x-components::table.heading>
                        <x-components::table.heading></x-components::table.heading>
                    </x-components::table.header>
                    <x-components::table.body>
                        @forelse($customer->supporters as $supporter)
                            <x-components::table.row :iteration="$loop->iteration">
                                <x-components::table.column>
                                    <div class="flex flex-row items-center">
                                        <div class="flex-1">
                                            @can('create', App\Models\Review::class)
                                                @if ( $f = $review->answers->where('supporter_id', $supporter->id)->first() )
                                                    <i class="fas fa-check text-green-600 mr-2"
                                                       title="{{ $f->created_at->format('d-m-Y H:i') }}"></i>
                                                @else
                                                    <i class="far fa-ellipsis-h text-yellow-700 mr-2"></i>
                                                @endif
                                            @endcan
                                        </div>
                                        <div class="flex-grow">
                                            <strong class="block">
                                                {{ $supporter->name }}
                                            </strong>
                                            <em class="block italic">
                                                {{ ucfirst($supporter->relationship) }}
                                            </em>
                                        </div>
                                    </div>

                                </x-components::table.column>

                                <x-components::table.column>
                                    @if ( $supporter->notification_method == 'both' )
                                        <i class="fas fa-envelope-open-text"></i> {{ $supporter->email }}, <br/>
                                        <i class="fas fa-phone"></i> {{ $supporter->phone }}
                                    @elseif( $supporter->notification_method == 'mail')
                                        <i class="fas fa-envelope-open-text"></i> {{ $supporter->email }}
                                    @else
                                        <i class="fas fa-phone"></i> {{ $supporter->phone }}
                                    @endif
                                </x-components::table.column>
                                <x-components::table.column actions>
                                    @if(!empty($f))
                                        <x-components::table.action
                                                :href="route('customers.reviews.detail', [$customer, $review, $supporter])">
                                            {{ __('Show') }}
                                        </x-components::table.action>
                                    @endif

                                    @if ( $review->answers->where('supporter_id', $supporter->id)->count() > 0 )
                                        <x-components::table.action disabled
                                                                    confirm
                                                                    tooltip="{{ __('Supporter already responded') }}">
                                            {{ __('Send invitation') }}
                                            <x-slot name="modalMessage">{{ __('Are you sure you want to send the invite to the supporter?') }}</x-slot>
                                        </x-components::table.action>
                                    @elseif ($review->opens_at > \Carbon\Carbon::now() )
                                        <x-components::table.action disabled
                                                                    confirm
                                                                    tooltip="{{ __('Review not open yet') }} ({{ $review->opens_at->format('d-m-Y') }})">
                                            {{ __('Send invitation') }}
                                            <x-slot name="modalMessage">{{ __('Are you sure you want to send the invite to the supporter?') }}</x-slot>
                                        </x-components::table.action>
                                    @elseif ($review->closes_at < \Carbon\Carbon::now() )
                                        <x-components::table.action disabled
                                                                    confirm
                                                                    tooltip="{{ __('Review already closed') }} ({{ $review->closes_at->format('d-m-Y') }})">
                                            {{ __('Send invitation') }}
                                            <x-slot name="modalMessage">{{ __('Are you sure you want to send the invite to the supporter?') }}</x-slot>
                                        </x-components::table.action>
                                    @elseif ( $invite = $review->reviewInvitations->where('supporter_id', $supporter->id)->where('created_at', '>', \Carbon\Carbon::now()->subDays(3) )->first() )
                                        <x-components::table.action disabled
                                                                    confirm
                                                                    tooltip="{{ __('Supporter already notified in the past 3 days') }} ({{ $invite->created_at->format('d-m-Y H:i') }})">
                                            {{ __('Send invitation') }}
                                            <x-slot name="modalMessage">{{ __('Are you sure you want to send the invite to the supporter?') }}</x-slot>
                                        </x-components::table.action>
                                    @else
                                        <x-components::table.action
                                                confirm
                                                :href="route('customers.reviews.sendInvitation', [$customer, $review, $supporter])">
                                            {{ __('Send invitation') }}
                                            <x-slot name="modalMessage">{{ __('Are you sure you want to send the invite to the supporter?') }}</x-slot>
                                        </x-components::table.action>
                                    @endif
                                </x-components::table.column>
                            </x-components::table.row>
                        @empty
                            <x-components::table.row>
                                <x-components::table.column
                                        colspan="5">{{ __('No supporters yet') }}</x-components::table.column>
                            </x-components::table.row>
                        @endforelse
                    </x-components::table.body>
                </x-components::table>
            </x-components::accordion.item>
        </x-components::accordion>

        @foreach($report->get('raw') as $raw)
            <h3 class="my-5"><strong>{{ replaceNames($raw['question']->name, $customer) }}</strong></h3>
            @if ( $goal = \App\Models\Goal::find($raw['question']->options->get('goal')) )
                <p>
                    <strong>{{ ucfirst(trans('general.goal')) }}:</strong> {{ $goal->name }}
                </p>
            @endif

            @if ( is_array($raw['graph']) && count($raw['graph']) == 2)
                @include($raw['graph'][0], $raw['graph'][1])
            @endif
        @endforeach
    </div>
</x-app-layout>
