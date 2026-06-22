<style>
    .ticket-card {
        max-width: 920px;
        margin: 0 auto 2.5rem auto;
        background: white;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07);
        border: 1px solid #e6edf4;
        overflow: hidden;
    }

    .ticket-header {
        background: #003366;
        padding: 0.9rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .ticket-header .brand {
        font-weight: 700;
        font-size: 1.3rem;
        color: white;
        letter-spacing: -0.2px;
    }

    .ticket-header .brand i {
        margin-right: 6px;
    }

    .ticket-header .badge-nonref {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        padding: 0.25rem 1rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .ticket-body {
        padding: 1.2rem 1.5rem 1.5rem;
    }

    /* summary row */
    .route-summary {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        align-items: center;
        border-bottom: 1px dashed #dce3ec;
        padding-bottom: 0.8rem;
        margin-bottom: 1rem;
    }

    .route-city {
        font-weight: 600;
        font-size: 1.1rem;
        color: #0b1e33;
    }

    .route-city small {
        font-weight: 400;
        color: #5e6f8d;
        font-size: 0.85rem;
    }

    .meta-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem 0.8rem;
        font-size: 0.8rem;
        color: #1f3a5e;
    }

    .meta-pills .pill {
        background: #eef2f7;
        padding: 0.1rem 0.7rem;
        border-radius: 30px;
    }

    /* flight meta box */
    .flight-meta-box {
        background: #f8fafc;
        border-radius: 14px;
        padding: 0.7rem 1.2rem;
        border: 1px solid #e9eef4;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 2rem;
        margin-bottom: 1.2rem;
    }

    .meta-item {
        font-size: 0.8rem;
        color: #1f3a5e;
    }

    .meta-item strong {
        font-weight: 600;
        color: #0b1e33;
    }

    .meta-item .label {
        color: #6a7e9c;
    }

    /* flight leg */
    .flight-leg {
        background: white;
        border-radius: 16px;
        border: 1px solid #e6edf4;
        padding: 0.8rem 1.2rem;
        margin-bottom: 0.8rem;
    }

    .flight-leg:last-child {
        margin-bottom: 0;
    }

    .leg-header {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 0.3rem;
    }

    .flight-number {
        font-weight: 700;
        color: #003366;
        background: #e8f0fe;
        padding: 0.1rem 0.7rem;
        border-radius: 30px;
        font-size: 0.75rem;
    }

    .aircraft {
        color: #2e405b;
        font-size: 0.75rem;
        font-weight: 500;
        margin-left: 0.3rem;
    }

    .leg-detail {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 0.3rem 0.5rem;
    }

    .time-place {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.2rem 0.6rem;
    }

    .time {
        font-weight: 700;
        font-size: 0.95rem;
        color: #0b1e33;
    }

    .time small {
        font-weight: 400;
        font-size: 0.65rem;
        color: #5e6f8d;
    }

    .place {
        font-size: 0.85rem;
        font-weight: 500;
        color: #1d3752;
    }

    .place small {
        font-weight: 400;
        color: #6a7e9c;
        font-size: 0.7rem;
    }

    .layover-badge {
        background: #f4f7fc;
        border-radius: 40px;
        padding: 0.1rem 0.9rem;
        font-size: 0.7rem;
        font-weight: 500;
        color: #004080;
        display: inline-block;
        margin-top: 0.3rem;
    }

    .footer-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.2rem;
        padding-top: 0.8rem;
        border-top: 1px solid #e6edf4;
        font-size: 0.75rem;
        color: #3e5677;
    }

    .footer-meta .eticket {
        font-weight: 600;
        color: #003366;
    }

    .wheelchair-badge {
        background: #e6f0fa;
        padding: 0.1rem 0.8rem;
        border-radius: 30px;
        font-weight: 500;
    }

    /* small screens */
    @media (max-width: 600px) {
        .ticket-body {
            padding: 1rem;
        }

        .flight-leg {
            padding: 0.6rem 0.8rem;
        }

        .leg-detail>div {
            flex: 0 0 100% !important;
            min-width: auto !important;
        }

        .leg-detail>div[style*="text-align:center"] {
            flex: 0 0 100% !important;
        }
    }
</style>

