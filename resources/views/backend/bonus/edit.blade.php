@extends('backend.layouts.app')

@section('page-title', 'Add Bonus')
@section('page-heading', 'Add Bonus')

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
      <div class="box box-default">
		{!! Form::open(['url' => route('backend.bonus.edit', $bonus->id), 'files' => true, 'id' => 'bonus-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">Edit Bonus</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.bonus.base', ['edit' => true])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
          Edit Bonus
        </button>
        <a href="{{route('backend.bonus.delete', $bonus->id)}}" class="btn btn-danger" data-method="GET" data-confirm-title="Please Confirm" data-confirm-text="Are you sure delete bonus?" data-confirm-delete="Yes, delete it!">
          Delete Bonus
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