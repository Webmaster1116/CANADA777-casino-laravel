@extends('frontend.Default.layouts.game_layout')


@section('content')
	<!-- GAMES - BEGIN -->
	<iframe id='game' style="margin:0px;border:0px;width:100%;height:100vh;" src="{{ $play['response'] }}" allowfullscreen>
	<!-- GAMES - BEGIN -->
@stop