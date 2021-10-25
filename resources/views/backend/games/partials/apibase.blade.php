 <div class="row">
		@if (!$edit || $apigame->name !== '')
    <div class="col-md-6">
		<div class="form-group">
            <label for="name">@lang('app.name')</label>
            <input type="text" class="form-control" id="name"
                   name="name" placeholder="@lang('app.name')" {{ $edit ? 'disabled' : '' }} value="{{ $edit ? $apigame->name : '' }}" required>
        </div>
    </div>
		@endif
            <div class="col-md-12">
                <div class="form-group">
                    <label for="category">@lang('app.categories')</label>
                    <input type="text" class="form-control" id="category"
                   name="category" placeholder="" {{ $edit ? 'disabled' : '' }} value="{{ $edit ? $apigame->category : '' }}" required>
                </div>
            </div>

    <div class="col-md-6">
		<div class="form-group">
            <label for="device">@lang('app.device')</label>
            <input type="text" class="form-control" id="device"
                   name="device" placeholder="" {{ $edit ? 'disabled' : '' }} value="{{ $apigame->mobile == 1 ? 'Mobile' : 'Desktop' }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="title">@lang('app.labels')</label>
            {!! Form::select('label', ['' => '---'] + $apigame->labels, $edit ? $apigame->label : '', ['class' => 'form-control']) !!}
        </div>
    </div>

	

    @if ($edit)
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary" id="update-details-btn">
                @lang('app.edit_game')
            </button>
        </div>
    @endif
</div>