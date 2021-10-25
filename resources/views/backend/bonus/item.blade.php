<tr>
	<td>{{ $item->id }}</td>
	<td><a href="{{ route('backend.bonus.edit', $item->id) }}">{{ $item->name }}</a></td>
	<td>{{ $item->getType() }}</td> 
	<td>{{ $item->valid_from }}</td>
    <td>{{ $item->valid_until }}</td>
	<td>{{ $item->getDays() }}</td>
	<td>{{ $item->deposit_min }}</td>
    <td>{{ $item->deposit_max }}</td>
	<td>{{ $item->match_win }}</td>
	<td>{{ $item->code }}</td>
    <td>{{ $item->wagering }}</td>
	<td>
		@if(!$item->active)
			<small><i class="fa fa-circle text-red"></i></small>
		@else
			<small><i class="fa fa-circle text-green"></i></small>
		@endif
	</td>
</tr>