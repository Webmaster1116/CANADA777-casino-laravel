@extends('backend.layouts.auth')

@section('page-title', trans('app.login'))

@section('content')
<div class="login-wrapper">
  <div class="login-box">
    <div class="login-logo">
{{--      <a href="{{ route('frontend.auth.login') }}"><span class="logo-lg"><b>CANADA 777</b> <small>/ mrs</small></span></a>--}}
        <a href="{{ route('frontend.auth.login') }}">
            <img src="{{asset('back/img/login-Logo.png')}}" />
        </a>
    </div>

    @include('backend.partials.messages')

    <div class="login-box-body">

      <form role="form" action="{{url('login')}}" method="POST" id="login-form" autocomplete="off">
        @csrf
        <div class="form-group has-feedback">
          <input type="text" name="username" id="username" class="form-control" placeholder="@lang('app.email_or_username')">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" name="password" id="password" class="form-control" placeholder="@lang('app.password')">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block btn-flat" id="btn-login">
              @lang('app.log_in')
            </button>
          </div>
        </div>
      </form>
    </div>
    <div class="login-box-footer">
        <a href="{{ url('/categories/all') }}">RETURN TO SITE</a>
    </div>
  </div>
</div>
  <script src="/back/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="/back/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="/back/plugins/iCheck/icheck.min.js"></script>
@stop
@section('scripts')
  {!! JsValidator::formRequest('VanguardLTE\Http\Requests\Auth\LoginRequest', '#login-form') !!}
@stop
