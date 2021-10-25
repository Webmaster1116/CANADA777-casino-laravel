@extends('backend.layouts.app')

@section('page-title', 'Add Freespin Round')
@section('page-heading', 'Add Freespin Round')

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
      <div class="box box-default">
		{!! Form::open(['route' => 'backend.freespinround.add', 'files' => true, 'id' => 'freespinround-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">Add Freespin Round</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.freespinround.base', ['edit' => false])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
          Add Freespin Round
        </button>
        </div>
		{!! Form::close() !!}
      </div>
    </section>

@stop
@section('scripts')
    <script>
        $(function() {
            $('input[name^="valid_"]').datepicker({
                format: 'yyyy-mm-dd',
            });
        });
    </script>
    {!! HTML::script('/back/js/as/app.js') !!}
    {!! HTML::script('/back/js/as/btn.js') !!}
    {!! HTML::script('/back/js/as/profile.js') !!}
@stop