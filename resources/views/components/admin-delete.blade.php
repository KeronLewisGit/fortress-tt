@props(['action', 'label' => 'Delete', 'confirm' => 'Delete this record? This cannot be undone.'])
<form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirm))" class="inline">@csrf @method('DELETE')<button class="admin-btn-danger">{{ $label }}</button></form>
