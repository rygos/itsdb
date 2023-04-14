<ul>
    <li><a href="{{ route('index') }}">Home</a></li>
    <li><a href="{{ route('calendar.index') }}">Calendar</a></li>
    <li><a href="{{ route('customers.index') }}">Customers</a></li>

    {{-- START:Compose LINKS --}}
    <li><a style="background-color: darkred" href="{{ route('compose.index') }}">Compose</a></li>
    {{-- END:Compose LINKS --}}

    {{-- START:ADDING LINKS --}}
    <li><a style="background-color: darkgreen" href="{{ route('customers.add') }}">Add Customer</a></li>
    <li><a style="background-color: darkgreen" href="{{ route('projects.add') }}">Add Project</a></li>
    {{-- END:ADDING LINKS --}}
</ul>
