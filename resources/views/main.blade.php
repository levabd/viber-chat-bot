@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
        @auth
<table>
<tr>
<td><b>Имя</b></td>
<td><b>Выбранный препарат</b></td>
<td><b>Количество этапов</b></td>
<td><b>Время приёма</b></td>
</tr>
@foreach($viberUsers as $viberUser)
<tr>
<td><a href={{ url('/sessions/'.$viberUser->id) }}>{{ $viberUser->name }}</a></td>
<td>{{ $viberUser->completedSession->drug->name }}</td>
<td>{{ $viberUser->completedSession->stage_num}}</td>
<td>{{ $viberUser->completedSession->procedure_at }}</td>
</tr>
@endforeach
</table>
  {!! $viberUsers->render() !!} 
        @endauth
                        </div>
        </div>
    </div>
</div>
@endsection
