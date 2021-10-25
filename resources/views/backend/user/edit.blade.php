@extends('backend.layouts.app')

@section('page-title', trans('app.edit_user'))
@section('page-heading', $user->present()->username)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        <div class="row">
            @include('backend.user.partials.info')
            <div class="col-md-9">
                <form action="" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('app.dateFrom')</label>
                                <input type="text" class="form-control" name="dateFrom" value="{{ Request::get('dateFrom') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('app.dateTo')</label>
                                <input type="text" class="form-control" name="dateTo" value="{{ Request::get('dateTo') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    @lang('app.filter')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a id="details-tab"
                               data-toggle="tab"
                               href="#details">
                                @lang('app.edit_user')
                            </a>
                        </li>
                        <li>
                            <a id="login-tab"
                               data-toggle="tab"
                               href="#login-details">
                                @lang('app.latest_activity')
                            </a>
                        </li>
                        <li>
                            <a id="game-tab"
                               data-toggle="tab"
                               href="#game-details">
                                @lang('app.games_activity')
                            </a>
                        </li>
                        <li>
                            <a id="transaction-tab"
                               data-toggle="tab"
                               href="#transaction-history">
                                @lang('app.transaction_history')
                            </a>
                        </li>
                        <li>
                            <a id="bet-tab"
                               data-toggle="tab"
                               href="#bet-history">
                                @lang('app.bet_history')
                            </a>
                        </li>
                        <li>
                            <a id="bet-tab"
                               data-toggle="tab"
                               href="#api-game-bet-history">
                                @lang('app.api_game_bet_history')
                            </a>
                        </li>
                        <li>
                            <a id="bonus-tab"
                               data-toggle="tab"
                               href="#bonus-history">
                                @lang('app.bonus_history')
                            </a>
                        </li>
                        <li>
                            <a id="freespin-tab"
                               data-toggle="tab"
                               href="#freespin-history">
                                @lang('app.freespin_history')
                            </a>
                        </li>
                        <li>
                            <a id="verify-tab"
                               data-toggle="tab"
                               href="#verify">
                                @lang('app.verify_account')
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane active" id="details">
                            {!! Form::open(['route' => ['backend.user.update.details', $user->id], 'method' => 'PUT', 'id' => 'details-form']) !!}
                            @include('backend.user.partials.edit')
                            {!! Form::close() !!}
                        </div>

                        <div class="tab-pane" id="login-details">
                            @if (count($userActivities))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.action')</th>
                                        <th>@lang('app.date')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($userActivities as $activity)
                                        <tr>
                                            <td>{{ $activity->description }}</td>
                                            <td>{{ $activity->created_at->format(config('app.date_time_format')) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>

                        <div class="tab-pane" id="game-details">
                            @if (count($numbers) || count($max_wins) || count($max_bets))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.games')</th>
                                        <th>@lang('app.count2')</th>
                                        <th>@lang('app.max_bet')</th>
                                        <th>@lang('app.max_win')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if ($numbers)
                                        @foreach($numbers as $number)
                                            <tr>
                                                <td>{{ $number->game }}</td>
                                                <td>{{ $number->summ }} </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @if ($max_wins)
                                        @foreach($max_wins as $win)
                                            <tr>
                                                <td>{{ $win->game }}</td>
                                                <td></td>
                                                <td></td>
                                                <td>{{ $win->max_win }} </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @if ($max_bets)
                                        @foreach($max_bets as $bet)
                                            <tr>
                                                <td>{{ $bet->game }}</td>
                                                <td></td>
                                                <td>{{ $bet->max_bet }} </td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>
                        <div class="tab-pane" id="transaction-history">
                            <form action="{{ route('backend.user.balance.update.manually') }}" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" id="AddId" name="user_id" value="{{ $user->present()->id }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.payment_amount')</label>
                                            <input type="text" class="form-control" name="summ" value="{{ Request::get('dateFrom') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.reason')</label>
                                            <input type="text" class="form-control" name="dateTo" value="{{ Request::get('dateTo') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-primary">
                                                @lang('app.deposit')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @if (count($transactions))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.date')</th>
                                        <th>@lang('app.status')</th>
                                        <th>@lang('app.payment_system')</th>
                                        <th>@lang('app.payment_type')</th>
                                        <th>@lang('app.payment_amount')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($transactions as $history)
                                        <tr>
                                            <td>{{ $history->created_at->format(config('app.date_time_format')) }}</td>
                                            <td>
                                                @if($history->status == 1) Confirm @else Unconfirm @endif
                                            </td>
                                            <td>{{ $history->system }}</td>
                                            <td>{{ $history->type }}</td>
                                            <td>{{ abs($history->summ) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>
                        <div class="tab-pane" id="bet-history">
                            <p style="text-align:center">TOTAL BET:{{$totalBet}} AMOUNT WIN:{{$amountWin}} AMOUNT WIN{{$winPercent}}%</p>
                            @if (count($bets))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.date')</th>
                                        <th>@lang('app.balance')</th>
                                        <th>@lang('app.bet')</th>
                                        <th>@lang('app.win')</th>
                                        <th>@lang('app.game')</th>
                                        <th>@lang('app.percent')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bets as $history)
                                        <tr data-id="{{$history->id}}">
                                            <td>{{$history->date_time}}</td>
                                            <td>{{$history->balance}}</td>
                                            <td>{{$history->bet}}</td>
                                            <td>{{$history->win}}</td>
                                            <td>{{$history->game}}</td>
                                            <td>{{$history->percent}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>
                        <div class="tab-pane" id="api-game-bet-history">
                            <p style="text-align:center">TOTAL BET:{{$totalApiGameBet}} AMOUNT WIN:{{$totalApiGameWin}} AMOUNT WIN{{$winApiGamePercent}}%</p>
                            @if (count($apiGameBet))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.date')</th>
                                        <th>@lang('app.balance')</th>
                                        <th>@lang('app.bet')</th>
                                        <th>@lang('app.win')</th>
                                        <th>@lang('app.game')</th>
                                        <!-- <th>@lang('app.percent')</th> -->
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($apiGameBet as $api_game)
                                        <tr data-id="{{$history->id}}">
                                            <td>{{$api_game->created_at}}</td>
                                            <td>{{$api_game->there_was_money}}</td>
                                            @if($api_game->action == 'debit')
                                            <td>{{$api_game->amount}}</td>
                                            <td></td>
                                            @else
                                            <td></td>
                                            <td>{{$api_game->amount}}</td>
                                            @endif
                                            <td>{{$api_game->name}}</td>
                                            <!-- <td></td> -->
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>
                        <div class="tab-pane" id="bonus-history">
                            @if (count($bonus))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.date')</th>
                                        <th>@lang('app.deposit_number')</th>
                                        <th>@lang('app.deposit')</th>
                                        <th>@lang('app.bonus')</th>
                                        <th>@lang('app.wager')</th>
                                        <th>@lang('app.wager_remain')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bonus as $history)
                                        <tr>
                                            <td>{{$history->created_at}}</td>
                                            @if($history->deposit_num == 1)
                                            <td>1st Deposit</td>
                                            @elseif($history->deposit_num == 2)
                                            <td>2nd Deposit</td>
                                            @elseif($history->deposit_num == 3)
                                            <td>3rd Deposit</td>
                                            @else
                                            <td>{{$history->deposit_num}}th Deposit</td>
                                            @endif
                                            <td>{{$history->deposit}}</td>
                                            <td>{{$history->bonus}}</td>
                                            <td>{{$history->wager}}</td>
                                            <td>{{$history->wager-$history->wager_played}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>
                        <div class="tab-pane" id="freespin-history">
                            @if(isset($freespin) && count($freespin))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Game</th>
                                            <th>Free Spins</th>
                                            <th>Remaining<br/>Free Spins</th>
                                            <th>Win</th>
                                            <th>Wager<br/>Need</th>
                                            <th>Remaining<br/>Wager</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($freespin as $history)
                                        @if(date('Y-m-d', strtotime($history->started_at)) < date('Y-m-d'))
                                        <tr>
                                        @elseif(date('Y-m-d', strtotime($history->started_at)) > date('Y-m-d'))
                                        <tr>
                                        @else
                                        <tr>
                                        @endif
                                            @if($history->day == 1)
                                            <td>{{date('Y-m-d', strtotime($history->started_at))}}<br/>{{$history->day}}st Day</td>
                                            @elseif($history->day == 2)
                                            <td>{{date('Y-m-d', strtotime($history->started_at))}}<br/>{{$history->day}}nd Day</td>
                                            @elseif($history->day == 3)
                                            <td>{{date('Y-m-d', strtotime($history->started_at))}}<br/>{{$history->day}}rd Day</td>
                                            @else
                                            <td>{{date('Y-m-d', strtotime($history->started_at))}}<br/>{{$history->day}}th Day</td>
                                            @endif
                                            <td>{{$history->name}}</td>
                                            <td>{{$history->freespin}}</td>
                                            <td>{{$history->remain_freespin}}</td>
                                            <td>{{$history->win}}</td>
                                            <td>{{$history->wager}}</td>
                                            <td>{{$history->wager-$history->wager_played}}</td>
                                            @if(date('Y-m-d', strtotime($history->started_at)) < date('Y-m-d'))
                                            <td>Expired</td>
                                            @elseif(date('Y-m-d', strtotime($history->started_at)) > date('Y-m-d'))
                                            <td>Comming</td>
                                            @else
                                            <td>Today</td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>
                        <div class="tab-pane" id="verify">
                            @if ($verify)
                            {!! Form::open(['route' => ['backend.user.update.verify', $user->id], 'method' => 'PUT', 'id' => 'details-form']) !!}
                            @if ($verify->id_img)
                            <div class="form-group">
                                <label>@lang('app.id_image')</label>
                                <img src="{{ $verify->id_img }}" style="display:block;">
                            </div>
                            @endif
                            @if ($verify->address_img)
                            <div class="form-group">
                                <label>@lang('app.address_image')</label>
                                <img src="{{ $verify->address_img }}" style="display:block;">
                            </div>
                            <div class="form-group">
                                <label>Verify Status</label>
                                <select class="form-control" id="verified" name="verified">
                                    @if ($verify->verified == 0)
                                    <option value="0" selected="selected">Unverify</option>
                                    <option value="1">Verify</option>
                                    @else
                                    <option value="0">Unverify</option>
                                    <option value="1" selected="selected">Verify</option>
                                    @endif
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" id="update-details-btn">
                                @lang('app.edit_user')
                            </button>
                            @endif
                            {!! Form::close() !!}
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@stop

@section('scripts')
    <script>
        $(function() {
            $('input[name="date"]').datepicker({
                format: 'yyyy-mm-dd',
            });
        });
    </script>
    {!! HTML::script('/back/js/as/app.js') !!}
    {!! HTML::script('/back/js/as/btn.js') !!}
    {!! HTML::script('/back/js/as/profile.js') !!}
    {!! JsValidator::formRequest('VanguardLTE\Http\Requests\User\UpdateDetailsRequest', '#details-form') !!}
    {!! JsValidator::formRequest('VanguardLTE\Http\Requests\User\UpdateLoginDetailsRequest', '#login-details-form') !!}
@stop
