<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.name')</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="@lang('app.name')" required value="{{ $edit ? $smart_list['name'] : '' }}">
    </div>
    @if($edit == true)
    <div class="form-group" style="display:flex; justify-content: center; align-items:center">
        <div class = "col-sm-12" style="display:flex; flex-direction:column; justify-content: center; align-items:center">
            <label class="col-sm-12">@lang('app.contacts')</label>
            <div class="col-sm-12" style="display:flex; flex-direction:row; justify-content: center; align-items:flex-start; border: 3px solid #cccccc;">
                <div class="col-sm-6">
                    <label class="col-sm-12" style="padding: 5px">Added Contacts</label>
                    <table class="table table-bordered table-striped" id="automizy_registered_contact_table">
                        <thead>
                            <tr>
                                <th>@lang('app.id')</th>
                                <th>@lang('app.email')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if (count($automizy_contacts) > 0)
                            @foreach ($automizy_contacts as $existing_contact)
                                <tr>
                                    <td>{{$existing_contact['id']}}</td>
                                    <td>{{$existing_contact['email']}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="9">@lang('app.no_data')</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-6">
                    <label class="col-sm-12" style="padding: 5px">Unadded Contacts</label>
                    <table class="table table-bordered table-striped" id="automizy_unregistered_contact_table">
                        <thead>
                            <tr>
                                <th>@lang('app.id')</th>
                                <th>@lang('app.email')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if (count($new_contacts) > 0)
                            @foreach ($new_contacts as $key => $new_contact)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$new_contact}}</td>
                                    <td>
                                        <a href="{{route('backend.automizy.add_contacts', ['id'=>$smart_list['id'], 'email'=>$new_contact ])}}" class="btn btn-danger" data-method="POST" data-confirm-title="Please Confirm" data-confirm-text="Are you sure add these contacts on automizy?" data-confirm-delete="Yes, add it!">
                                        Add
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="9">@lang('app.no_data')</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@section('scripts')
	<script>
		$('#automizy_registered_contact_table').dataTable();
		$('#automizy_unregistered_contact_table').dataTable();
       
	</script>
@stop