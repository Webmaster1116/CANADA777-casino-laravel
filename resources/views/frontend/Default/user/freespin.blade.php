@extends('frontend.Default.user.profile')
@section('content')
<div class="navigation-sibling">
    <div class="main-section px-4 py-4">
        <hr class="divider"></hr>
        <div class="row">
            <div class="col-md-12">
                <center><h3><b>BONUS</b></h3>
<p>1st deposit • 100% Match +200 Free Spins<br/>2nd deposit • 100% Match<br/>3rd deposit • 100% Match</p>
                <h3><b>BONUS FREE SPINS</b></h3> </center>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="freespins_history_table" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:20%" scope="col">Date</th>
                        <th style="width:20%" scope="col">Game</th>
                        <th style="width:10%" scope="col">Free Spins</th>
                        <th style="width:10%" scope="col">Remaining<br/>Free Spins</th>
                        <th style="width:10%" scope="col">Win</th>
                        <th style="width:10%" scope="col">Wager<br/>Need</th>
                        <th style="width:10%" scope="col">Remaining<br/>Wager</th>
                        <th style="width:10%" scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($welcomepackage_history) && count($welcomepackage_history))
                    @foreach($welcomepackage_history as $history)
                    @if(date('Y-m-d', strtotime($history->started_at)) < date('Y-m-d'))
                    <tr class="text-muted">
                    @elseif(date('Y-m-d', strtotime($history->started_at)) > date('Y-m-d'))
                    <tr class="text-dark">
                    @else
                    <tr class="text-primary">
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
                        @if (date('Y-m-d', strtotime($history->started_at)) == date('Y-m-d'))
                        <td><a href="{{ route('frontend.game.go.prego', ['game'=>$history->name, 'prego'=>'realgo']) }}">{{$history->name}}</a></td>
                        @else
                        <td><a href="javascript:void(0);">{{$history->name}}</a></td>
                        @endif
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
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection