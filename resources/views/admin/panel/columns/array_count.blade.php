{{-- enumerate the values in an array  --}}
<td>
	@php
		$array = $entry->{$column['name']};
		// The value should be an array weather or not attribute casting is used
		if (!is_array($array)) {
			$array = json_decode($array, true);
		}
		if ($array && count($array)) {
			echo count($array) . ' items';
		} else {
			echo '-';
		}
	@endphp
</td>
