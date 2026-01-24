<table>
    <thead>
        <tr>
            <th>Short</th>
            <th>SAP</th>
            <th>Customer</th>
            <th>City</th>
            <th>Project</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Hours</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($open_projects as $i)
            @php
                $statusName = optional($i->status)->name;
                $color = \App\Helpers\StatusHelper::color($statusName);
                $textColor = \App\Helpers\StatusHelper::textColor($statusName);
            @endphp
            <tr>
                <td style="text-align: left">{{ $i->customer->short_no }}</td>
                <td style="text-align: left"><a href="{{ route('customers.view', $i->customer->id) }}">{{ $i->customer->sap_no }}</a></td>
                <td style="text-align: left">{{ $i->customer->name }}</td>
                <td style="text-align: left">
                    <a href="{{ route('customers.city', $i->customer->city->id) }}">
                        <img src="/assets/flags/{{ $i->customer->city->country_code }}.png"> {{ $i->customer->city->name }}
                    </a>
                </td>
                @php
                    $endDate = \Carbon\Carbon::parse($i->end_date)->startOfDay();
                    $today = \Carbon\Carbon::today();
                    $daysRemaining = $today->diffInDays($endDate, false);
                    if ($daysRemaining > 0) {
                        $endBg = 'green';
                    } elseif ($daysRemaining === 0) {
                        $endBg = 'orange';
                    } else {
                        $endBg = 'red';
                    }
                @endphp
                <td style="text-align: left">{{ $i->name }}</td>
                <td style="text-align: left;">{{ \Carbon\Carbon::parse($i->start_date)->toDateString() }}</td>
                <td style="text-align: left; background-color: {{ $endBg }};">
                    {{ \Carbon\Carbon::parse($i->end_date)->toDateString() }} ({{ $daysRemaining }})
                </td>
                <td style="text-align: left;">{{ $i->hours ?? '-' }}</td>
                <td style="text-align: left;background-color: {{ $color }};color: {{ $textColor }};">{{ $i->status->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
