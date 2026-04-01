{{-- Sticky Bell + Modal --}}
<div class="position-fixed bottom-0 end-0 me-3 mb-3" style="z-index: 1050;">
    {{-- Bell button --}}
    <button id="notifyBell"
            class="btn btn-lg btn-outline-light rounded-circle p-2"
            data-bs-toggle="modal"
            data-bs-target="#notifyModal"
            aria-label="Notifications">
        <i class="bi bi-bell fs-4"></i>
        <span id="notifyBadge"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              style="font-size: .75rem; line-height: 1; width: 1.8rem; height: 1.8rem; display: none;">
            0
        </span>
    </button>
</div>

{{-- Notification Modal --}}
<div class="modal fade" id="notifyModal" tabindex="-1" aria-labelledby="notifyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="notifyModalLabel">
                    <i class="bi bi-bell me-2"></i>System Announcements
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                {{-- Re‑use the component you already have --}}
                @include('components.user-notifications')
            </div>
        </div>
    </div>
</div>

{{-- Styles & Scripts --}}
@push('styles')
<style>
    /* Pulse animation for the bell when there’s at least one unread */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(13,110,253,0.4); }
        70% { box-shadow: 0 0 0 10px rgba(13,110,253,0); }
        100% { box-shadow: 0 0 0 0 rgba(13,110,253,0); }
    }
    .bell-pulse {
        animation: pulse 2s infinite;
    }
    /* Make the bell slightly larger on hover */
    #notifyBell:hover { transform: scale(1.08); }
</style>
@endpush

@push('scripts')
<script>
    // ---- Helper: fetch unread count and update badge/pulse ----
    async function updateNotifyBadge() {
        try {
            const resp = await fetch('{{ route("admin.notifications.count") }}', {
                credentials: 'same-origin', // send Laravel session cookie
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!resp.ok) throw new Error('Bad response');
            const data = await resp.json(); // expects { count: X } or just a number
            const count = typeof data === 'object' ? data.count : data;
            const badge = document.getElementById('notifyBadge');
            const bell  = document.getElementById('notifyBell');

            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'block';
                bell.classList.add('bell-pulse');
            } else {
                badge.style.display = 'none';
                bell.classList.remove('bell-pulse');
            }
        } catch (e) {
            console.warn('Notification count failed:', e);
        }
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', () => {
        updateNotifyBadge();
        // Poll every 15 seconds (adjust as you like)
        setInterval(updateNotifyBadge, 15000);
    });

    // Optional: reset pulse when modal is opened (so it doesn’t keep flashing)
    const notifyModalEl = document.getElementById('notifyModal');
    if (notifyModalEl) {
        notifyModalEl.addEventListener('show.bs.modal', () => {
            document.getElementById('notifyBell').classList.remove('bell-pulse');
        });
    }
</script>
@endpush