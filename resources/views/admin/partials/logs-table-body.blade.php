@forelse($logs as $log)
    <tr>
        <td>{{ $log->id }}</td>
        <td>{{ $log->user_name ?? '-' }}</td>
        <td>{{ $log->role ?? '-' }}</td>
        <td>{{ $log->module }}</td>
        <td>{{ $log->action }}</td>
        <td>{{ $log->description }}</td>
        <td>{{ $log->activity_at }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">No logs found.</td>
    </tr>
@endforelse