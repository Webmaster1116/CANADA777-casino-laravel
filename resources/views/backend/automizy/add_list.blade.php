@extends('backend.layouts.app')

@section('page-title', 'Add List')
@section('page-heading', 'Add List')

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
      <div class="box box-default">
		{!! Form::open(['route' => 'backend.automizy.add_list', 'files' => true, 'id' => 'automizy-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">Add List</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.automizy.base_list', ['edit' => false])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
          Add List
        </button>
        </div>
		{!! Form::close() !!}
      </div>
    </section>

@stop
@section('scripts')
    {!! HTML::script('/back/js/as/app.js') !!}
    {!! HTML::script('/back/js/as/btn.js') !!}
    {!! HTML::script('/back/js/as/profile.js') !!}
@stop