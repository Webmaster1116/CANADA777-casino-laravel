@extends('backend.layouts.app')

@section('page-title', trans('app.dashboard'))
@section('page-heading', trans('app.dashboard'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-light-blue">
                    <div class="inner">
                        <h3>{{ number_format($stats['total']) }}</h3>
                        <p>@lang('app.total_users')</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="{{ route('backend.user.list') }}" class="small-box-footer">@lang('app.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ number_format($stats['new']) }}</h3>
                        <p>@lang('app.new_users_this_month')</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <a href="{{ route('backend.user.list') }}" class="small-box-footer">@lang('app.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ number_format($stats['banned']) }}</h3>
                        <p>@lang('app.banned_users')</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-ban"></i>
                    </div>
                    <a href="{{ route('backend.user.list') }}" class="small-box-footer">@lang('app.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ number_format($stats['games']) }}</h3>
                        <p>@lang('app.games')</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-desktop"></i>
                    </div>
                    <a href="{{ route('backend.game.list') }}" class="small-box-footer">@lang('app.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <!-- /.row -->

        <!-- Latest Pay Stats / Latest Game Stats -->

        <div class="row">

            @permission('stats.pay')
            <div class="col-xs-6">
                <div class="box box-success">

                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('app.latest_pay_stats')</h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>@lang('app.system')</th>
                                    <th>@lang('app.sum')</th>
                                    <th>@lang('app.user')</th>
                                    <th>@lang('app.date')</th>
                                </tr>
                                </thead>

                                <tbody>

                                @if (count($statistics))
                                    @foreach ($statistics as $stat)
                                        <tr>
                                            <td>
                                                <a href="{{ route('backend.statistics', ['system_str' => $stat->admin ? $stat->admin->username : $stat->system])  }}">
                                                    {{ $stat->admin ? $stat->admin->username : $stat->system }}
                                                </a>
                                                @if( $stat->value ) {{ $stat->value }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($stat->type == 'in')
                                                    <span class="text-green">{{ abs($stat->summ) }}</span>
                                                @else
                                                    <span class="text-red">{{ abs($stat->summ) }}</span>
                                                @endif
                                            </td>
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.statistics', ['user' => $stat->user ? $stat->user->username : ''])  }}">
                                                    {{ $stat->user ? $stat->user->username : '' }}
                                                </a>
                                            </td>
                                            <td>{{ $stat->created_at->format(config('app.time_format')) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="4">@lang('app.no_data')</td></tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endpermission

            @permission('stats.game')
            <div class="col-xs-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('app.latest_game_stats')</h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">

                            <table class="table table-striped">

                                <thead>
                                <tr>
                                    <th>@lang('app.game')</th>
                                    <th>@lang('app.user')</th>
                                    <th>@lang('app.balance')</th>
                                    <th>@lang('app.bet')</th>
                                    <th>@lang('app.win')</th>
                                    <th>@lang('app.date')</th>
                                </tr>
                                </thead>

                                <tbody>

                                @if (count($gamestat))
                                    @foreach ($gamestat as $stat)
                                        <tr>
                                            <td>
                                                <a href="{{ route('backend.game_stat', ['game' => $stat->game])  }}">
                                                    {{ $stat->game }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.game_stat', ['user' => $stat->user ? $stat->user->username : ''])  }}">
                                                    {{ $stat->user ? $stat->user->username : '' }}
                                                </a>
                                            </td>
                                            <td>{{ $stat->balance }}</td>
                                            <td>{{ $stat->bet }}</td>
                                            <td>{{ $stat->win }}</td>
                                            <td>{{ date(config('app.time_format'), strtotime($stat->date_time)) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="6">@lang('app.no_data')</td></tr>
                                @endif

                                </tbody>

                            </table>

                        </div>
                    </div>

                </div>
            </div>
            @endpermission

        </div>

        <!-- /Latest Pay Stats / Latest Game Stats -->

        <!-- Latest Shops / Latest Bank Stats -->

        <div class="row">

            @permission('users.manage')
            <div class="col-xs-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('app.latest_registrations')</h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">

                            <table class="table table-striped">

                                <thead>
                                <tr>
                                    <th>@lang('app.username')</th>
                                    @permission('users.balance.manage')
                                    <th>@lang('app.balance')</th>
                                    @endpermission
                                    <th>@lang('app.status')</th>
                                </tr>
                                </thead>

                                <tbody>

                                @if (count($latestRegistrations))
                                    @foreach ($latestRegistrations as $user)
                                        <tr>
                                            <td>
                                                <a href="{{ route('backend.user.edit', $user->id) }}">
                                                    {{ $user->username ?: trans('app.n_a') }}
                                                </a>
                                            </td>
                                            @permission('users.balance.manage')
                                            <td>{{ $user->balance }}</td>
                                            @endpermission
                                            <td>{{ date(config('app.time_format'), strtotime($user->last_login)) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="4">@lang('app.no_data')</td></tr>
                                @endif

                                </tbody>

                            </table>

                        </div>
                    </div>

                </div>
            </div>
            @endpermission


            @permission('stats.bank')
            <div class="col-xs-6">
                <div class="box box-success">

                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('app.latest_bank_stat')</h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>@lang('app.name')</th>
                                    <th>@lang('app.user')</th>
                                    <th>@lang('app.old')</th>
                                    <th>@lang('app.new')</th>
                                    <th>@lang('app.sum')</th>
                                    <th>@lang('app.date')</th>
                                </tr>
                                </thead>

                                <tbody>

                                @if (count($bank_stat))
                                    @foreach ($bank_stat as $stat)
                                        <tr>
                                            <td>{{ $stat->name }}</td>
                                            <td>{{ $stat->user ? $stat->user->username : '' }}</td>
                                            <td>{{ $stat->old }}</td>
                                            <td>{{ $stat->new }}</td>
                                            <td>
                                                @if ($stat->type == 'in')
                                                    <span class="text-green">{{ abs($stat->sum) }}</span>
                                                @else
                                                    <span class="text-red">{{ abs($stat->sum) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $stat->created_at->format(config('app.time_format')) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="6">@lang('app.no_data')</td></tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endpermission
        </div>

        <!-- /Latest Shops / Latest Registrations -->


        <!-- Latest Shift Stat -->

        <div class="row">

            @permission('stats.shift')
            <div class="col-xs-12">
                <div class="box box-success">

                    <div class="box-header with-border">
                        <h3 class="box-title">Latest @lang('app.shift_stats')</h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    @if(!auth()->user()->hasRole('cashier'))
                                        <th>@lang('app.shift')</th>
                                    @endif
                                    <th>@lang('app.user')</th>
                                    <th>@lang('app.date_start')</th>
                                    <th>@lang('app.date_end')</th>
                                    @if(!auth()->user()->hasRole('cashier'))
                                        <th>@lang('app.credit')</th>
                                        <th>@lang('app.in')</th>
                                        <th>@lang('app.out')</th>
                                    @endif
                                    <th>@lang('app.total')</th>
                                    @permission('games.in_out')
                                        <th>@lang('app.banks')</th>
                                    @endpermission
                                    <th>@lang('app.returns')</th>
                                    <th>@lang('app.money')</th>
                                    <th>@lang('app.in')</th>
                                    <th>@lang('app.out')</th>
                                    <th>@lang('app.total')</th>
                                    @if(auth()->user()->hasRole('admin'))
                                        <th>@lang('app.profit')</th>
                                    @endif
                                </tr>
                                </thead>

                                <tbody>

                                @if (count($open_shift))
                                    @foreach ($open_shift as $num=>$stat)
                                        <tr>
                                            @if(!auth()->user()->hasRole('cashier'))
                                                <td>{{ $stat->id }}</td>
                                            @endif
                                            <td>{{ $stat->user ? $stat->user->username : '' }}</td>
                                            <td>{{ date(config('app.date_format'), strtotime($stat->start_date)) }}</td>
                                            <td>{{ $stat->end_date ? date(config('app.date_format'), strtotime($stat->end_date)) : '' }}</td>
                                            @if(!auth()->user()->hasRole('cashier'))
                                                <td>{{ $stat->balance }}</td>
                                            <td>{{ $stat->balance_in }}</td>
                                            <td>{{ $stat->balance_out }}</td>
                                                @endif
                                                    <td>{{ number_format ($stat->balance + $stat->balance_in - $stat->balance_out, 4, ".", "") }}</td>
                                                @permission('games.in_out')
                                                @php
                                                $banks = !$stat->end_date ? $stat->banks() : $stat->last_banks;
                                            @endphp
                                            <td>{{ number_format ($banks, 4, ".", "") }}</td>
                                                @endpermission
                                            <td>
                                                @if( !$stat->end_date )
                                                    {{ $stat->returns() }}
                                                @else
                                                    {{ $stat->last_returns }}
                                                @endif
                                            </td>

                                            @php
                                                $money = $stat->users;
                                                if($stat->end_date == NULL){
                                                    $money = $summ;
                                                }
                                            @endphp

                                            <td>{{ $money }}</td>
                                            <td>{{ $stat->money_in }}</td>
                                            <td>{{ $stat->money_out }}</td>

                                            @php
                                                $total = $stat->money_in - $stat->money_out;
                                            @endphp

                                            <td>{{ number_format ($total, 4, ".", "") }}</td>

                                            @if(auth()->user()->hasRole('admin'))
                                                <td>{{ number_format ($stat->profit(), 4, ".", "") }}</td>
                                            @endif

                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="15">@lang('app.no_data')</td></tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endpermission

        </div>

        <!-- /Latest Shift Stat -->

    </section>
    <!-- /.content -->

@stop

@section('scripts')
    <script>
        //$('.table').dataTable();

                @php
                    $data = [];
                    foreach($usersPerMonth AS $key=>$value){
                        $data[] = ['y' => $key, 'item1' => rand(100,1000)];
                    }
                @endphp

        var area = new Morris.Area({
                element   : 'revenue-chart',
                resize    : true,
                data      : [
                        @foreach($usersPerMonth AS $key=>$value)
                    {y: "{{ $key }}", item1: {{ $value }} },
                    @endforeach
                ],
                xkey      : 'y',
                ykeys     : ['item1'],
                labels    : ["{{ trans('app.new_sm') }}"],
                lineColors: ['#a0d0e0'],
                hideHover : 'auto'
            });

    </script>
    {!! HTML::script('/back/dist/js/pages/dashboard.js') !!}
@stop
