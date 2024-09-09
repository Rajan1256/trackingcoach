<div>
    <x-slot name="header">
        <x-page-header>
            {{ __('Customers') }}
            <x-slot name="actions">
                @if (!$archived)
                    <x-button.big href="{{ route('customers.archived') }}" id="archived-customers"
                                  :outline-white="!current_team()->colorIsLight()"
                                  :outline-black="current_team()->colorIsLight()">
                        {{ __('Archived customers') }}
                    </x-button.big>
                @else
                    <x-button.big href="{{ route('customers') }}" :outline-white="!current_team()->colorIsLight()"
                                  :outline-black="current_team()->colorIsLight()">
                        {{ __('Active customers') }}
                    </x-button.big>
                @endif
                @if (current_team()->maxCustomers() > current_team()->users()->isCustomer()->count())
                    <x-button.big href="{{ route('customers.create') }}" id="button-customers"
                                  :outline-white="!current_team()->colorIsLight()"
                                  :outline-black="current_team()->colorIsLight()">
                        + {{ __('Add customer') }}
                    </x-button.big>
                @else
                    <x-button.big
                            :href="Auth::user()->hasCurrentTeamRole([App\Enum\Roles::ADMIN]) ? route('billing.portal') : '#'"
                            :outline-white="!current_team()->colorIsLight()"
                            :outline-black="current_team()->colorIsLight()">
                        + {{ __('Add customer') }}
                    </x-button.big>
                @endif
            </x-slot>
        </x-page-header>
    </x-slot>

    <x-slot name="sidebar">
        @include('includes.sidebar.welcome-back')
        @include('customers.sidebar')
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session()->has('message'))
                <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
            @endif

            <x-components::inputs.input wire:model="filters.search" placeholder="{{ __('Search by name or email') }}"
                                        class="mb-3"/>

            @if (!$showGrid)
                @foreach($customers->groupBy(fn($user) => Str::of($user->first_name)->substr(0,1)->__toString()) as $grouped)
                    <div class="border-t border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
                        {{ Str::of($grouped->first()->first_name)->substr(0,1) }}
                    </div>

                    <div>
                        @foreach($grouped as $customer)
                            <div class="group relative px-6 py-5 flex items-center space-x-3 hover:bg-gray-50 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full"
                                         src="{{ \Creativeorange\Gravatar\Facades\Gravatar::get($customer->email) }}"
                                         alt="">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('customers.show', $customer) }}" class="focus:outline-none">
                                        <!-- Extend touch target to entire panel -->
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $customer->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ $customer->getSettings()->company_name }}
                                        </p>
                                    </a>
                                </div>
                                <div>
                                    <x-components::grid :cols="(config('app.env') === 'local' ? 3 : 2) + ($archived ? 1 : 0)"
                                                        class="opacity-0 group-hover:opacity-100 gap-3">
                                        @if (\Illuminate\Support\Facades\Route::has('letmein') && config('app.env') === 'local')
                                            <x-components::grid.block>
                                                <x-components::lists.button
                                                        method="PUT"
                                                        href="{{ route('letmein', [$customer]) }}">
                                                    <i class="fas fa-lock-open-alt" title="{{ __('Login as') }}"></i>
                                                </x-components::lists.button>
                                            </x-components::grid.block>
                                        @endif
                                        <x-components::grid.block>
                                            <x-components::lists.button
                                                    method="PUT"
                                                    href="{{ route('customers.edit', [$customer]) }}">
                                                <i class="fas fa-user-edit" title="{{ __('Edit the user') }}"></i>
                                            </x-components::lists.button>
                                        </x-components::grid.block>
                                        <x-components::grid.block>
                                            @if(!$archived)
                                                <x-components::lists.button
                                                        delete href="{{ route('customers.destroy', [$customer]) }}">
                                                    <i class="fas fa-user-times"
                                                       title="{{ __('Archive customer') }}"></i>
                                                    <x-slot name="modalMessage">{{ __('Are you sure you want to archive the customer?') }}</x-slot>
                                                </x-components::lists.button>
                                            @else
                                                <x-components::lists.button
                                                        delete href="{{ route('customers.destroy', [$customer]) }}">
                                                    <i class="fas fa-user-check"
                                                       title="{{ __('Restore customer') }}"></i>
                                                    <x-slot name="modalMessage">{{ __('Are you sure you want to restore the customer?') }}</x-slot>
                                                </x-components::lists.button>
                                            @endif
                                        </x-components::grid.block>
                                        @if($archived)
                                            <x-components::grid.block>
                                                <x-components::lists.button
                                                        delete href="{{ route('customers.destroy', [$customer, 'force' => 1]) }}">
                                                    <i class="fas fa-user-times"
                                                       title="{{ __('Delete customer') }}"></i>
                                                    <x-slot name="modalMessage">{{ __('Are you sure you want to delete the customer?') }}</x-slot>
                                                </x-components::lists.button>
                                            </x-components::grid.block>
                                        @endif
                                    </x-components::grid>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
                {{ $customers->links() }}
            @else
                <x-components::list cards>
                    @foreach($customers->sortBy('first_name') as $customer)
                        @php
                            $href = '';
                            if (\Illuminate\Support\Facades\Route::has('letmein')) {
                                $href = "<a href='" . route('letmein', $customer) ."'><i class='fas fa-lock-open-alt'></i></a>";
                            }
                        @endphp
                        <x-components::lists.card-with-2-buttons :header="$customer->name"
                                                                 :subHeader="(config('app.env') === 'local') ? $href : ''"
                                                                 :img="$customer->gravatar"
                                                                 href="{{ route('customers.show', $customer) }}">
                            <x-slot name="firstButton">
                                <x-components::lists.button
                                        method="PUT"
                                        href="{{ route('customers.edit', [$customer]) }}">
                                    <i class="fas fa-user-edit" title="{{ __('Edit the user') }}"></i>
                                </x-components::lists.button>
                            </x-slot>
                            <x-slot name="secondButton">
                                @if(!$archived)
                                    <x-components::lists.button
                                            delete href="{{ route('customers.destroy', [$customer]) }}">
                                        <i class="fas fa-user-times" title="{{ __('Archive customer') }}"></i>
                                        <x-slot name="modalMessage">{{ __('Are you sure you want to archive the customer?') }}</x-slot>
                                    </x-components::lists.button>
                                @else
                                    <x-components::lists.button
                                            delete href="{{ route('customers.destroy', [$customer]) }}">
                                        <i class="fas fa-user-check" title="{{ __('Restore customer') }}"></i>
                                        <x-slot name="modalMessage">{{ __('Are you sure you want to restore the customer?') }}</x-slot>
                                    </x-components::lists.button>
                                @endif
                            </x-slot>
                        </x-components::lists.card-with-2-buttons>
                    @endforeach
                </x-components::list>
            @endif
        </div>
    </div>
</div>
