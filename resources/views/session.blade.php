@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content-center">
    <div class="col-md-8">
        @auth
        <h1>Сессии пользователя {{ $viberUser->name }}</h1>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Выбранный препарат</th>
                    <th scope="col">Количество этапов</th>
                    <th scope="col">Время приёма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($viberUser->sessions as $session)
                <tr>
                    <td>{{ $session->drug->name }}</td>
                    <td>{{ $session->stage_num}}</td>
                    <td>{{ $session->procedure_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endauth
    </div>
</div>
@endsection
