@props([
    'buttonPadding' => true
])

<form {{ $attributes }} onsubmit="disableFormButtons(this)">
    @csrf
    {{ $customMethod ?? '' }}
    @if(!empty($intro) || !empty($fieldset))
        <div>
            {{ $intro ?? '' }}

            @if(!empty($fieldset))
                <div class="mt-6 sm:mt-5 space-y-6 sm:space-y-5">
                    {{ $fieldset }}
                </div>
            @endif
        </div>
    @endif
    @if(!empty($hidden))
        {{ $hidden }}
    @endif
    @if (!empty($buttons))
        <div @if($buttonPadding) class="py-6" @endif>
            <div class="flex justify-end">
                {{ $buttons }}
            </div>
        </div>
    @endif
</form>
