@php
	$keyName = $column['key'] ?? $column['name'] ?? '-';
	$entryValue = $entry['attributes'][$keyName] ?? '-';
	$displayValue = $column['options'][$entryValue] ?? '';
@endphp
<td>{{ $displayValue }}</td>
