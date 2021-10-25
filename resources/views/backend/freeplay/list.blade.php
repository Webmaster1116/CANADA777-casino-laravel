@extends('backend.layouts.app')

@section('page-title', trans('app.free_play'))
@section('page-heading', trans('app.free_play'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.free_play')</h3>
			</div>
            <div class="box-body">
                <div class="table-responsive" >
                    <table class="table table-bordered table-striped" id="free_play_table">
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.email')</th>
						<th>@lang('app.visitor_id')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($freeplay_users))
						@foreach ($freeplay_users as $key => $item)
							@include('backend.freeplay.item')
						@endforeach
					@else
						<tr><td colspan="9">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
					<th>@lang('app.id')</th>
						<th>@lang('app.email')</th>
						<th>@lang('app.visitor_id')</th>
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
		$('#free_play_table').dataTable();
	</script>
@stop
