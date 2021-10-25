@extends('backend.layouts.app')

@section('page-title', 'Add List')
@section('page-heading', 'Add List')

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
      <div class="box box-default">
		{!! Form::open(['url' => route('backend.automizy.edit_list', $smart_list['id']), 'files' => true, 'id' => 'automizy-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">Edit List</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.automizy.base_list', ['edit' => true])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
          Edit List
        </button>
        <a href="{{route('backend.automizy.delete_list', $smart_list['id'])}}" class="btn btn-danger" data-method="GET" data-confirm-title="Please Confirm" data-confirm-text="Are you sure delete list?" data-confirm-delete="Yes, delete it!">
          Delete List
        </a>
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