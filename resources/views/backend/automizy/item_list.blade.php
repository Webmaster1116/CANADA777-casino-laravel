<tr>
	<td>{{ $item['id'] }}</td>
	<td><a href="{{ route('backend.automizy.edit_list', $item['id']) }}">{{ $item['name'] }}</a></td>
	<td>{{ $item['contactsCount'] }}</td>
</tr>