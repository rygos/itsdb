<table>
    <tr>
        <td style="background-color: #17395c" colspan="4">
            Last 5 Customers
        </td>
    </tr>
    @foreach($last5customers as $item)
        <tr>
            <td style="text-align: left;">{{ $item->short_no }}</td>
            <td style="text-align: left;">{{ $item->name }}</td>
            <td style="text-align: left;"><img src="assets/flags/{{ $item->city->country_code }}.png"></td>
            <td style="text-align: left;">{{ $item->city->name }}</td>
        </tr>
    @endforeach
</table>
