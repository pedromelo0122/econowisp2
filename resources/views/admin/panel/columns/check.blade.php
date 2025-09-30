{{-- checkbox with loose false/null/0 checking --}}
@php
	$value = $entry->{$column['name']} ?? '';
	$strippedValue = strip_tags($value);
	
	$icon = 'fa fa-check-square-o';
	$icon = !empty($strippedValue) ? 'fa fa-square-o' : $icon;
@endphp
<td>
	<i class="{{ $icon }}"></i>
</td>
