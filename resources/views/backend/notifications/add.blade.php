@extends('backend.layouts.app')

@section('page-title', 'Add Notifications')
@section('page-heading', 'Add Notifications')

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
      <div class="box box-default">
		{!! Form::open(['route' => 'backend.notifications.add', 'files' => true, 'id' => 'notifications-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">Add Notifications</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.notifications.base', ['edit' => false])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
          Add Notifications
        </button>
        </div>
		{!! Form::close() !!}
      </div>
    </section>

@stop
@section('scripts')
    <script>
        $(function() {
            $('input[name="valid_from"]').datepicker({
                format: 'yyyy-mm-dd',
            });
            $('input[name="valid_time"]').datetimepicker({
                format: 'HH:mm',
            });
        });
    </script>
    {!! HTML::script('/back/js/as/app.js') !!}
    {!! HTML::script('/back/js/as/btn.js') !!}
    {!! HTML::script('/back/js/as/profile.js') !!}
@stop