@extends('backend.layouts.app')

@section('page-title', 'Add Freespin Round')
@section('page-heading', 'Add Freespin Round')

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
      <div class="box box-default">
		{!! Form::open(['url' => route('backend.freespinround.edit', $freespinround->id), 'files' => true, 'id' => 'freespinround-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">Edit Freespin Round</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.freespinround.base', ['edit' => true])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
          Edit Freespin Round
        </button>
        <a href="{{route('backend.freespinround.delete', $freespinround->id)}}" class="btn btn-danger" data-method="GET" data-confirm-title="Please Confirm" data-confirm-text="Are you sure delete freespin round?" data-confirm-delete="Yes, delete it!">
          Delete Freespin Round
        </a>
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