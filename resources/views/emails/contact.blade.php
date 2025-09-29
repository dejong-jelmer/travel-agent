@extends('emails.default')

@section('content')
    <h3>Contact formulier bericht</h3>
    <p>Naam: {{ $contact->name }}</p>
    <p>Email: {{ $contact->email }}</p>
    @if($contact->phone)
        <p>Telefoonbnummer:{{ $contact->phone }}</p>
    @endif
    <p>Met het volgende bericht:</p>
    <p>{{ $contact->text }}</p>
@endsection
