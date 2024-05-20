@extends('mail.mailbody')

@section('header')
    <h1>Hello {{$request->first_name}} {{$request->last_name}}</h1>
@endsection


@section('content')
    <h1>Hello {{$request->first_name}} {{$request->last_name}}</h1>
    <p>Du hast deine email Adresse geändert auf {{$request->email}} - Bitte bestätige uns diese Änderung durch klick auf den folgenden Link</p>
    <a href="{{env('APP_URL')}}/verify-email/{{$user->remember_token}}/{{$user->id}}/{{$request->email}}" target="_blank" style="display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;">eMail ändern</a>
@endsection