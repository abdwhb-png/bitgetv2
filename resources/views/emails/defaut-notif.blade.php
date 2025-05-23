<x-mail::message>
# {!! $title !!}

{!! $body !!}

<x-mail::button :url="$url">
{{ $btn_text }}
</x-mail::button>

Kindly,<br>
{{ config('app.name') }} team
</x-mail::message>
