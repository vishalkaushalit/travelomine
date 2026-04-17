
<p>Your reservation and payment authorization details are listed below.</p>

{{-- Example passenger loop --}}
<table>
    <thead>
        <tr>
            <th>S. No.</th>
            <th>Type</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>GND</th>
            <th>DOB</th>
        </tr>
    </thead>
    <tbody>
        @foreach($booking->passengers as $index => $pax)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $pax->type }}</td>
                <td>{{ $pax->first_name }}</td>
                <td>{{ $pax->middle_name }}</td>
                <td>{{ $pax->last_name }}</td>
                <td>{{ $pax->gender }}</td>
                <td>{{ $pax->dob }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

