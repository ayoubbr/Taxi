@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="{{ asset('images/taxi-app.png') }}" class="logo" alt="Taxi logo"
                    style="width: auto; max-height: 65px;">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
