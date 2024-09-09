@props([
    'name' => null,
    'data' => []
])


<x-slot name="input">
    <div x-data="{{ $name }}Handler()" class="mt-3">
        <div class="gap-y-3 grid">
            <template x-for="(field, index) in {{ $name }}Fields" :key="index">
                <x-components::grid cols="12">
                    <x-components::grid.block colSpan="1" x-text="index + 1 + '.'"
                                              class="p-3"></x-components::grid.block>
                    <x-components::grid.block colSpan="10">
                        {!! $input !!}
                    </x-components::grid.block>
                    <x-components::grid.block colSpan="1" class="flex items-center justify-center"
                                              x-show="{{ $name }}Fields.length > 1">
                        <i class="fas fa-times text-red-600 cursor-pointer" @click="remove{{ $name }}Field(index)"></i>
                    </x-components::grid.block>
                </x-components::grid>
            </template>
        </div>
        <x-components::form.button class="ml-0 my-3"
                                   @click="addNew{{ $name }}Field()">{{ __('Add') }}</x-components::form.button>
    </div>
    <script type="text/javascript">
        function {{ $name }}Handler() {
            const data = @json(array_map(fn($e) => ['value' => $e], (array) $data));

            return {
                {{ $name }}Fields: data,
                addNew{{ $name }}Field() {
                    this.{{ $name }}Fields.push({
                        value: ''
                    });
                },
                remove{{ $name }}Field(index) {
                    this.{{ $name }}Fields.splice(index, 1);
                }
            }
        }
    </script>
</x-slot>
