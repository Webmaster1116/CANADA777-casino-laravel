@extends('frontend.Default.user.profile')
@section('content')
<div class="navigation-sibling">
    <div class="main-section px-4 py-4">
        <hr class="divider"></hr>
        <div class="row">
            <div class="col-md-12">
                <h3>Bonus History</h3>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="bonus_history_table" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:30%" scope="col">Date</th>
                        <th style="width:30%" scope="col">Deposit Number</th>
                        <th style="width:10%" scope="col">Deposit</th>
                        <th style="width:10%" scope="col">Bonus</th>
                        <th style="width:10%" scope="col">Wager</th>
                        <th style="width:20%" scope="col">Remaining<br/>Wager</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($bonus_history) && count($bonus_history))
                    @foreach($bonus_history as $history)
                    @if($history->wager_played == $history->wager)
                    <tr class="text-muted">
                    @elseif($history->wager_played == 0)
                    <tr class="text-dark">
                    @else
                    <tr class="text-primary">
                    @endif
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
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection