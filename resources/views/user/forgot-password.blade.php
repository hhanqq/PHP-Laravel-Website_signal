@extends('layouts.main')

@section('title', 'Home page')

@section('content')
<h1 class="h2">Forgot password</h1>

<p>Введите email для восстановления!</p>

<form action="{{ route('password.email') }}" method="post">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input name="email" type="email" class="form-control" id="email" placeholder="Email">
    </div>
    <button type="submit" class="btn btn-primary">Send link</button>

</form>

@endsection