{{ Form::open(['route' => 'logout']) }}
@if(env('APP_DEV') == true)
    @php $style = 'background-color: darkred;' @endphp
@else
    @php $style = '' @endphp
@endif
<ul style="{{$style}}">
    <li><a href="{{ route('index') }}">Home</a></li>
    @if(auth()->user()?->hasPermission('calendar', 'visible'))
        <li><a href="{{ route('calendar.index') }}">Calendar</a></li>
    @endif
    @if(auth()->user()?->hasPermission('customers', 'visible'))
        <li><a href="{{ route('customers.index') }}">Customers</a></li>
    @endif
    @if(auth()->user()?->hasPermission('hours', 'visible'))
        <li><a href="{{ route('hours.index') }}">Stunden</a></li>
    @endif
    @if(auth()->user()?->hasPermission('product_matrix', 'visible'))
        <li><a href="{{ route('product_matrix.index') }}">Produkte Matrix</a></li>
    @endif

    {{-- START:Compose LINKS --}}
    @if(auth()->user()?->hasPermission('compose', 'visible'))
        <li><a style="background-color: darkred" href="{{ route('compose.index') }}">Compose</a></li>
    @endif
    {{-- END:Compose LINKS --}}

    {{-- START:ADDING LINKS --}}
    @if(auth()->user()?->hasPermission('projects', 'editable'))
        <li><a style="background-color: darkgreen" href="{{ route('customers_projects.add') }}">Add Customer + Project</a></li>
    @endif
    {{-- END:ADDING LINKS --}}

    @if(auth()->user()?->hasPermission('administration', 'visible'))
        <li><a class="adminlink" href="{{ route('administration.index') }}">Administration</a></li>
    @endif

    @if(\Illuminate\Support\Facades\Auth::check())
        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
        </li>
    @endif
</ul>
{{ Form::close() }}
