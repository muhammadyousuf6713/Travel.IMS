@extends('layouts.user_type.auth')

@section('content')
<style>
    /* Compact, clean typography and smooth natural scrolling */
    .app-page-container {
        width: 100%;
        padding-bottom: 2rem;
    }
    .kpi-card {
        border-radius: 10px;
        border: 1px solid #edf2f7;
        background: #ffffff;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .status-chip {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .custom-modal-scroll {
        max-height: 75vh;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        padding-right: 8px;
    }
    .custom-modal-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .custom-modal-scroll::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }
</style>

<div class="container-fluid py-3 app-page-container">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.15rem;">
                <i class="fa-solid fa-map-location-dot text-primary me-2"></i> Pilgrimage & Itinerary Operations
            </h5>
            <span class="text-xs text-secondary">Live journey tracking, checkpoints & hotel vouchers</span>
        </div>
        <div>
            <a href="{{ route('pilgrimage.create') }}" class="btn btn-primary btn-sm mb-0 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> New Booking
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success text-white alert-dismissible fade show py-2 px-3 mb-3" role="alert">
            <span class="text-xs font-weight-bold"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</span>
            @if(session('generated_voucher_id'))
                <a href="{{ route('pilgrimage.voucher.show', session('generated_voucher_id')) }}" target="_blank" class="btn btn-xs btn-light text-success ms-2 my-0">
                    <i class="fa-solid fa-file-pdf me-1"></i> View Hotel Voucher
                </a>
            @endif
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-white alert-dismissible fade show py-2 px-3 mb-3" role="alert">
            <span class="text-xs font-weight-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}</span>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Compact KPI Counters -->
    <div class="row g-2 mb-3">
        <div class="col-xl-3 col-sm-6">
            <div class="card kpi-card shadow-xs">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-xxs mb-0 text-uppercase font-weight-bold text-secondary">Active Bookings</p>
                        <h5 class="font-weight-bold mb-0 text-dark" style="font-size: 1.25rem;">{{ number_format($totalActive) }}</h5>
                    </div>
                    <div class="icon icon-shape bg-light text-primary shadow-none text-center border-radius-md" style="width: 38px; height: 38px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-route fs-6"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card kpi-card shadow-xs">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-xxs mb-0 text-uppercase font-weight-bold text-secondary">Due Today</p>
                        <h5 class="font-weight-bold mb-0 text-warning" style="font-size: 1.25rem;">{{ number_format($dueTodayCount) }}</h5>
                    </div>
                    <div class="icon icon-shape bg-light text-warning shadow-none text-center border-radius-md" style="width: 38px; height: 38px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-clock fs-6"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card kpi-card shadow-xs">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-xxs mb-0 text-uppercase font-weight-bold text-secondary">Overdue Checkpoints</p>
                        <h5 class="font-weight-bold mb-0 text-danger" style="font-size: 1.25rem;">{{ number_format($overdueCount) }}</h5>
                    </div>
                    <div class="icon icon-shape bg-light text-danger shadow-none text-center border-radius-md" style="width: 38px; height: 38px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-bell fs-6"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card kpi-card shadow-xs">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-xxs mb-0 text-uppercase font-weight-bold text-secondary">Completed</p>
                        <h5 class="font-weight-bold mb-0 text-success" style="font-size: 1.25rem;">{{ number_format($totalCompleted) }}</h5>
                    </div>
                    <div class="icon icon-shape bg-light text-success shadow-none text-center border-radius-md" style="width: 38px; height: 38px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-circle-check fs-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card shadow-xs border-0 border-radius-lg mb-3">
        <div class="card-body p-2">
            <form method="GET" action="{{ route('pilgrimage.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span>
                        <input type="text" name="search" class="form-control form-control-sm border-start-0 ps-0" placeholder="Search CRM ID, Passenger, Passport..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="service_type" class="form-select form-select-sm">
                        <option value="all" {{ request('service_type') == 'all' ? 'selected' : '' }}>All Services</option>
                        <option value="umrah" {{ request('service_type') == 'umrah' ? 'selected' : '' }}>Umrah</option>
                        <option value="hajj" {{ request('service_type') == 'hajj' ? 'selected' : '' }}>Hajj</option>
                        <option value="tour" {{ request('service_type') == 'tour' ? 'selected' : '' }}>Group Tour</option>
                        <option value="corporate" {{ request('service_type') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100 mb-0">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'service_type', 'status']))
                        <a href="{{ route('pilgrimage.index') }}" class="btn btn-sm btn-outline-secondary mb-0 px-2" title="Reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card shadow-xs border-0 border-radius-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead class="bg-light">
                        <tr class="text-secondary font-weight-bold text-xxs text-uppercase">
                            <th class="ps-3 py-2">CRM ID</th>
                            <th class="ps-2 py-2">Lead Passenger</th>
                            <th class="text-center py-2">Pax</th>
                            <th class="ps-2 py-2">Agent</th>
                            <th class="ps-2 py-2">Current Status</th>
                            <th class="ps-2 py-2">Next Checkpoint</th>
                            <th class="text-center pe-3 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            @php
                                $statusPhrase = $booking->dashboard_status;
                                $nextLeg = $booking->next_due_leg;
                            @endphp
                            <tr id="booking-row-{{ $booking->id }}" class="border-bottom">
                                <td class="ps-3 py-2">
                                    <span class="badge bg-dark text-white font-weight-bold text-xxs">
                                        {{ $booking->crm_id }}
                                    </span>
                                    <span class="text-xxs text-secondary text-uppercase d-block mt-1">{{ $booking->service_type }}</span>
                                </td>

                                <td class="ps-2 py-2">
                                    <span class="text-xs font-weight-bold text-dark d-block">{{ $booking->leader_name }}</span>
                                    @if($booking->leader_contact_no)
                                        <span class="text-xxs text-secondary">{{ $booking->leader_contact_no }}</span>
                                    @endif
                                </td>

                                <td class="text-center py-2">
                                    <span class="badge bg-light text-dark font-weight-bold border text-xxs px-2 py-1">
                                        {{ $booking->total_persons }} Pax
                                    </span>
                                </td>

                                <td class="ps-2 py-2">
                                    <span class="text-xs text-dark font-weight-semibold">{{ $booking->submittingAgent->name ?? 'Unassigned' }}</span>
                                </td>

                                <td class="ps-2 py-2" id="status-container-{{ $booking->id }}">
                                    @if($statusPhrase === 'Not started yet')
                                        <span class="status-chip bg-light text-secondary border">
                                            <i class="fa-regular fa-circle-dot"></i> Not started yet
                                        </span>
                                    @elseif(str_starts_with($statusPhrase, 'Arrived:'))
                                        <span class="status-chip bg-info text-white">
                                            <i class="fa-solid fa-plane-arrival"></i> {{ $statusPhrase }}
                                        </span>
                                    @elseif(str_starts_with($statusPhrase, 'Checked in'))
                                        <span class="status-chip bg-primary text-white">
                                            <i class="fa-solid fa-hotel"></i> {{ $statusPhrase }}
                                        </span>
                                    @elseif(str_starts_with($statusPhrase, 'Checked out'))
                                        <span class="status-chip bg-warning text-white">
                                            <i class="fa-solid fa-van-shuttle"></i> {{ $statusPhrase }}
                                        </span>
                                    @elseif(str_starts_with($statusPhrase, 'Ziyaraat completed'))
                                        <span class="status-chip bg-success text-white">
                                            <i class="fa-solid fa-mosque"></i> {{ $statusPhrase }}
                                        </span>
                                    @else
                                        <span class="status-chip bg-success text-white">
                                            <i class="fa-solid fa-check"></i> {{ $statusPhrase }}
                                        </span>
                                    @endif
                                </td>

                                <td class="ps-2 py-2">
                                    @if($nextLeg)
                                        <span class="text-xs font-weight-semibold text-dark d-block">
                                            {{ $nextLeg->title }} ({{ $nextLeg->city_or_route }})
                                        </span>
                                        <span class="text-xxs {{ $nextLeg->is_overdue ? 'text-danger font-weight-bold' : ($nextLeg->is_due_soon ? 'text-warning font-weight-bold' : 'text-secondary') }}">
                                            <i class="fa-regular fa-clock me-1"></i>{{ $nextLeg->event_datetime->format('d M, H:i') }}
                                            @if($nextLeg->is_overdue)
                                                <span class="badge bg-danger text-white py-0 px-1 text-xxs">Overdue</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-xs text-success font-weight-semibold">
                                            <i class="fa-solid fa-circle-check me-1"></i> All legs finished
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center pe-3 py-2">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary btn-xs mb-0 btn-view-details" data-booking-id="{{ $booking->id }}" title="Manage Checkpoints">
                                            <i class="fa-solid fa-list-check me-1"></i> Checkpoints
                                        </button>
                                        <a href="{{ route('pilgrimage.voucher.show', $booking->id) }}" target="_blank" class="btn btn-outline-success btn-xs mb-0" title="Hotel Voucher">
                                            <i class="fa-solid fa-file-invoice me-1"></i> Voucher
                                        </a>
                                        <a href="{{ route('pilgrimage.edit', $booking->id) }}" class="btn btn-outline-secondary btn-xs mb-0" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-xs text-secondary mb-2">No bookings found.</p>
                                    <a href="{{ route('pilgrimage.create') }}" class="btn btn-primary btn-xs">
                                        <i class="fa-solid fa-plus me-1"></i> Create Booking Intake
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                <span class="text-xxs text-secondary">Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }}</span>
                <div>{{ $bookings->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>

</div>

<!-- Checkpoints Drawer / Modal -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 border-radius-lg">
            <div class="modal-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-route text-primary fs-6"></i>
                    <h6 class="modal-title font-weight-bold text-dark mb-0 text-sm" id="modalBookingTitle">Journey Checkpoints</h6>
                </div>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Natural Scrollable Container for Touchpad/Mouse -->
            <div class="modal-body p-3 custom-modal-scroll" id="modalContentContainer">
                <div class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <p class="text-xs text-secondary mt-2">Loading journey checkpoints...</p>
                </div>
            </div>

            <div class="modal-footer bg-light border-top py-2 px-3 d-flex justify-content-between">
                <span class="text-xxs text-muted">Status updates instantly.</span>
                <button type="button" class="btn btn-secondary btn-xs mb-0" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
    const modalContent = document.getElementById('modalContentContainer');
    const modalTitle = document.getElementById('modalBookingTitle');

    document.querySelectorAll('.btn-view-details').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-booking-id');
            
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <p class="text-xs text-secondary mt-2">Loading checkpoints...</p>
                </div>`;
            detailModal.show();

            fetch(`{{ url('pilgrimage') }}/${bookingId}/modal-details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalTitle.innerHTML = `Checkpoints &bull; <span class="badge bg-dark">${data.crm_id}</span>`;
                        modalContent.innerHTML = data.html;
                        attachLegToggleListeners();
                    } else {
                        modalContent.innerHTML = `<div class="alert alert-danger text-white text-xs py-2">Failed to load details.</div>`;
                    }
                })
                .catch(() => {
                    modalContent.innerHTML = `<div class="alert alert-danger text-white text-xs py-2">Error connecting to server.</div>`;
                });
        });
    });

    function attachLegToggleListeners() {
        document.querySelectorAll('.btn-toggle-leg').forEach(btn => {
            btn.addEventListener('click', function () {
                const legId = this.getAttribute('data-leg-id');
                const originalHtml = this.innerHTML;
                this.disabled = true;
                this.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span>`;

                fetch(`{{ url('pilgrimage/legs') }}/${legId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        const legItem = document.getElementById(`leg-node-${legId}`);
                        if (legItem) {
                            const badge = legItem.querySelector('.leg-badge-status');
                            const timestampEl = legItem.querySelector('.leg-completed-time');
                            
                            if (res.new_status === 'done') {
                                badge.className = 'status-chip bg-success text-white leg-badge-status';
                                badge.innerHTML = '<i class="fa-solid fa-circle-check"></i> Completed';
                                timestampEl.innerHTML = `<span class="text-xxs text-success font-weight-semibold"><i class="fa-regular fa-clock me-1"></i> Completed: ${res.completed_at}</span>`;
                                btn.className = 'btn btn-xs btn-outline-secondary mb-0 btn-toggle-leg';
                                btn.innerHTML = '<i class="fa-solid fa-rotate-left me-1"></i> Undo';
                            } else {
                                badge.className = 'status-chip bg-light text-secondary border leg-badge-status';
                                badge.innerHTML = '<i class="fa-regular fa-clock"></i> Pending';
                                timestampEl.innerHTML = `<span class="text-xxs text-secondary">Awaiting completion</span>`;
                                btn.className = 'btn btn-xs btn-success mb-0 btn-toggle-leg';
                                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Mark Done';
                            }
                        }

                        // Update row status
                        const currentBookingId = document.querySelector('#current-modal-booking-id')?.value;
                        if (currentBookingId) {
                            const statusContainer = document.getElementById(`status-container-${currentBookingId}`);
                            if (statusContainer) {
                                let chipHtml = `<span class="status-chip bg-success text-white"><i class="fa-solid fa-check"></i> ${res.dashboard_status}</span>`;
                                if (res.dashboard_status === 'Not started yet') {
                                    chipHtml = `<span class="status-chip bg-light text-secondary border"><i class="fa-regular fa-circle-dot"></i> Not started yet</span>`;
                                } else if (res.dashboard_status.startsWith('Arrived:')) {
                                    chipHtml = `<span class="status-chip bg-info text-white"><i class="fa-solid fa-plane-arrival"></i> ${res.dashboard_status}</span>`;
                                } else if (res.dashboard_status.startsWith('Checked in')) {
                                    chipHtml = `<span class="status-chip bg-primary text-white"><i class="fa-solid fa-hotel"></i> ${res.dashboard_status}</span>`;
                                } else if (res.dashboard_status.startsWith('Checked out')) {
                                    chipHtml = `<span class="status-chip bg-warning text-white"><i class="fa-solid fa-van-shuttle"></i> ${res.dashboard_status}</span>`;
                                } else if (res.dashboard_status.startsWith('Ziyaraat completed')) {
                                    chipHtml = `<span class="status-chip bg-success text-white"><i class="fa-solid fa-mosque"></i> ${res.dashboard_status}</span>`;
                                }
                                statusContainer.innerHTML = chipHtml;
                            }
                        }
                    }
                    btn.disabled = false;
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
            });
        });
    }
});
</script>
@endsection
