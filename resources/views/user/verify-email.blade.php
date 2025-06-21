@extends('layouts.main')


@section('title', 'Home page')

@section('content')

    <div class="alert alert-info" role="alert">
        Thanks you for registration! A link to confirm your registration
        has been sent to your email.
    </div>
    
    <div>
        Didn`t recive the link?
        <form method="post" action="{{ route('verification.send') }}">
            @csrf
            <button type="sumbit" class="btn btn-link ps-0">Send link</button>
        </form>
    </div>

@endsection