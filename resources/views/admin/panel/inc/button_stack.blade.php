@php
        $buttons = $xPanel->buttons->where('stack', $stack);
@endphp

@if ($buttons->count())
        @foreach ($buttons as $button)
                @if ($button->type == 'model_function')
                        @if ($stack == 'line')
                                {!! $entry->{$button->content}($xPanel, $entry); !!}
                        @else
                                {!! $xPanel->model->{$button->content}($xPanel); !!}
                        @endif
                @else
                        @include($button->content)
                @endif
        @endforeach
@endif

@if (
        $stack === 'line'
        && isset($entry)
        && method_exists($entry, 'impersonateInLineButton')
        && !$buttons->contains(fn ($button) => $button->name === 'impersonate')
)
        {!! $entry->impersonateInLineButton($xPanel, $entry); !!}

@endif
