
    {{-- Header --}}
 
    {{-- Row: MCO Summary + Filter Tabs --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h5 class="mb-1 h5">MCO Performance</h5>
                            <p class="text-muted mb-0 small">Total MCO value and records by period</p>
                        </div>
                        <ul class="nav nav-pills mt-3 mt-md-0" id="mcoFilterTabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-period="today">Today</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-period="week">This Week</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-period="month">This Month</button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body">
                    {{-- MCO KPI boxes --}}
                    <div class="row text-center mb-4" id="mcoKpiRow">
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="p-3 rounded-3 bg-light">
                                <p class="text-muted mb-1">MCO Records</p>
                                <h4 class="mb-0" id="mcoCount">0</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="p-3 rounded-3 bg-light">
                                <p class="text-muted mb-1">Total MCO Value</p>
                                <h4 class="mb-0" id="mcoTotal">$0.00</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-light">
                                <p class="text-muted mb-1">Avg. MCO / Booking</p>
                                <h4 class="mb-0" id="mcoAvg">$0.00</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-light">
                                <p class="text-muted mb-1">Conversion Rate</p>
                                <h4 class="mb-0" id="mcoConversion">0%</h4>
                            </div>
                        </div>
                    </div>

                    {{-- MCO Chart --}}
                    <div class="position-relative" style="min-height: 260px;">
                        <canvas id="mcoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row: Top Agents --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h5 class="mb-1">Top Performing Agents (This Month)</h5>
                        <p class="text-muted mb-0">Based on total bookings and MCO generated</p>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success d-inline-flex align-items-center mt-3 mt-md-0">
                        <i class="bi bi-trophy-fill me-1"></i> Leaderboard
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Agent Name</th>
                                    <th>Alias</th>
                                    <th class="text-center">Total Bookings</th>
                                    <th class="text-center">MCO Generated</th>
                                </tr>
                            </thead>
                            <tbody id="topAgentsTableBody">
                                {{-- Dummy rows (will be populated by JS) --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const mcoDataSets = @json($mcoStats);
        const topAgentsData = @json($topAgents);

        function formatCurrency(value) {
            return '$' + Number(value || 0).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function formatPercent(value) {
            return Number(value || 0).toFixed(0) + '%';
        }

        function renderTopAgentsTable() {
            const tbody = document.getElementById('topAgentsTableBody');
            tbody.innerHTML = '';

            if (!topAgentsData.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No data found for this month.</td>
                    </tr>
                `;
                return;
            }

            topAgentsData.forEach((agent) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${agent.rank}</td>
                    <td>${agent.name}</td>
                    <td>${agent.alias}</td>
                    <td class="text-center">${agent.bookings}</td>
                    <td class="text-center">${formatCurrency(agent.mco)}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        let mcoChartInstance = null;

        function renderMcoChart(periodKey) {
            const ctx = document.getElementById('mcoChart').getContext('2d');
            const dataSet = mcoDataSets[periodKey];

            if (!dataSet) return;

            document.getElementById('mcoCount').textContent = dataSet.count ?? 0;
            document.getElementById('mcoTotal').textContent = formatCurrency(dataSet.total);
            document.getElementById('mcoAvg').textContent = formatCurrency(dataSet.avg);
            document.getElementById('mcoConversion').textContent = formatPercent(dataSet.conversion);

            const chartConfig = {
                type: 'line',
                data: {
                    labels: dataSet.labels,
                    datasets: [{
                        label: 'MCO Value',
                        data: dataSet.values,
                        borderColor: 'rgba(13, 110, 253, 1)',
                        backgroundColor: 'rgba(13, 110, 253, 0.10)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + formatCurrency(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            };

            if (mcoChartInstance) {
                mcoChartInstance.destroy();
            }

            mcoChartInstance = new Chart(ctx, chartConfig);
        }

        function initMcoTabs() {
            const tabs = document.querySelectorAll('#mcoFilterTabs .nav-link');

            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const period = this.getAttribute('data-period');
                    renderMcoChart(period);
                });
            });

            renderMcoChart('today');
        }

        document.addEventListener('DOMContentLoaded', function () {
            renderTopAgentsTable();
            initMcoTabs();
        });
    </script>
@endpush