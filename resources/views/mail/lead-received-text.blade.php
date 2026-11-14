New {{ strtolower($label) }} from the website

@foreach($fields as $name => $value)
{{ $name }}: {{ $value }}
@endforeach

Reply to this email to answer {{ $lead->name }} directly.
Admin: {{ $adminUrl }}
