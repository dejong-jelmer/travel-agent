@extends('emails.default')

@section('content')
    <h3>Er is een boeking binnengekomen.</h3>
    <p>Voor: {{ $product->name }}</p>
    <p>vertrak datum: {{ $booking->departure_date->format('d-m-Y') }}</p>
    <h5>Van:</h5>
    <p>Naam: {{ $booking->contact->name }}</p>
    <p>Email: {{ $booking->contact->email }}</p>
    <p>Telefoonbnummer:{{ $booking->contact->phone }}</p>
@endsection
