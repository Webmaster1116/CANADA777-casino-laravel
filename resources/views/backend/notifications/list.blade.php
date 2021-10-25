@extends('backend.layouts.app')

@section('page-title', trans('app.notifications'))
@section('page-heading', trans('app.notifications'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.notifications')</h3>
				<div class="pull-right box-tools">
					<a href="{{ route('backend.notifications.add') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
			</div>
            <div class="box-body">
                <div class="table-responsive" >
                    <table class="table table-bordered table-striped" id="notifications_table">
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.message')</th>
						<th>@lang('app.image')</th>
						<th>@lang('app.schedule_campaign')</th>
						<th>@lang('app.frequency')</th>
						<th>@lang('app.valid_date')</th>
						<th>@lang('app.active')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($notifications))
						@foreach ($notifications as $item)
							@include('backend.notifications.item')
						@endforeach
					@else
						<tr><td colspan="9">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.message')</th>
						<th>@lang('app.image')</th>
						<th>@lang('app.schedule_campaign')</th>
						<th>@lang('app.frequency')</th>
						<th>@lang('app.valid_date')</th>
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
		$('#notifications_table').dataTable();
	</script>
@stop
