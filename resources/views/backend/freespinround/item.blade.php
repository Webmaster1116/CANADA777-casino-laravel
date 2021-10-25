<tr>
	<td>{{ $item->id }}</td>
	<td><a href="{{ route('backend.freespinround.edit', $item->id) }}">{{ $item->title }}</a></td>
	<td>{{ $item->free_rounds }}</td>
	<td>{{ $item->bet_type }}</td>
	<td>{{ $item->valid_from }}</td>
    <td>{{ $item->valid_to }}</td>
	<td>
		@if($item->active == 0)
			<small><i class="fa fa-circle text-red"></i></small>
		@else
			<small><i class="fa fa-circle text-green"></i></small>
		@endif
	</td>
</tr>