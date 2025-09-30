{{-- enumerate the values in an array  --}}
<td>
	@php
		$value = $entry->{$column['name']};
		// The value should be an array weather or not attribute casting is used
		if (!is_array($value)) {
			$value = json_decode($value, true);
		}
		if ($value && count($value)) {
			echo implode(', ', $value);
		} else {
			echo '-';
		}
	@endphp
</td>
