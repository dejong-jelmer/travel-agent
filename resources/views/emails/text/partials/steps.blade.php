{{--
    Plain text counterpart of emails.partials.steps.
    Expects: $steps (array of [title, description] pairs, or plain strings).
--}}
@foreach ($steps as $stepIndex => $step)
@if (is_array($step))
{!! $stepIndex + 1 !!}. {!! $step[0] !!}
   {!! $step[1] !!}
@else
{!! $stepIndex + 1 !!}. {!! $step !!}
@endif
@endforeach
