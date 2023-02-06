<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'SIKRAMAT')
<img src="https://sikramat.ngaeapp.com/assets/admin/assets/img/logo_prov_bali.png" class="logo" alt="Sikramat Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
