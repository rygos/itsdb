{{ Form::open(['route' => 'logout']) }}
@if(env('APP_DEV') == true)
    @php $style = 'background-color: darkred;' @endphp
@else
    @php $style = '' @endphp
@endif
<ul style="{{$style}}">
    <li><a href="{{ route('index') }}">Home</a></li>
    <li><a href="{{ route('calendar.index') }}">Calendar</a></li>
    <li><a href="{{ route('customers.index') }}">Customers</a></li>
    <li><a href="{{ route('hours.index') }}">Stunden</a></li>

    {{-- START:Compose LINKS --}}
    <li><a style="background-color: darkred" href="{{ route('compose.index') }}">Compose</a></li>
    {{-- END:Compose LINKS --}}

    {{-- START:ADDING LINKS --}}
    <li><a style="background-color: darkgreen" href="{{ route('customers_projects.add') }}">Add Customer + Project</a></li>
    {{-- END:ADDING LINKS --}}

    @if(\Illuminate\Support\Facades\Auth::check())
        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
        </li>
    @endif
</ul>
{{ Form::close() }}
