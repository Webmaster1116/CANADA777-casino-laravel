<tr>
	<td>
		<a href="{{ route('backend.statistics', ['system_str' => $stat->admin ? $stat->admin->username : $stat->system])  }}">
			{{ $stat->admin ? $stat->admin->username : $stat->system }}
		</a>
		@if( $stat->value ) {{ $stat->value }} @endif
	</td>
<td>
		@if ($stat->type == 'in')
			<span class="text-green">{{ abs($stat->summ) }}</span>
		@endif
</td>
	<td>
		@if ($stat->type != 'in')
			<span class="text-red">{{ abs($stat->summ) }}</span>
		@endif
	</td>
</td>
	<td>
		@if ($stat->user)
		<a href="{{ route('backend.statistics', ['user' => $stat->user->username])  }}">
			{{ $stat->user->username }}
		</a>
		@endif
	</td>

<td>{{ $stat->created_at->format(config('app.date_time_format')) }}</td>
<td>{{ $stat->getStatus() }}</td>

</tr>