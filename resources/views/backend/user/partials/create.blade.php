<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.username')</label>
        <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.email')</label>
        <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.first_name')</label>
        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.last_name')</label>
        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.date_of_birth')</label>
        <input type="text" class="form-control" id="birthday" name="birthday" value="{{ old('birthday') }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.phone')</label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.country')</label>
        {!! Form::select('country', $countries, 36,
            ['class' => 'form-control', 'id' => 'country', '']) !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.address')</label>
        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.postal_code')</label>
        <input type="text" class="form-control" id="postalCode" name="postalCode" value="{{ old('postalCode') }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.currency')</label>
        {!! Form::select('currency', $currencies, '',
            ['class' => 'form-control', 'id' => 'currency', '']) !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.lang')</label>
        <input type="text" class="form-control" id="language" name="language" value="{{ old('language') }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.status')</label>
        {!! Form::select('status', $statuses, '',
            ['class' => 'form-control', 'id' => 'status', '']) !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.role')</label>
        {!! Form::select('role_id', $roles, '',
            ['class' => 'form-control', 'id' => 'role_id', '']) !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>{{ trans('app.password') }}</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>{{ trans('app.confirm_password') }}</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.shop')</label>
        {!! Form::select('shop', $shops, '',
            ['class' => 'form-control', 'id' => 'shop', '']) !!}
    </div>
</div>