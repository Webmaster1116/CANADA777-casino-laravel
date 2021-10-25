<div class="col-md-10">
    <div class="form-group">
        <label>@lang('app.title')</label>
        <input type="text" class="form-control" id="title" name="title" placeholder="@lang('app.title')" required value="{{ $edit ? $freespinround->title : '' }}">
    </div>
</div>
<div class="col-md-10">
    <div class="form-group">
        <label>@lang('app.players')</label>
        <select class="form-control select2" name="players[]" id="players" multiple="multiple" style="width: 100%;" data-placeholder="">
            @if($savedPlayer == 'all')
                <option value="all" selected='selected'>ALL</option>
                @foreach ($players as $player=>$key)
                    <option value="{{ $key }}" >{{ $player }}</option>
                @endforeach  
            @else
                <option value="all" >ALL</option>
                @foreach ($players as $player=>$key)
                    <option value="{{ $key }}" {{ (count($savedPlayer) && in_array($key, $savedPlayer))? 'selected="selected"' : '' }}>{{ $player }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>Original Games</label>
        <select class="form-control select2" name="games[]" id="games" multiple="multiple" style="width: 100%;" data-placeholder="">
            @if($savedGame == 'all')
                <option value="all" selected='selected'>ALL</option>
                @foreach ($games as $game=>$key)
                    <option value="{{ $key }}" >{{ $game }}</option>
                @endforeach 
            @else
                <option value="all" >ALL</option>
                @foreach ($games as $game=>$key)
                    <option value="{{ $key }}" {{ (count($savedGame) && in_array($key, $savedGame))? 'selected="selected"' : '' }}>{{ $game }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>Api Games</label>
        <select class="form-control select2" name="apigames[]" id="apigames" multiple="multiple" style="width: 100%;" data-placeholder="">
            @if($savedApiGame == 'all')
                <option value="all" selected='selected'>ALL</option>
                @foreach ($apigames as $apigame=>$key)
                    <option value="{{ $key }}">{{ $apigame }}</option>
                @endforeach
            @else
                <option value="all" >ALL</option>
                @foreach ($apigames as $apigame=>$key)
                    <option value="{{ $key }}" {{ (count($savedApiGame) && in_array($key, $savedApiGame))? 'selected="selected"' : '' }}>{{ $apigame }}</option>
                @endforeach
            @endif    
        </select>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>Valid From</label>
        <input type="text" class="form-control" id="valid_from" name="valid_from" placeholder="Valid From Date" required value="{{ $edit ? $freespinround->valid_from : '' }}">
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>Valid To</label>
        <input type="text" class="form-control" id="valid_to" name="valid_to" placeholder="Valid To Date" required value="{{ $edit ? $freespinround->valid_to : '' }}">
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>Bet</label>
        {!! Form::select('bet_type', [ 'min' => 'Min', 'mid' => 'Mid', 'max' => 'Max'], $edit ? $freespinround->bet_type : '', ['id' => 'bet_type', 'class' => 'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>Free Rounds</label>
        <input type="number" class="form-control" id="free_rounds" name="free_rounds" placeholder="Free Rounds" required value="{{ $edit ? $freespinround->free_rounds : '' }}">
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>Freerounds_added_notify</label>
        <!-- <input type="checkbox" class="form-control" id="notify" name="notify" value="{{ $edit ? $freespinround->notify : '' }}"> -->
        @if($edit == true)
            @if($freespinround->notify == 0)
                <input type="checkbox" id="notify" name="notify" }}>
            @else
                <input type="checkbox" id="notify" name="notify" checked="checked" }}>
            @endif
        @else
            <input type="checkbox" id="notify" name="notify">
        @endif
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>Active</label>
        {!! Form::select('active', [ 1 => 'Active', 0 => 'UnActive'], $edit ? $freespinround->active : '', ['id' => 'active', 'class' => 'form-control']) !!}
    </div>
</div>