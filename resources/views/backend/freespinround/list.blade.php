@extends('backend.layouts.app')

@section('page-title', trans('app.freespinround'))
@section('page-heading', trans('app.freespinround'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.freespinround')</h3>
				<div class="pull-right box-tools">
					<a href="{{ route('backend.freespinround.add') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
			</div>
            <div class="box-body">
                <div class="table-responsive" >
                    <table class="table table-bordered table-striped" id="freespinround_table">
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.title')</th>
						<th>@lang('app.freespinround')</th>
						<th>@lang('app.bet')</th>
						<th>Valid From</th>
						<th>Valid To</th>
						<th>@lang('app.active')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($freespinrounds))
						@foreach ($freespinrounds as $item)
							@include('backend.freespinround.item')
						@endforeach
					@else
						<tr><td colspan="9">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.title')</th>
						<th>@lang('app.freespinround')</th>
						<th>@lang('app.bet')</th>
						<th>Valid From</th>
						<th>Valid To</th>
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
		$('#freespinround_table').dataTable();
	</script>
@stop
