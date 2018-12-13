@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content-center">
        <div class="col-md-8">
        @auth
        <h1>Сессии пользователя {{ $viberUser->name }}</h1>
        <table>
<tr>
<td><b>Выбранный препарат</b></td>
<td><b>Количество этапов</b></td>
<td><b>Время приёма</b></td>
</tr>
@foreach($viberUser->sessions as $session)
<tr>
<td>{{ $session->drug->name }}</td>
<td>{{ $session->stage_num}}</td>
<td>{{ $session->procedure_at }}</td>
</tr>
@endforeach
</table>
  @endauth
                        </div>
        </div>
    </div>
</div>
@endsection
