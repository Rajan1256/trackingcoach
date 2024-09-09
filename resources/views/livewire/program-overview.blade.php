<div>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Program overview') }}</x-slot>

        </x-page-header>
    </x-slot>

    @include('customers.menu')

    <div class="p-6">
        <div class="container">
            <div class="flex flex-col md:grid text-gray-800">
                @foreach($milestones as $milestone)
                    <div class="flex md:contents">
                        <div class="mr-10 md:mx-auto relative">
                            @if($milestones->count() > 1)
                                <div class="h-full w-6 flex {{ $loop->first ? 'items-end ' : '' }}{{ !$loop->first && !$loop->last ? 'items-center ' : '' }}{{ $loop->last ? 'items-top ' : '' }} justify-center">
                                    <div class="{{ $loop->first || $loop->last ? 'h-1/2' : 'h-full' }} w-1 {{ $milestone->finished() ? 'bg-green-500' : 'bg-gray-300' }} pointer-events-none"></div>
                                </div>
                            @endif
                            <div class="w-6 h-6 absolute top-1/2 -mt-3 rounded-full {{ $milestone->finished() ? 'bg-green-500' : 'bg-gray-300' }} text-center">
                                @if($milestone->finished())
                                    <i class="fas fa-check text-white"></i>
                                @else
                                    <i class="fas fa-dot text-white"></i>
                                @endif
                            </div>
                        </div>
                        <div class="col-start-2 col-end-12 p-4 mr-auto w-full group">
                            <h3 class="font-semibold text-lg mb-1">
                                {{ $milestone->title }}
                            </h3>
                            <div class="leading-tight text-justify w-full text-sm text-gray-600">
                                <i class="fal fa-calendar mr-1"></i> {{ date_format_helper($milestone->date)->get_dmy() }}
                                @can('delete', $milestone)
                                    <div x-data="{ confirm : false }" class="inline-block">
                                        <button type="button"
                                                class="text-red-600 px-2 md:hidden group-hover:inline-block"
                                                @click="confirm = !confirm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <x-components::modal.delete-confirm name="confirm"
                                                                            :title="__('Delete milestone')"
                                                                            :firstButton="__('OK')"
                                                                            :secondButton="__('Cancel')"
                                                                            wire:submit.prevent="deleteMilestone({{ $milestone->id }})">
                                            {{ __('Are you sure you want to delete this milestone?') }}
                                        </x-components::modal.delete-confirm>
                                    </div>
                                @endcan

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>


        @can('create', \App\Models\ProgramMilestone::class)
            <h2 class=" {{ $milestones->count() ? 'mt-8 ' : ''}}mb-2 text-lg font-bold">{{ __('Add new milestone') }}</h2>

            <form class="grid grid-cols-7 gap-3" wire:submit.prevent="save">
                <div class="flex-1 col-span-3">
                    <input type="date" wire:model="newMilestoneDate" class="rounded py-2 px-3 w-full">
                </div>

                <div class="flex-1 col-span-3">
                    <input type="text" wire:model="newMilestoneTitle" class="rounded py-2 px-3 w-full"
                           placeholder="{{ __('Milestone name') }}">
                </div>
                <div>
                    <button type="submit"
                            class="h-full bg-blue-600 hover:bg-blue-700 text-white w-full rounded">
                        <i class="fal fa-plus-circle mr-1"></i>
                        Add
                    </button>
                </div>
            </form>


            @error('newMilestoneDate')
            <div class="text-red-600 text-sm font-bold">{{ $message }}</div> @enderror
            @error('newMilestoneTitle')
            <div class="text-red-600 text-sm font-bold">{{ $message }}</div> @enderror
        @endcan

    </div>

</div>
