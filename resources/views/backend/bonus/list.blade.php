@extends('backend.layouts.app')

@section('page-title', trans('app.bonus'))
@section('page-heading', trans('app.bonus'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.bonus')</h3>
				<div class="pull-right box-tools">
					<a href="{{ route('backend.bonus.add') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
			</div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.name')</th>
						<th>@lang('app.type')</th>
						<th>Valid From</th>
						<th>Valid Until</th>
						<th>Days</th>
						<th>Min Deposit</th>
						<th>Max Deposit</th>
						<th>Match Win</th>
						<th>Code</th>
						<th>Wagering</th>
						<th>@lang('app.active')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($bonus))
						@foreach ($bonus as $item)
							@include('backend.bonus.item')
						@endforeach
					@else
						<tr><td colspan="9">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.name')</th>
						<th>@lang('app.type')</th>
						<th>Valid From</th>
						<th>Valid Until</th>
						<th>Days</th>
						<th>Min Deposit</th>
						<th>Max Deposit</th>
						<th>Match Win</th>
						<th>Code</th>
						<th>Wagering</th>
						<th>@lang('app.active')</th>
					</tr>
					</thead>
                    </table>
                </div>
            </div>
		</div>
	</section>
@stop

@section('scripts')
	<script>
		$('#jackpots-table').dataTable();
		$("#status").change(function () {
			$("#users-form").submit();
		});
		$('.addPayment').click(function(event){
			console.log($(event.target));
			var item = $(event.target).hasClass('addPayment') ? $(event.target) : $(event.target).parent();
			var id = item.attr('data-id');
			$('#AddId').val(id);
			$('#outAll').val('0');
		});


		$('#doOutAll').click(function () {
			$('#outAll').val('1');
			$('form#outForm').submit();
		});

		$('.outPayment').click(function(event){
			console.log($(event.target));
			var item = $(event.target).hasClass('outPayment') ? $(event.target) : $(event.target).parent();
			console.log(item);
			var id = item.attr('data-id');
			$('#OutId').val(id);
		});
	</script>
@stop
