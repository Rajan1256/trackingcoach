<section>
    <div class="bg-white overflow-hidden shadow">
        <div class="bg-white p-6">
            <div class="sm:flex sm:items-center sm:justify-between">
                @if (current_team()->maxCustomers() > current_team()->users()->isCustomer()->count())
                    <x-button.big href="{{ route('customers.create') }}" primary>
                        {{ __('Create new customer') }}
                    </x-button.big>
                @else
                    <x-button.big
                            :href="Auth::user()->hasCurrentTeamRole([App\Enum\Roles::ADMIN]) ? route('billing.portal') : '#'"
                            primary>
                        {{ __('Create new customer') }}
                    </x-button.big>
                @endif
            </div>
        </div>
    </div>
</section>
