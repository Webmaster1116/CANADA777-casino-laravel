@extends('backend.layouts.app')

@section('page-title', trans('app.automizy'))
@section('page-heading', trans('app.automizy'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.automizy')</h3>
				<div class="pull-right box-tools">
					<a href="{{ route('backend.automizy.add_list') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
			</div>
            <div class="box-body">
                <div class="table-responsive" >
                    <table class="table table-bordered table-striped" id="automizy_table">
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.name')</th>
						<th>@lang('app.contactsCount')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($smart_lists))
						@foreach ($smart_lists as $item)
							@include('backend.automizy.item_list')
						@endforeach
					@else
						<tr><td colspan="9">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.name')</th>
						<th>@lang('app.contactsCount')</th>
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
		$('#automizy_table').dataTable();
	</script>
@stop
