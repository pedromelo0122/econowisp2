{{-- custom return value --}}
@php
	$xPanel ??= null;
@endphp
<td>
	@php
		echo $entry->{$column['function_name']}($xPanel);
	@endphp
</td>
