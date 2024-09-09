@if($answersSelf->count())
    <h3 class="my-5">{{ __('Self') }}</h3>
    <x-components::list>
        @foreach ($answersSelf->shuffle() as $answer)
            <x-components::lists.item :truncate="false">{{ $answer->answer_text }}</x-components::lists.item>
        @endforeach
    </x-components::list>
@endif
@if($answersOthers->count())
    <h3 class="my-5">{{ __('Others') }}</h3>
    <x-components::list>
        @foreach ($answersOthers->shuffle() as $answer)
            <x-components::lists.item :truncate="false">{{ $answer->answer_text }}</x-components::lists.item>
        @endforeach
    </x-components::list>
@endif
