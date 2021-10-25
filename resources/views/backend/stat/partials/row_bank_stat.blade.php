<tr>
    <td>{{ $stat->name }}</td>
	<td>{{ $stat->user->username }}</td>
	<td>{{ $stat->old }}</td>
	<td>{{ $stat->new }}</td>
	<td>
		@if ($stat->type == 'in')
			<span class="text-green">
		@endif
		{{ abs($stat->sum) }}
		</span>
	</td>
	<td>
		@if ($stat->type != 'in')
					<span class="text-red">
		@endif
						{{ abs($stat->sum) }}
		</span>
	</td>
	<td>{{ $stat->created_at->format(config('app.date_time_format')) }}</td>
</tr>