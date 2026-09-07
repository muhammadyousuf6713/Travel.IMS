<input type="hidden" id="current-modal-booking-id" value="{{ $booking->id }}">

<style>
    /* Sleek, modern timeline component */
    .timeline-modern {
        position: relative;
        padding-left: 20px;
    }
    .timeline-modern::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 8px;
        bottom: 12px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 16px;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-dot {
        position: absolute;
        left: -20px;
        top: 2px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        color: #fff;
        box-shadow: 0 0 0 3px #fff;
    }
    .timeline-card {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        padding: 10px 14px;
        transition: border-color 0.15s ease;
    }
    .timeline-card:hover {
        border-color: #cbd5e1;
    }
</style>

<!-- Lead Traveler Summary Card -->
<div class="card bg-light border-0 border-radius-md p-3 mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-md-5">
            <span class="text-xxs text-uppercase text-secondary font-weight-bold d-block">Lead Passenger</span>
            <span class="text-sm font-weight-bold text-dark d-block">
                <i class="fa-solid fa-user text-primary me-1"></i> {{ $booking->leader_name }}
            </span>
            @if($booking->leader_contact_no)
                <span class="text-xs text-secondary d-block"><i class="fa-solid fa-phone me-1"></i>{{ $booking->leader_contact_no }}</span>
            @endif
            @if($booking->leader_passport_no)
                <span class="text-xxs text-muted d-block">Passport: {{ $booking->leader_passport_no }}</span>
            @endif
        </div>

        <div class="col-md-4">
            <span class="text-xxs text-uppercase text-secondary font-weight-bold d-block">Party & Submitting Agent</span>
            <span class="text-xs font-weight-semibold text-dark d-block">
                <i class="fa-solid fa-users text-primary me-1"></i> {{ $booking->total_persons }} Travelers
            </span>
            <span class="text-xs text-secondary d-block">
                Agent: <strong class="text-dark">{{ $booking->submittingAgent->name ?? 'Unassigned' }}</strong>
            </span>
        </div>

        <div class="col-md-3 text-md-end">
            <a href="{{ route('pilgrimage.voucher.show', $booking->id) }}" target="_blank" class="btn btn-xs btn-success w-100 mb-1">
                <i class="fa-solid fa-file-invoice me-1"></i> Hotel Voucher
            </a>
            <span class="badge bg-dark text-uppercase text-xxs w-100 py-1">
                {{ $booking->service_type }}
            </span>
        </div>
    </div>

    @if($booking->notes)
        <div class="mt-2 pt-2 border-top">
            <span class="text-xxs text-secondary font-weight-bold">Notes:</span>
            <span class="text-xs text-dark ms-1">{{ $booking->notes }}</span>
        </div>
    @endif
</div>

<!-- Co-Travelers if any -->
@if($booking->pilgrims->isNotEmpty())
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-xxs text-uppercase font-weight-bold text-secondary">
                Co-Travelers ({{ $booking->pilgrims->count() }})
            </span>
        </div>
        <div class="bg-white border border-radius-md p-2">
            <div class="d-flex flex-wrap gap-2">
                @foreach($booking->pilgrims as $idx => $pilgrim)
                    <span class="badge bg-light text-dark border font-weight-normal text-xxs px-2 py-1">
                        <strong>{{ $idx + 1 }}.</strong> {{ $pilgrim->name }}
                        @if($pilgrim->passport_no) <span class="text-muted">({{ $pilgrim->passport_no }})</span>@endif
                    </span>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- Checkpoints Timeline -->
<div class="mb-2">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-xxs text-uppercase font-weight-bold text-secondary">
            <i class="fa-solid fa-timeline text-primary me-1"></i> Journey Checkpoints
        </span>
        <span class="text-xxs text-muted">Click button to complete checkpoint</span>
    </div>

    <div class="timeline-modern">
        @forelse($booking->itineraryLegs as $leg)
            @php
                $isDone = $leg->status === 'done';
                $isOverdue = $leg->is_overdue;
                $isDueSoon = $leg->is_due_soon;

                $dotBg = $isDone ? '#10b981' : ($isOverdue ? '#ef4444' : ($isDueSoon ? '#f59e0b' : '#3b82f6'));
                $iconClass = 'fa-check';
                if ($leg->leg_type === 'flight') $iconClass = 'fa-plane';
                elseif ($leg->leg_type === 'hotel_checkin') $iconClass = 'fa-hotel';
                elseif ($leg->leg_type === 'hotel_checkout') $iconClass = 'fa-van-shuttle';
                elseif ($leg->leg_type === 'ziyaraat') $iconClass = 'fa-mosque';
            @endphp

            <div class="timeline-item" id="leg-node-{{ $leg->id }}">
                <div class="timeline-dot" style="background-color: {{ $dotBg }};">
                    <i class="fa-solid {{ $iconClass }}"></i>
                </div>

                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="text-xs font-weight-bold text-dark">{{ $leg->title }}</span>
                                <span class="badge bg-light text-secondary border text-xxs px-2 py-0">
                                    {{ $leg->city_or_route }}
                                </span>
                                @if($isDone)
                                    <span class="status-chip bg-success text-white leg-badge-status py-0 px-2" style="font-size: 10px;">
                                        <i class="fa-solid fa-circle-check"></i> Completed
                                    </span>
                                @elseif($isOverdue)
                                    <span class="status-chip bg-danger text-white leg-badge-status py-0 px-2" style="font-size: 10px;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Overdue
                                    </span>
                                @elseif($isDueSoon)
                                    <span class="status-chip bg-warning text-white leg-badge-status py-0 px-2" style="font-size: 10px;">
                                        <i class="fa-solid fa-clock"></i> Due Soon
                                    </span>
                                @else
                                    <span class="status-chip bg-light text-secondary border leg-badge-status py-0 px-2" style="font-size: 10px;">
                                        <i class="fa-regular fa-clock"></i> Pending
                                    </span>
                                @endif
                            </div>

                            <span class="text-xxs text-secondary d-block">
                                <i class="fa-regular fa-calendar me-1"></i> 
                                <strong class="text-dark">{{ $leg->event_datetime->format('d M Y, H:i (l)') }}</strong>
                            </span>

                            @if($leg->details)
                                <p class="text-xxs text-muted mb-1 mt-1 bg-light p-1 border-radius-sm">{{ $leg->details }}</p>
                            @endif

                            <div class="leg-completed-time mt-1">
                                @if($isDone)
                                    <span class="text-xxs text-success font-weight-semibold">
                                        <i class="fa-regular fa-circle-check me-1"></i> Completed: {{ $leg->completed_at ? $leg->completed_at->format('d M Y, h:i A') : 'Recorded' }}
                                    </span>
                                @else
                                    <span class="text-xxs text-secondary">Awaiting completion</span>
                                @endif
                            </div>
                        </div>

                        <div class="text-end">
                            @if($isDone)
                                <button type="button" class="btn btn-xs btn-outline-secondary mb-0 btn-toggle-leg px-2 py-1" data-leg-id="{{ $leg->id }}">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Undo
                                </button>
                            @else
                                <button type="button" class="btn btn-xs btn-success mb-0 btn-toggle-leg px-2 py-1" data-leg-id="{{ $leg->id }}">
                                    <i class="fa-solid fa-check me-1"></i> Mark Done
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-3">
                <span class="text-xs text-muted">No checkpoints configured.</span>
            </div>
        @endforelse
    </div>
</div>
