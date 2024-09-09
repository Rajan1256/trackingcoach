<div class="question col-span-6 p-6" id="question-{{ $question->model->id }}" data-id="{{ $question->model->id }}">
    <div class="questionContent">
        <h3>
            <strong>{{ isset($customer) ? replaceNames($question->model->name, $customer) : $question->model->name }}</strong>
        </h3>
        @if ( $question->model->description )
            <p class="vraagUitleg">{!! nl2br(e(isset($customer) ? replaceNames($question->model->description, $customer) : $question->model->description)) !!}</p>
        @endif

        @if ( isset($isTracker) && $isTracker )
            <div class="mg-bottom30">
                @if (isset($question->model->options['start']) )
                    <span class="uk-badge uk-badge-notification">
                        <i class="uk-icon far fa-play-circle"></i> {{ __("start") }}:
                        @if(isset($question->model->options['positive']))
                            {{ $question->model->options['start'] }}{{ $question->model->options['positive'] ? 'x yes' : 'x no' }}
                        @else
                            {{ $question->model->options['start'] }}
                        @endif
                    </span>
                @endif
                @if (isset($question->model->options['target']) )
                    <span class="uk-badge uk-badge-notification">
                        <i class="fas fa-bullseye"></i> {{ __("target") }}:
                        @if(isset($question->model->options['positive']))
                            {{ $question->model->options['target'] }}{{ $question->model->options['positive'] ? 'x yes' : 'x no' }}
                        @else
                            {{ $question->model->options['target'] }}
                        @endif
                    </span>
                @endif
            </div>

        @endif

        @include('questions.' . $question->viewFolder(). '.view')
    </div>
    <div class="h40"></div>
</div>
