<tr>
    <td>
		@permission('games.edit')
		<a href="{{ route('backend.game.apiedit', $apigame->game_id) }}">
		@endpermission

		{{ $apigame->name }}
		@permission('games.edit')
		</a>
		@endpermission
	</td>
	<td>
		API GAME
	</td>
	<!-- Classified games order -->
	<td>
		<input type="number" class="game-order-input" value="{{ $apigame->order }}" data-gameid="{{ $apigame->game_id }}" data-gametype="apigame"/>
		<button type="button" class="btn btn-success btn-sm order-update">Update</button>
	</td>
	
    <td>{{$apigame->credit_in}}</td>
    <td>{{$apigame->debit_out}}</td>
    <td>
    @if(($apigame->credit_in - $apigame->debit_out) >= 0)
        <span class="text-green">
    @else
        <span class="text-red">
    @endif	
    {{ number_format(abs($apigame->credit_in-$apigame->debit_out), 2, '.', '') }}
    </span>
    </td>
    <td></td>
    <td></td>
	
<td>

	<label class="checkbox-container">
		<input type="checkbox" name="checkbox[{{ $apigame->id }}]" Disabled>
		<span class="checkmark"></span>
	</label>
		
</td>
</tr>