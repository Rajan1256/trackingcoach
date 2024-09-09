<div>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Daily questions') }}</x-slot>

            <x-slot name="actions">
                <x-button.big
                        :outline-white="!current_team()->colorIsLight()"
                        :outline-black="current_team()->colorIsLight()"
                        href="{{ route('customers.daily-questions.templates.index', [$customer]) }}">{{ __('Templates') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    @include('customers.menu')

    <div class="p-6 flow-root">
        @if($errors->any())
            <div class="pb-5">
                @foreach($errors->all() as $error)
                    <x-components::alert.error>{{ $error }}</x-components::alert.error>
                @endforeach
            </div>
        @endif

        <ul id="questionnaire-editor" wire:sortable="updateQuestionOrder" class="-mb-8">
            @forelse($questions as $question)
                <li wire:sortable.item="{{ $question->model->id }}" wire:key="question-{{ $question->model->id }}">
                    <div class="relative pb-8">
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 is-a-dash"
                              aria-hidden="true"></span>
                        <div class="relative flex space-x-3 group">
                            <div wire:sortable.handle class="cursor-move">
                                <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center ring-8 ring-white"
                                      wire:sortable.handle>
                                  <img class="h-5 w-5"
                                       src="{{ $question->getIcon() }}"/>
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 pt-1.5">
                                <div class="flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">{{ $question->model->name }}</p>
                                    </div>
                                    <div class="text-right text-sm whitespace-nowrap text-gray-500 opacity-0 group-hover:opacity-100">
                                        <x-components::grid cols="2" class="opacity-0 group-hover:opacity-100">
                                            <x-components::grid.block>
                                                <x-components::lists.button
                                                        wire:click="editQuestion({{ $question->model->id }})"
                                                        py="0" px="2">
                                                    <i class="fas fa-pencil" title="{{ __('Edit the question') }}"></i>
                                                </x-components::lists.button>
                                            </x-components::grid.block>
                                            <x-components::grid.block>
                                                <x-components::lists.button
                                                        py="0" px="2"
                                                        delete
                                                        href="{{ route('customers.daily-questions.destroy', [$customer, $question->model->id]) }}">
                                                    <i class="fas fa-trash"
                                                       title="{{ __('Remove question') }}"></i>
                                                    <x-slot name="modalMessage">{{ __('Are you sure you want to archive this question?') }}</x-slot>
                                                </x-components::lists.button>
                                            </x-components::grid.block>
                                        </x-components::grid>
                                    </div>
                                </div>
                                @if($question->model->id === $editingQuestion)
                                    <div>
                                        <x-components::form method="post"
                                                            action="{{ route('customers.daily-questions.update', [$customer, $question->model]) }}">
                                            <x-slot name="customMethod">@method('put')</x-slot>
                                            <x-components::form.fieldset>
                                                <input type="hidden" name="type"
                                                       value="{{ get_class($question) }}">
                                                <div class="rounded-lg border border-gray-300 bg-white shadow-sm px-6 py-4">
                                                    <div class=" grid grid-cols-1 divide-y gap-4 divide-dashed mb-8">

                                                        <x-components::form.group-top>
                                                            <x-components::form.label
                                                                    :small="false">{{ __("Question") }}</x-components::form.label>
                                                            <x-components::form.input name="name"
                                                                                      value="{{ $question->model->name }}">
                                                            </x-components::form.input>
                                                        </x-components::form.group-top>

                                                        @include('questions.'.$question->viewFolder().'.createOrEdit', ['question' => $question->model])
                                                    </div>

                                                    <x-components::form.button primary ml="0"
                                                                               submit>{{ __('Update question') }}</x-components::form.button>
                                                </div>

                                            </x-components::form.fieldset>
                                        </x-components::form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @empty

            @endforelse
            <li>
                <div class="relative pb-8">
                    <div class="relative flex space-x-3">
                        <div class="text-gray-700">
                                <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center ring-8 ring-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div>
                                <input type="text" class="rounded py-2 px-3 w-full" placeholder="Add a new question"
                                       wire:model="newQuestion"
                                       wire:keydown.debounce.150ms="updateTemplates()">
                            </div>

                            @if($templates->count() > 0)
                                @foreach($templates as $template)
                                    <x-components::form
                                            :buttonPadding="false"
                                            action="{{ route('customers.daily-questions.template', [$customer, $template]) }}"
                                            method="post">
                                        <x-components::form.button-group>
                                            <x-components::form.button
                                                    submit
                                                    class="mx-0 ml-0 my-2 relative col-span-1 rounded-lg border border-gray-300 bg-white shadow-sm px-6 py-4 cursor-pointer hover:border-gray-400 sm:flex sm:justify-between focus:outline-none hover:bg-white w-full">{{ $template->getLatestVersion()->name }}</x-components::form.button>
                                        </x-components::form.button-group>
                                    </x-components::form>
                                @endforeach
                            @endif

                            @if($newQuestion)
                                <div class="grid grid-cols-3 gap-2 my-2">
                                    @foreach($questionTypes as $questionType)
                                        <label class="{{ $newType == get_class($questionType) ? 'ring-1 ring-offset-2 ring-blue-500 ' : '' }}relative col-span-1 rounded-lg border border-gray-300 bg-white shadow-sm px-6 py-4 cursor-pointer hover:border-gray-400 sm:flex sm:justify-between focus:outline-none">
                                            <input type="radio" wire:model="newType" name="server-size"
                                                   value="{{ get_class($questionType) }}"
                                                   class="sr-only"
                                                   aria-labelledby="server-size-0-label"
                                                   aria-describedby="server-size-0-description-0 server-size-0-description-1">
                                            <div class="flex items-center">
                                                <div class="text-sm">
                                                    <p id="server-size-0-label" class="font-medium text-gray-900">
                                                        {{ $questionType->getName() }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="{{ $newType == get_class($questionType) ? 'border-blue-500 ' : 'border-transparent ' }}absolute -inset-px rounded-lg border-2 pointer-events-none"
                                                 aria-hidden="true"></div>
                                        </label>
                                    @endforeach
                                </div>

                                @if($newType)
                                    <x-components::form method="post"
                                                        action="{{ route('customers.daily-questions.store', $customer) }}">
                                        <x-components::form.fieldset>
                                            <input type="hidden" name="name" value="{{ $newQuestion }}">
                                            <input type="hidden" name="type" value="{{ $newType }}">
                                            <div class="rounded-lg border border-gray-300 bg-white shadow-sm px-6 py-4">
                                                <div class=" grid grid-cols-1 divide-y gap-4 divide-dashed mb-8">
                                                    @include('questions.'.$questionTypes[$newType]->viewFolder().'.createOrEdit', ['question' => null])
                                                </div>
                                                <div class="flex items-center">
                                                    <x-components::form.button primary ml="0"
                                                                               submit>{{ __('Create question') }}</x-components::form.button>
                                                    <div class="pl-3">
                                                        <x-components::form.checkbox-option
                                                                name="template"
                                                                :checked="old('template') == 1"
                                                                value="1">{{ __('Save as template') }}</x-components::form.checkbox-option>
                                                    </div>
                                                </div>
                                            </div>

                                        </x-components::form.fieldset>
                                    </x-components::form>
                                @endif
                        </div>
                        @endif

                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
