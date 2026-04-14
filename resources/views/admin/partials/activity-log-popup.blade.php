
<button type="button" class="btn btn-primary position-relative mb-3" data-bs-toggle="modal" data-bs-target="#activityLogModal">
    Activity Logs
</button>

<div class="modal fade" id="activityLogModal" tabindex="-1" aria-labelledby="activityLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">All Activity Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="activity-log-wrapper">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date Time</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Module</th>
                                <th>Action</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody id="activity-log-body">
                            {{-- ajax rows --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('activityLogModal').addEventListener('shown.bs.modal', function () {
    // fetch("{{ route('admin.activity.logs.latest') }}")
        .then(res => res.json())
        .then(rows => {
            let html = '';
            rows.forEach(log => {
                html += `
                    <tr>
                        <td>${log.activity_at ?? ''}</td>
                        <td>${log.user_name ?? '-'}</td>
                        <td>${log.role ?? '-'}</td>
                        <td>${log.module}</td>
                        <td>${log.action}</td>
                        <td>${log.description}</td>
                    </tr>
                `;
            });
            document.getElementById('activity-log-body').innerHTML = html;
        });
});
</script>
@endpush
