<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.message')</label>
        <Textarea class="form-control" id="message" name="message" placeholder="@lang('app.message')" required style="height: 150px">{{ $edit ? $notification->message : '' }}</Textarea>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.image')</label>
        <input type="file" name="image" id="image" value="{{ $edit ? $notification->image : ''}}"/>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        @if($edit == true)
            @if($notification->campaign == 0)
                <input type="checkbox" id="campaign" name="campaign" }}>
            @else
                <input type="checkbox" id="campaign" name="campaign" checked="checked" }}>
            @endif
        @else
            <input type="checkbox" id="campaign" name="campaign">
        @endif
        <label>@lang('app.schedule_campaign')</label>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>Date</label>
        <input type="text" class="form-control" id="valid_from" name="valid_from" placeholder="Valid From Date" required value="{{ $edit ? $notification->notify_date : '' }}">
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>Time</label>
        <input type="text" class="form-control" id="valid_time" name="valid_time" placeholder="Valid To Time" required value="{{ $edit ? $notification->notify_time : '' }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.frequency')</label>
        {!! Form::select('frequency_type', $frequency_type, $edit ? $notification->frequency : '', ['id' => 'frequency', 'class' => 'form-control']) !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>Active</label>
        {!! Form::select('active', [ 1 => 'Active', 0 => 'UnActive'], $edit ? $notification->active : '', ['id' => 'active', 'class' => 'form-control']) !!}
    </div>
</div>