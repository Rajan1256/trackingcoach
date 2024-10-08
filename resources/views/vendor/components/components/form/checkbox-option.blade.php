@props([
    'id' => 'form-'.Str::random(),
    'name' => '',
    'value' => '',
    'checked' => false,
])

<div class="relative flex items-start">
    <div class="flex items-center h-5">
        <input id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" type="checkbox"
               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
               @if($checked) checked @endif>
    </div>
    <div class="ml-3 text-sm">
        <label for="{{ $id }}" class="font-medium text-gray-700">
            {{ $slot }}
        </label>
    </div>
</div>
