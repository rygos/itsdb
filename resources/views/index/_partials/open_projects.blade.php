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
                        $textColor = 'inherit';
                        break;
                    case 'WIP':
                        $color = 'orange';
                        $textColor = 'black';
                        break;
                    case 'CHECK':
                        $color = 'blue';
                        $textColor = 'white';
                        break;
                    case 'ON HOLD':
                        $color = 'red';
                        $textColor = 'white';
                        break;
                    case 'WAIT FOR INFO':
                        $color = 'yellow';
                        $textColor = 'black';
                        break;
                    case 'FINISHED':
                        $color = 'green';
                        $textColor = 'white';
                        break;
                    default:
                        $color = 'none';
                        $textColor = 'inherit';
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
                <td style="text-align: left;background-color: {{ $color }};color: {{ $textColor }};">{{ $i->status->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
