<div class="box-body box-profile">


    <div class="form-group">
        <label>@lang('app.role')</label>
        {!! Form::select('role_id', Auth::user()->available_roles( true ), $edit ? $user->role_id : '',
            ['class' => 'form-control', 'id' => 'role_id', 'disabled' => true]) !!}
    </div>

    <div class="form-group">
        <label>@lang('app.status')</label>
        {!! Form::select('status', $statuses, $edit ? $user->status : '' ,
            ['class' => 'form-control', 'id' => 'status', 'disabled' => ($user->hasRole(['admin']) || $user->id == auth()->user()->id) ? true: false]) !!}
    </div>

    <div class="form-group">
        <label>@lang('app.username')</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="(@lang('app.optional'))" value="{{ $edit ? $user->username : '' }}">
    </div>

    @if( $user->email != '' )
    <div class="form-group">
        <label>@lang('app.email')</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="(@lang('app.optional'))" value="{{ $edit ? $user->email : '' }}">
    </div>
    @endif
    <div class="form-group">
        <label>@lang('app.first_name')</label>
        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="(@lang('app.optional'))" value="{{ $edit ? $user->first_name : '' }}">
    </div>
    <div class="form-group">
        <label>@lang('app.last_name')</label>
        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="(@lang('app.optional'))" value="{{ $edit ? $user->last_name : '' }}">
    </div>
    <div class="form-group">
        <label>@lang('app.date_of_birth')</label>
        <input type="text" class="form-control" id="birthday" name="birthday" placeholder="(@lang('app.optional'))" value="{{ ($edit && date('Y', strtotime($user->birthday))>1900) ? date('Y-m-d', strtotime($user->birthday)) : '' }}">
    </div>
    <div class="form-group">
        <label>@lang('app.phone')</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="(@lang('app.optional'))" value="{{ $edit ? $user->phone : '' }}">
    </div>
    <div class="form-group">
        <label>@lang('app.country')</label>
        {!! Form::select('country', array_pluck($countries, 'country', 'id') , $user->country,
            ['class' => 'form-control', 'id' => 'country']) !!}
    </div>
    <div class="form-group">
        <label>@lang('app.address')</label>
        <input type="text" class="form-control" id="address" name="address" placeholder="(@lang('app.optional'))" value="{{ $edit ? $user->address : '' }}">
    </div>
    <div class="form-group">
        <label>@lang('app.postal_code')</label>
        <input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="(@lang('app.optional'))" value="{{ $edit ? $user->postalCode : '' }}">
    </div>
    <div class="form-group">
        <label>@lang('app.currency')</label>
        {!! Form::select('currency', array_pluck($currencies, 'currency', 'id') , $user->currency,
            ['class' => 'form-control', 'id' => 'currency']) !!}
    </div>
    <div class="form-group">
        <label>@lang('app.lang')</label>
        {!! Form::select('language', $langs, $edit ? $user->language : '', ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        <label>{{ $edit ? trans("app.new_password") : trans('app.password') }}</label>
        <input type="password" class="form-control" id="password" name="password" @if ($edit) placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" @endif>
    </div>

    <div class="form-group">
        <label>{{ $edit ? trans("app.confirm_new_password") : trans('app.confirm_password') }}</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" @if ($edit) placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" @endif>
    </div>

</div>

<div class="box-footer">
    <button type="submit" class="btn btn-primary" id="update-details-btn">
        @lang('app.edit_user')
    </button>
</div>