<div class="ticket-card">
    <div class="ticket-header">
        <span class="brand"><i class="fas fa-plane"></i> Delta <small
                style="font-weight:400; font-size:0.8rem; opacity:0.8;">e-ticket</small></span>
        <span class="badge-nonref">Non-Refundable</span>
    </div>
    <div class="ticket-body">
        <!-- summary -->
        <div class="route-summary">
            <div class="route-city">Nassau, Bahamas <small>· Thu, Jun 4 – Tue, Jun 9 2026</small></div>
            <div class="meta-pills">
                <span><i class="fas fa-sync-alt"></i> Round Trip</span>
                <span>|</span>
                <span><i class="fas fa-user"></i> 1 Passenger</span>
                <span>|</span>
                <span class="pill">Ticket Exp: Jun 2, 2027</span>
            </div>
        </div>

        <!-- meta row -->
        <div class="flight-meta-box">
            <span class="meta-item"><span class="label">eTicket</span> <strong>#0062435634758</strong></span>
            <span class="meta-item"><span class="label">Accessible</span> <strong>Wheelchair Assistance</strong></span>
            <span class="meta-item"><span class="label">Class</span> <strong>33D Delta Main Basic (E)</strong></span>
        </div>

        <!-- OUTBOUND 1 -->
        <div class="flight-leg">
            <div class="leg-header">
                <span><span class="flight-number">DL2233</span> <span class="aircraft">Boeing 737-900</span></span>
                <span style="font-size:0.7rem; color:#2e405b;">On Time 8:05am · THU, JUN 4</span>
            </div>
            <div class="leg-detail" style="display:flex; flex-wrap:wrap; align-items:center; gap:0.3rem 0.5rem;">
                <div style="flex:0 0 42%; min-width:140px;">
                    <div class="time-place"><span class="time">8:05 AM</span> <span
                            class="place">Hartford/Springfield, CT (BDL) <small>Terminal TBD (A10)</small></span></div>
                </div>
                <div style="flex:0 0 8%; text-align:center; color:#7a8aa5;"><i class="fas fa-arrow-right"></i></div>
                <div style="flex:0 0 42%; min-width:140px;">
                    <div class="time-place"><span class="time">10:33 AM</span> <span class="place">Atlanta, GA (ATL)
                            <small>Domestic Term-South (A28)</small></span></div>
                </div>
            </div>
            <div class="layover-badge">Layover | 1h 25m · Change planes in Atlanta, GA (ATL)</div>
        </div>

        <!-- OUTBOUND 2 -->
        <div class="flight-leg">
            <div class="leg-header">
                <span><span class="flight-number">DL1938</span> <span class="aircraft">Boeing 737-900</span></span>
                <span style="font-size:0.7rem; color:#2e405b;">On Time 11:58am</span>
            </div>
            <div class="leg-detail" style="display:flex; flex-wrap:wrap; align-items:center; gap:0.3rem 0.5rem;">
                <div style="flex:0 0 42%; min-width:140px;">
                    <div class="time-place"><span class="time">11:58 AM</span> <span class="place">Atlanta, GA (ATL)
                            <small>International Term (A28)</small></span></div>
                </div>
                <div style="flex:0 0 8%; text-align:center; color:#7a8aa5;"><i class="fas fa-arrow-right"></i></div>
                <div style="flex:0 0 42%; min-width:140px;">
                    <div class="time-place"><span class="time">2:04 PM</span> <span class="place">Nassau, Bahamas
                            (NAS) <small>Terminal A (C46)</small></span></div>
                </div>
            </div>
            <div style="font-size:0.7rem; color:#004080; margin-top:0.1rem;">Total Duration 5h 59m</div>
        </div>

        <!-- RETURN 1 -->
        <div class="flight-leg">
            <div class="leg-header">
                <span><span class="flight-number">DL1965</span> <span class="aircraft">Boeing 737-900</span></span>
                <span style="font-size:0.7rem; color:#2e405b;">1:17pm · TUE, JUN 9</span>
            </div>
            <div class="leg-detail" style="display:flex; flex-wrap:wrap; align-items:center; gap:0.3rem 0.5rem;">
                <div style="flex:0 0 42%; min-width:140px;">
                    <div class="time-place"><span class="time">1:17 PM</span> <span class="place">Nassau, Bahamas
                            (NAS) <small>Terminal A (Gate TBD)</small></span></div>
                </div>
                <div style="flex:0 0 8%; text-align:center; color:#7a8aa5;"><i class="fas fa-arrow-right"></i></div>
                <div style="flex:0 0 42%; min-width:140px;">
                    <div class="time-place"><span class="time">—</span> <span class="place">Atlanta, GA (ATL)
                            <small>Layover 3h 7m</small></span></div>
                </div>
            </div>
            <div class="layover-badge">Layover | 3h 7m · Change planes in Atlanta, GA (ATL)</div>
        </div>

        <!-- RETURN 2 -->
        <div class="flight-leg">
            <div class="leg-header">
                <span><span class="flight-number">DL2504</span> <span class="aircraft">Boeing 737-900</span></span>
                <span style="font-size:0.7rem; color:#2e405b;">6:50pm</span>
            </div>
            <div class="leg-detail" style="display:flex; flex-wrap:wrap; align-items:center; gap:0.3rem 0.5rem;">
                <div style="flex:0 0 42%; min-width:140px;">
                    <div class="time-place"><span class="time">6:50 PM</span> <span class="place">Atlanta, GA
                            (ATL)</span></div>
                </div>
                <div style="flex:0 0 8%; text-align:center; color:#7a8aa5;"><i class="fas fa-arrow-right"></i></div>
                <div style="flex:0 0 42%; min-width:140px;">
                    <div class="time-place"><span class="time">9:17 PM</span> <span
                            class="place">Hartford/Springfield, CT (BDL)</span></div>
                </div>
            </div>
        </div>

        <!-- footer -->
        <div class="footer-meta">
            <span class="eticket"><i class="fas fa-ticket-alt"></i> #0062435634758</span>
            <span class="wheelchair-badge"><i class="fas fa-wheelchair"></i> Wheelchair Assistance</span>
            <span>33D · Main Basic (E)</span>
        </div>
    </div>
</div>
