@extends('backend.layouts.app')

@section('page-title', trans('app.edit_game'))
@section('page-heading', $apigame->name)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3">

                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <center>
                            <img class="img-responsive" src="{{ $edit ? $apigame->image_filled : '' }}" alt="{{ $edit ? $apigame->image_filled : '' }}">
                        </center>
                        <ul class="list-group list-group-unbordered">
                            @permission('games.in_out')
                            <li class="list-group-item">
                                <b>@lang('app.in')</b> <a class="pull-right">{{ $apigame_in }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('app.out')</b> <a class="pull-right">{{ $apigame_out }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('app.total')</b>
                                <a class="pull-right">
                                    @if(($apigame_in - $apigame_out) >= 0)
                                        <span class="text-green">
		@else
                                                <span class="text-red">
		@endif
                                                    {{ number_format(abs($apigame_in-$apigame_out), 4, '.', '') }}
		</span>
                                </a>
                            </li>
                            @endpermission
                        </ul>
                    </div>
                </div>

            </div>

            <div class="col-md-9" id="colrighttemp">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a id="details-tab"
                               data-toggle="tab"
                               href="#details">
                                @lang('app.api_game_details')
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="active tab-pane" id="details">
                            {!! Form::open(['route' => ['backend.game.apiupdate', $apigame->game_id], 'method' => 'POST', 'id' => 'details-form']) !!}
                            @include('backend.games.partials.apibase')
                            {!! Form::close() !!}
                        </div>
                    </div>

                </div>

            </div>
        </div>



    </section>





   

    

@stop

@section('scripts')
    <script>
        $('.changeAddSum').click(function(event){
            $('#AddSum').val($(event.target).data('value'));
            $('#gamebank_add').submit();
        });
    </script>
@stop