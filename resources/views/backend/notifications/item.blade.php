<tr>
	<td>{{ $item->id }}</td>
	<td><a href="{{ route('backend.notifications.edit', $item->id) }}">{{ $item->message }}</a></td>
	<td><img style="width:100px; height:100px; object-fit:cover" src="/notify/{{ $item->image }}" alt=""/></td>
	<td>
		@if($item->campaign == 0)
			<small><i class="fa fa-close text-red"></i></small>
		@else
			<small><i class="fa fa-check text-green"></i></small>
		@endif
	</td>
	<td>{{ $frequency_type[$item->frequency] }}</td>
    <td>{{ $item->notify_date.' '.$item->notify_time }}</td>
	<td>
		@if($item->active == 0)
			<small><i class="fa fa-circle text-red"></i></small>
		@else
			<small><i class="fa fa-circle text-green"></i></small>
		@endif
	</td>
</tr>