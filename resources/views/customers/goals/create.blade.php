<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Add mini-survey goal') }}</x-slot>
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
    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="px-6 bg-white border-b border-gray-200">

            <x-components::form action="{{ route('customers.goals.store', [$customer]) }}" method="post">
                <x-components::form.fieldset-top>
                    @foreach(config('trackingcoach.languages') as $short => $full)
                        <x-components::form.group-top>
                            <x-components::form.label required
                                                      for="name[{{ $short }}]">{{ __('Goal (:lang)', ['lang' => \Illuminate\Support\Str::upper($short)])  }}</x-components::form.label>
                            <x-components::form.input id="name[{{ $short }}]" name="name[{{ $short }}]"/>
                        </x-components::form.group-top>
                    @endforeach
                </x-components::form.fieldset-top>
                <x-components::form.button-group>
                    <x-components::form.button primary submit>
                        {{ __('Save') }}
                    </x-components::form.button>
                </x-components::form.button-group>
            </x-components::form>
        </div>
    </div>
</x-app-layout>
