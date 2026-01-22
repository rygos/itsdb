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
                switch($i->status->name){
                    case 'NEW':
                        $color = 'none';
                        break;
                    case 'WIP':
                        $color = 'orange';
                        break;
                    case 'CHECK':
                        $color = 'blue';
                        break;
                    case 'ON HOLD':
                        $color = 'red';
                        break;
                    case 'WAIT FOR INFO':
                        $color = 'yellow';
                        break;
                    case 'FINISHED':
                        $color = 'green';
                        break;
                    default:
                        $color = 'none';
                }
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
                <td style="text-align: left">{{ $i->name }}</td>
                <td style="text-align: left;">{{ $i->start_date }}</td>
                <td style="text-align: left;">{{ $i->end_date }}</td>
                <td style="text-align: left;">{{ \App\Helpers\MiscHelper::work_hours_diff($i->start_date, $i->end_date) }}</td>
                <td style="text-align: left;background-color: {{ $color }};">{{ $i->status->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
