@extends('backend.layouts.app')

@section('page-title', trans('app.withdraw'))
@section('page-heading', trans('app.withdraw'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.withdraw')</h3>
				<div class="pull-right box-tools">
					<!-- <a href="" class="btn btn-block btn-primary btn-sm">ADD</a> -->
				</div>
			</div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="withdraw-table">
						<thead>
							<tr>
								<th>UserID</th>
								<th>Username</th>
								<th>Date</th>
								<th>RealBalance</th>
								<th>Amount</th>
								<th>Request Email</th>
								<th>Phone</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(isset($transactions) && count($transactions))
						@foreach($transactions as $transaction)
							<tr>
								<td>{{$transaction->user_id}}</td>
								<td>
									<a href="{{ route('backend.user.edit', $transaction->user_id) }}">
									{{$transaction->username}}
									</a>
								</td>
								<td>{{$transaction->created_at}}</td>
								<td>{{$transaction->balance}}</td>
								<td>{{$transaction->summ}}</td>
								<td>{{$transaction->email}}</td>
								<td>{{$transaction->phone}}</td>
								<td>{{$transaction->getStatus()}}</td>
								<td class="d-flex">
									<!-- @if($transaction->approve)
									<button type="button" class="btn btn-block btn-success btn-xs">Approve</button>
									@else
									<button type="button" class="btn btn-block btn-success btn-xs" disabled="true">Inprogress</button>
									@endif -->
									@if ($transaction->status == 0)
										@if( $transaction->system == 'interac')
											<a href="{{ route('backend.withdraw.approve', $transaction->id) }}" type="button" class="btn btn-block btn-success btn-xs">Approve</a>
											<a href="{{ route('backend.withdraw.reject', $transaction->id) }}" type="button" class="btn btn-block btn-danger btn-xs">Reject</a>
										@elseif ($transaction->system == 'crypto')
											<a href="{{ route('backend.crypto_withdraw.approve', $transaction->id) }}" type="button" class="btn btn-block btn-success btn-xs">Approve</a>
											<a href="{{ route('backend.crypto_withdraw.reject', $transaction->id) }}" type="button" class="btn btn-block btn-danger btn-xs">Reject</a>
										@endif
									@endif
								</td>
							</tr>
						@endforeach
						@endif
						</tbody>
                    </table>
                </div>
            </div>
		</div>
	</section>
	<div id="multiaccount-modal" class="multiaccount-modal modal">
		<div class="multiaccount-modal-content">
			
		</div>
	</div>
@stop

@section('scripts')
	<script>
		$('#withdraw-table').dataTable({
			"order": [[ 2, "desc" ]]
		});
	</script>
@stop
