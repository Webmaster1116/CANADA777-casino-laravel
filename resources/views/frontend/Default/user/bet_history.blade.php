@extends('frontend.Default.user.profile')
@section('content')
<div class="navigation-sibling">
    <div class="main-section px-4 py-4">
    @include('frontend.Default.user.transaction_header')
        <hr class="divider"></hr>
        <div class="row">
            <div class="col-md-12">
                <h3>Bets History</h3>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="bet_history_table" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 40%" scope="col">Datetime</th>
                        <th style="width: 20%" scope="col">Balance</th>
                        <th style="width: 20%" scope="col">Bet</th>
                        <th style="width: 20%" scope="col">Win</th>
                        <th style="width: 20%" scope="col">Game</th>
                        <th style="width: 20%" scope="col">Percent</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($bet_history) && count($bet_history))
                    @foreach($bet_history as $history)
                    <tr>
                        <td>{{$history->date_time}}</td>
                        <td>{{$history->balance}}</td>
                        <td>{{$history->bet}}</td>
                        <td>{{$history->win}}</td>
                        <td>{{$history->game}}</td>
                        <td>{{$history->percent}}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection