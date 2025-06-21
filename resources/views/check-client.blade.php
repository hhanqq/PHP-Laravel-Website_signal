@extends('layouts.main')

@section('content')
<div class="container">
    <h2>Введите ID клиента</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('check.client') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="client">ID клиента:</label>
            <input type="text" name="client" id="client" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Проверить</button>
    </form>
</div>
@endsection