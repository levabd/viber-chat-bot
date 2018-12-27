@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @auth
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Имя</th>
                        <th scope="col">Выбранный препарат</th>
                        <th scope="col">Количество этапов</th>
                        <th scope="col">Время приёма</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($viberUsers as $viberUser)
                    <tr>
                    <td scope="row"><a href={{ url('/sessions/'.$viberUser->id) }}>{{ $viberUser->name }}</a></td>
                    <td>{{ $viberUser->completedSession->drug->name }}</td>
                    <td>{{ $viberUser->completedSession->stage_num}}</td>
                    <td>{{ $viberUser->completedSession->procedure_at->format(config('viber.datetime_format')) }}</td>
                    </tr>
                    @endforeach
                <tbody>
            </table>
            {!! $viberUsers->render() !!} 
            @endauth
        </div>
    </div>
</div>
@endsection
