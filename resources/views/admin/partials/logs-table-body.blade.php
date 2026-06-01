@forelse($logs as $log)
    <tr>
        <td>{{ $log->id }}</td>
        <td>{{ $log->user_name ?? '-' }}</td>
        <td>{{ $log->role ?? '-' }}</td>
        <td>{{ $log->module }}</td>
        <td>{{ $log->action }}</td>
        <td>
            @php
                $bookingReference = data_get($log, 'booking_reference') ?? data_get($log, 'meta.booking_reference');
                $bookingId =
                    data_get($log, 'booking_id') ??
                    (data_get($log, 'subject_type') === App\Models\Booking::class
                        ? data_get($log, 'subject_id')
                        : null);
            @endphp

            @if ($bookingReference)
                {{ $bookingReference }}
                @if ($bookingId)
                    <br><small class="text-muted">ID: {{ $bookingId }}</small>
                @endif
            @else
                -
            @endif
        </td>
        <td>{{ $log->description }}</td>
        <td><code>{{ $log->ip_address ?? '-' }}</code></td>
        <td>{{ $log->activity_at ?? '-' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center">No logs found.</td>
    </tr>
@endforelse
