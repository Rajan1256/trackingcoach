@props([
    'id' => 'input-' . Str::random(),
    'name' => '',
    'value' => '',
    'checked' => false,
    'required' => false,
])

<div {{ $attributes->merge([
    'class' => "flex items-center"
]) }}>
    <input id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" type="radio" @if($required) required @endif @if($checked) checked @endif
    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
    @if($slot->isNotEmpty())
        <label for="{{ $id }}" class="ml-3 block text-sm font-medium text-gray-700">{{ $slot }}</label>
    @endif
</div>
