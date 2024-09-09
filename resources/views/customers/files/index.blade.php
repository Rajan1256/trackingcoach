<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Files') }}</x-slot>
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

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="p-6 bg-white border-b border-gray-200"
             x-data="{show: null, selected: 0, selectedType: null, dragging: 0, dragging_file: 0, selected_author_id: 0, user_id: {{ Auth::user()->id }}}">
            <x-components::buttons>
                <div x-show="selected != 0 && selectedType == 'folder'" class="hidden"
                     x-bind:class="{
                                '!block': selected != 0 && selectedType == 'folder',
                            }">>
                    <x-components::buttons.delete name="deleteFolder"
                                                  href=""
                                                  class="bg-red-500 text-white hover:bg-red-600 rounded-md ml-2">
                        {{ __('Delete') }}
                        <x-slot name="modalMessage">{{ __('Are you sure you want to delete the folder?') }}</x-slot>
                    </x-components::buttons.delete>
                </div>
                <div x-show="selected != 0 && selectedType == 'file' && user_id == selected_author_id"
                     class="hidden"
                     x-bind:class="{
                                    '!block': selected != 0 && selectedType == 'file' && user_id == selected_author_id,
                                }">
                    <x-components::buttons.button name="downloadFile"
                                                  class="rounded-md ml-2"
                                                  x-bind:href="'{{ route('customers.files.show', [$customer, '']) }}/' + selected">
                        {{ __('Download') }}
                    </x-components::buttons.button>
                    <x-components::buttons.delete name="deleteFile"
                                                  href="{{ route('customers.files.destroy', [$customer, '']) }}"
                                                  class="bg-red-500 text-white hover:bg-red-600 rounded-md ml-2">
                        {{ __('Delete') }}
                        <x-slot name="modalMessage">{{ __('Are you sure you want to delete the file?') }}</x-slot>
                    </x-components::buttons.delete>
                </div>
                <x-components::buttons.button
                        @click="show = (show == 'folder') ? null : 'folder'"
                        class="rounded-md ml-2">{{ __('New folder') }}</x-components::buttons.button>
                <x-components::buttons.button
                        @click="show = (show == 'file') ? null : 'file'"
                        class="rounded-md ml-2">{{ __('Upload files') }}</x-components::buttons.button>
            </x-components::buttons>

            <div x-show="show == 'folder'" class="hidden"
                 x-bind:class="{
                            '!block': show == 'folder',
                        }">
                <h2>{{ __('Create a folder') }}</h2>
                <x-components::form method="post"
                                    action="{{ route('customers.folders.store', [$customer, 'folder' => $currentFolder?->id]) }}">
                    <x-components::form.fieldset>
                        <x-components::form.group-top>
                            <x-components::form.label>{{ __('Folder name') }}</x-components::form.label>
                            <x-components::form.input name="name" id="name"></x-components::form.input>
                        </x-components::form.group-top>
                    </x-components::form.fieldset>
                    <x-components::form.button-group>
                        <x-components::form.button
                                submit>{{ __('Create new folder') }}</x-components::form.button>
                    </x-components::form.button-group>
                </x-components::form>
            </div>
            <div x-show="show == 'file'" class="hidden"
                 x-bind:class="{
                            '!block': show == 'file',
                        }">
                <x-components::form method="post"
                                    action="{{ route('customers.files.store', [$customer, 'folder' => $currentFolder?->id]) }}"
                                    enctype="multipart/form-data">
                    <x-components::form.fieldset>
                        <x-components::dropzone></x-components::dropzone>
                    </x-components::form.fieldset>
                    <x-components::form.button-group>
                        <x-components::form.button submit>{{ __('Upload files') }}</x-components::form.button>
                    </x-components::form.button-group>
                </x-components::form>
            </div>

            <x-components::grid cols="8" class="mt-5 gap-4">
                @if($currentFolder)
                    <x-components::grid.block
                            title="{{ $currentFolder->name  }}"
                            class="text-center p-1 border border-dashed border-transparent bg-indigo-200"
                            @click="selected = (selected == {{ $currentFolder->id }}) ? null : {{ $currentFolder->id }}; selectedType = ''"
                            x-bind:class="{
                                        '!border-indigo-700': selected == {{ $currentFolder->id }},
                                        '!bg-transparent': selected != {{ $currentFolder->id }},
                                    }"
                            @dblclick="window.location.href = '{{ route('customers.files.index', [$customer, 'folder' => $currentFolder->parent_id]) }}'">
                        <a class="filemanager-item folder" data-id="{{ $currentFolder->id }}">
                            <img class="icon" src="{{ asset('img/filemanager/left-arrow.svg') }}"
                                 draggable="false"/>
                            <span class="caption truncate ...">{{ __('Go back') }}</span>
                        </a>
                    </x-components::grid.block>
                @endif

                @foreach($folders as $folder)
                    <x-components::grid.block
                            title="{{ $folder->name  }}"
                            class="text-center p-1 border border-dashed border-transparent bg-indigo-200"
                            @click="selected = (selected == {{ $folder->id }}) ? null : {{ $folder->id }}; selectedType = (selected == null) ? '' : 'folder'; document.getElementById('deleteFolderForm').setAttribute('action', '{{ route('customers.folders.destroy', [$customer, $folder]) }}')"
                            x-bind:class="{
                                        '!border-indigo-700': selected == {{ $folder->id }},
                                        '!bg-transparent': selected != {{ $folder->id }},
                                    }"
                            @dblclick="window.location.href = '{{ route('customers.files.index', [$customer, 'folder' => $folder->id]) }}'">
                        <a class="filemanager-item folder"
                           data-id="{{ $folder->id }}"
                           data-double-action="{{ route('customers.files.index', [$customer, 'folder' => $folder->id]) }}"
                           data-delete-action="{{ route('customers.folders.destroy', [$customer, 'folder' => $folder]) }}"
                        >
                            <img class="icon" src="{{ asset('img/filemanager/folder.svg') }}"
                                 draggable="false"/>
                            <span class="caption truncate ...">{{ $folder->name }}</span>
                        </a>
                    </x-components::grid.block>
                @endforeach
                @foreach($files as $file)
                    <x-components::grid.block
                            title="{{ $file->file()->file_name }}"
                            class="text-center p-1 border border-dashed border-transparent bg-indigo-200"
                            @click="selected_author_id = {{ $file->author_id }}; selected = (selected == {{ $file->id }}) ? null : {{ $file->id }}; selectedType = (selected == null) ? '' : 'file'; document.getElementById('deleteFileForm').setAttribute('action', '{{ route('customers.files.destroy', [$customer, $file]) }}')"
                            x-bind:class="{
                                        '!border-indigo-700': selected == {{ $file->id }},
                                        '!bg-transparent': selected != {{ $file->id }},
                                    }"
                            @dblclick="window.location.href = '{{ route('customers.files.show', [$customer, $file->id]) }}'">
                        <a class=" filemanager-item file"
                           data-id="{{ $file->id }}"
                        >
                            <img class="icon" src="{{ $file->getIcon() }}"
                                 draggable="false"/>
                            <span class="caption">
                                        {{ substr($file->file()->file_name, 0, 10) . (strlen($file->file()->file_name) ? '...' : '') }}
                                    </span>
                        </a>
                    </x-components::grid.block>
                @endforeach
            </x-components::grid>
        </div>
    </div>

    <script>

    </script>
</x-app-layout>
