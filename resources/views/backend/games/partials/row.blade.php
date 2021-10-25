<tr>
    <td>
		@permission('games.edit')
		<a href="{{ route('backend.game.edit', $game->id) }}">
		@endpermission

		{{ $game->title }}

		@permission('games.edit')
		</a>
		@endpermission
	</td>
	<td>
		Original GAME
	</td>
	<!-- Classified games order -->
	<td>
		<input type="number" class="game-order-input" value="{{ $game->order }}" data-gameid="{{ $game->original_id }}" data-gametype="original"/>
		<button type="button" class="btn btn-success btn-sm order-update">Update</button>
	</td>
	
	@if ( count($savedCategory) )
		@foreach ($savedCategory as $game_category)
			@if($game_category == 1)
				<td>
					<input type="number" class="game-order-input" value="{{ $game->hot_order }}" data-gameid="{{ $game->original_id }}" />
					<button type="button" class="btn btn-success btn-sm hot-order-update">Update</button>
				</td>
			@endif
			@if($game_category == 2)
				<td>
					<input type="number" class="game-order-input" value="{{ $game->new_order }}" data-gameid="{{ $game->original_id }}" />
					<button type="button" class="btn btn-success btn-sm new-order-update">Update</button>
				</td>
			@endif
		@endforeach
	@endif
	<!-- --- -->
	@permission('games.in_out')
	<td>{{ $game->stat_in }}</td>
	<td>{{ $game->stat_out }}</td>
	<td>
		@if(($game->stat_in - $game->stat_out) >= 0)
			<span class="text-green">
		@else
			<span class="text-red">
		@endif	
		{{ number_format(abs($game->stat_in-$game->stat_out), 2, '.', '') }}
		</span>
	</td>
	@endpermission
	<td>{{ $game->bids }}</td>
	<td>{{ $game->denomination }}</td>
<td>

	<label class="checkbox-container">
		<input type="checkbox" name="checkbox[{{ $game->id }}]">
		<span class="checkmark"></span>
	</label>
			<!--
        <input class="custom-control-input minimal" id="cb-[{{ $game->id }}]" name="checkbox[{{ $game->id }}]" type="checkbox">

			-->
</td>
</tr>