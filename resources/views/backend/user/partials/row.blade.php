<tr>
    <td>
        <a href="{{ route('backend.user.edit', $user->id) }}">
            {{ $user->username ?: trans('app.n_a') }}
        </a>
    </td>
    <td>
        <a href="{{ route('backend.user.edit', $user->id) }}">
            {{ $user->created_at }}
        </a>
    </td>
	@permission('users.balance.manage')
	<td>{{ $user->last_login ? $user->last_login : $user->created_at}}</td>
	<td>{{ $user->balance }}</td>
	<td>{{ $user->bonus }}</td>
	<td>{{ $user->wager }}</td>
	<td>
		<a class="editMoney" href="#" data-toggle="modal" data-target="#openEditModal" data-id="{{ $user->id }}" data-username="{{$user->username}}" data-balance="{{$user->balance}}" data-bonus="{{$user->bonus}}" data-wager="{{$user->wager}}">
		<button type="button" class="btn btn-block btn-success btn-xs">Edit</button>
		</a>
	</td>
    @endpermission

	@if(isset($show_shop) && $show_shop)
		@if($user->shop)
			<td><a href="{{ route('backend.shop.edit', $user->shop->id) }}">{{ $user->shop->name }}</a></td>
			@else
			<td></td>
		@endif
	@endif
</tr>