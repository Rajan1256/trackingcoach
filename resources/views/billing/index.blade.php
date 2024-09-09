<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Billing Portal') }}
            <x-slot name="actions">
                <x-button.big href="{{ route('billing.stripe.portal') }}"
                              :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()">
                    {{ __('Manage billing details') }}
                </x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    @if(current_team()->subscribed() && current_team()->users()->isCustomer()->count() === current_team()->maxCustomers())
        <x-components::alert.warning>
            {{ __('You have reached the maximum amount of customers for your current plan. Switch plans to continue adding customers.') }}
        </x-components::alert.warning>
    @endif

    <div class="bg-white" x-data="{billingPeriod: 'monthly'}">
        <div class="max-w-7xl mx-auto pb-16 px-4 sm:px-6 lg:px-8">
            <div>
                <div class="flex flex-row sm:align-center">
                    <h1 class="text-3xl flex-1 tracking-tight font-bold text-gray-900 mt-8">{{ __('Pricing Plans') }}</h1>

                    <div class="relative mt-6 rounded-lg bg-gray-100 p-0.5 sm:mt-8">
                        <button type="button" x-on:click="billingPeriod = 'monthly'" class="relative w-1/2 whitespace-nowrap rounded-md border sm:w-auto sm:px-8 focus:z-10 py-2 text-sm font-medium" x-bind:class="billingPeriod === 'monthly' ? 'border-gray-200 bg-white text-gray-900 shadow-sm' : 'ml-0.5 border-transparent text-gray-700'">Monthly billing</button>
                        <button type="button" x-on:click="billingPeriod = 'yearly'" class="relative w-1/2 whitespace-nowrap rounded-md border sm:w-auto sm:px-8 focus:z-10 py-2 text-sm font-medium" x-bind:class="billingPeriod === 'yearly' ? 'border-gray-200 bg-white text-gray-900 shadow-sm' : 'ml-0.5 border-transparent text-gray-700'">Yearly billing</button>
                    </div>
                </div>
                <div class="mt-12 space-y-4 sm:mt-16 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-6 lg:max-w-4xl lg:mx-auto xl:max-w-none xl:mx-0 xl:grid-cols-4" x-show="billingPeriod === 'monthly'">
                    @foreach(config('cashier.plans') as $plan)
                        <div class="border border-gray-200 rounded-lg shadow-sm divide-y divide-gray-200 @if($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count() && current_team()->paymentMethods()->count() !== 0 && !current_team()->subscribedToPrice($plan['monthly_id'])) opacity-25 @endif @if(current_team()->subscribedToPrice($plan['monthly_id'])) scale-110 bg-[#fef5d7]@endif">
                            <div class="p-6">
                                <h2 class="text-lg leading-6 font-medium text-gray-900">{{ $plan['name'] }}</h2>
                                <p class="mt-4 text-sm text-gray-500">{!! $plan['short_description'] !!}</p>
                                <div class="mt-8 flex flex-col">
                                    @if(isset($plan['cost_monthly']))
                                        <div class="flex items-center">
                                            <span class="text-4xl tracking-tight font-bold text-gray-900">{{ $plan['cost_monthly'] }}</span>
                                            <span class="text-base font-medium text-gray-500 ml-2">/ {{ __('month') }}</span>
                                            @if(isset($plan['cost_coach']))
                                                <span class="flex-1"></span>
                                                <span class="text-2xl font-medium text-gray-500 text-right">+</span>
                                            @endif
                                        </div>
                                        @if(isset($plan['cost_coach']))
                                            <div class="flex items-center">
                                                <span class="text-xl tracking-tight font-bold text-gray-900">{{ $plan['cost_coach'] }}</span>
                                                <span class="text-sm font-medium text-gray-500 ml-2">/ {{ __('month') }}</span>
                                                <span class="text-sm font-medium text-gray-500 ml-2">/ {{ __('additional coach') }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <span class="text-2xl tracking-tight font-bold text-gray-900">&nbsp;</span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-4xl tracking-tight font-bold text-gray-900">{{ __('Contact us') }}</span>
                                        <div class="flex items-center">
                                            <span class="text-2xl tracking-tight font-bold text-gray-900">&nbsp;</span>
                                        </div>
                                    @endif
                                </div>
                                @if(isset($plan['cost_monthly']))
                                    @if(current_team()->subscribedToPrice($plan['monthly_id']))
                                        <x-button.big :class="implode(' ', [
                                                    'mt-8 block w-full',
                                                    ($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count()) ? 'hover:!bg-white hover:!text-black hover:!cursor-default' : '',
                                                ])"
                                                href="#"
                                                :outline-black="true">
                                            {{ __('Current plan') }}
                                        </x-button.big>
                                    @else
                                        <x-button.big :class="implode(' ', [
                                                    'mt-8 block w-full',
                                                    ($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count()) ? 'pointer-events-none opacity-25' : '',
                                                ])"
                                                :href="($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count()) ? '#' : route('billing.subscribe', ['plan' => $plan['monthly_id']])"
                                                :outline-black="true">
                                              @if($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count())
                                                {{ __('Too many customers') }}
                                              @elseif(current_team()->subscribed())
                                                {{ __('Switch to :plan', ['plan' => $plan['name']]) }}
                                              @else
                                                {{ __('Subscribe to :plan', ['plan' => $plan['name']]) }}
                                              @endif
                                        </x-button.big>
                                    @endif
                                @else
                                    <x-button.big :class="implode(' ', [
                                                     'mt-8 block w-full',
                                                 ])"
                                                  href="mailto:info@trackingcoach.com"
                                                :outline-black="true">
                                          {{ __('Contact us') }}
                                    </x-button.big>
                                @endif
    {{--                            <a href="#" class="mt-8 block w-full bg-gray-800 border border-gray-800 rounded-md py-2 text-sm font-semibold text-white text-center hover:bg-gray-900"></a>--}}
                            </div>
                            <div class="pt-6 pb-8 px-6">
                                <h3 class="text-sm font-medium text-gray-900">{{ __("What's included") }}</h3>
                                <ul role="list" class="mt-6 space-y-4">
                                    @foreach($plan['features'] as $feature)
                                        <li class="flex space-x-3">
                                            <!-- Heroicon name: solid/check -->
                                            <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm text-gray-500">{{ __($feature) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 space-y-4 sm:mt-16 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-6 lg:max-w-4xl lg:mx-auto xl:max-w-none xl:mx-0 xl:grid-cols-4" x-show="billingPeriod === 'yearly'">
                    @foreach(config('cashier.plans') as $plan)
                        <div class="border border-gray-200 rounded-lg shadow-sm divide-y divide-gray-200 @if($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count() && current_team()->paymentMethods()->count() !== 0 && !current_team()->subscribedToPrice($plan['monthly_id'])) opacity-25 @endif @if(current_team()->subscribedToPrice($plan['monthly_id'])) scale-110 bg-[#fef5d7]@endif">
                            <div class="p-6">
                                <h2 class="text-lg leading-6 font-medium text-gray-900">{{ $plan['name'] }}</h2>
                                <p class="mt-4 text-sm text-gray-500">{!! $plan['short_description'] !!}</p>
                                <div class="mt-8 flex flex-col">
                                    @if(isset($plan['cost_yearly']))
                                        <div class="flex items-center">
                                            <span class="text-4xl tracking-tight font-bold text-gray-900">{{ $plan['cost_yearly'] }}</span>
                                            <span class="text-base font-medium text-gray-500 ml-2">/ {{ __('year') }}</span>
                                            @if(isset($plan['cost_coach_yearly']))
                                                <span class="flex-1"></span>
                                                <span class="text-2xl font-medium text-gray-500 text-right">+</span>
                                            @endif
                                        </div>
                                        @if(isset($plan['cost_coach_yearly']))
                                            <div class="flex items-center">
                                                <span class="text-xl tracking-tight font-bold text-gray-900">{{ $plan['cost_coach_yearly'] }}</span>
                                                <span class="text-sm font-medium text-gray-500 ml-2">/ {{ __('year') }}</span>
                                                <span class="text-sm font-medium text-gray-500 ml-2">/ {{ __('additional coach') }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <span class="text-2xl tracking-tight font-bold text-gray-900">&nbsp;</span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-4xl tracking-tight font-bold text-gray-900">{{ __('Contact us') }}</span>
                                        <div class="flex items-center">
                                            <span class="text-2xl tracking-tight font-bold text-gray-900">&nbsp;</span>
                                        </div>
                                    @endif
                                </div>
                                @if(isset($plan['cost_yearly']))
                                    @if(current_team()->subscribedToPrice($plan['yearly_id']))
                                        <x-button.big :class="implode(' ', [
                                                    'mt-8 block w-full',
                                                    ($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count()) ? 'hover:!bg-white hover:!text-black hover:!cursor-default' : '',
                                                ])"
                                                href="#"
                                                :outline-black="true">
                                            {{ __('Current plan') }}
                                        </x-button.big>
                                    @else
                                        <x-button.big :class="implode(' ', [
                                                    'mt-8 block w-full',
                                                    ($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count()) ? 'pointer-events-none opacity-25' : '',
                                                ])"
                                                :href="($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count()) ? '#' : route('billing.subscribe', ['plan' => $plan['yearly_id']])"
                                                :outline-black="true">
                                              @if($plan['options']['max_customers'] <= current_team()->users()->isCustomer()->count())
                                                {{ __('Too many customers') }}
                                              @elseif(current_team()->subscribed())
                                                {{ __('Switch to :plan', ['plan' => $plan['name']]) }}
                                              @else
                                                {{ __('Subscribe to :plan', ['plan' => $plan['name']]) }}
                                              @endif
                                        </x-button.big>
                                    @endif
                                @else
                                    <x-button.big :class="implode(' ', [
                                                     'mt-8 block w-full',
                                                 ])"
                                                  href="mailto:info@trackingcoach.com"
                                                :outline-black="true">
                                          {{ __('Contact us') }}
                                    </x-button.big>
                                @endif
    {{--                            <a href="#" class="mt-8 block w-full bg-gray-800 border border-gray-800 rounded-md py-2 text-sm font-semibold text-white text-center hover:bg-gray-900"></a>--}}
                            </div>
                            <div class="pt-6 pb-8 px-6">
                                <h3 class="text-sm font-medium text-gray-900">{{ __("What's included") }}</h3>
                                <ul role="list" class="mt-6 space-y-4">
                                    @foreach($plan['features'] as $feature)
                                        <li class="flex space-x-3">
                                            <!-- Heroicon name: solid/check -->
                                            <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm text-gray-500">{{ __($feature) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
