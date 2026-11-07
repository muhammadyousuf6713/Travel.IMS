<form id="addFollowupForm" class="mb-4">
    @csrf
    <input type="hidden" name="inquiry_id" value="{{ $id }}">

    <div class="mb-3">
        <label class="form-label fw-semibold">Follow-up Status</label>
        <select name="followup_status" class="form-select no-select2" id="followupStatus" onchange="toggleFollowupDate()">
            <option value="Open" {{ isset($inquiry) && $inquiry->status == 'Open' ? 'selected' : '' }}>
                Open</option>
             <option value="In-Progress" {{ isset($inquiry) && $inquiry->status == 'In-Progress' ? 'selected' : '' }}>
                In Progress</option>
            <option value="Confirmed" {{ isset($inquiry) && $inquiry->status == 'Confirmed' ? 'selected' : '' }}>
                Confirm</option>
            <option value="Completed" {{ isset($inquiry) && $inquiry->status == 'Completed' ? 'selected' : '' }}>
                Completed</option>
            <option value="Quotation" {{ isset($inquiry) && $inquiry->status == 'Quotation' ? 'selected' : '' }}>
                Quotation</option>
            <option value="Hold" {{ isset($inquiry) && $inquiry->status == 'Hold' ? 'selected' : '' }}>
                Hold</option>
            <option value="Cancelled" {{ isset($inquiry) && $inquiry->status == 'Cancelled' ? 'selected' : '' }}>
                Cancelled</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Remarks</label>
        <textarea name="remarks" class="form-control" rows="3" placeholder="Enter follow-up remarks..."></textarea>
    </div>

    <div class="form-group mb-3" id="followupDateContainer">
        <label class="form-label fw-semibold">Follow-up Date</label>
        <input type="date" name="followup_date" id="followupDate" required class="form-control">
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-success">
            <i class="fa fa-plus me-1"></i> Add Follow-up
        </button>
    </div>
</form>

<script>
    function toggleFollowupDate() {
        const statusSelect = document.getElementById('followupStatus');
        const dateInput = document.getElementById('followupDate');
        const dateContainer = document.getElementById('followupDateContainer');
        
        if (!statusSelect || !dateInput || !dateContainer) return;

        const val = String(statusSelect.value || '').trim().toLowerCase();
        if (val === 'cancelled' || val === 'completed') {
            dateInput.removeAttribute('required');
            dateInput.value = '';
            dateInput.disabled = true;
            dateContainer.style.setProperty('display', 'none', 'important');
        } else {
            dateInput.setAttribute('required', 'required');
            dateInput.disabled = false;
            dateContainer.style.removeProperty('display');
        }
    }

    // Run immediately and whenever status changes (both native change and jQuery/select2 change)
    toggleFollowupDate();
    if (document.getElementById('followupStatus')) {
        document.getElementById('followupStatus').addEventListener('change', toggleFollowupDate);
    }
    if (window.jQuery) {
        $(document).off('change.followupStatus', '#followupStatus').on('change.followupStatus', '#followupStatus', toggleFollowupDate);
    }
</script>

<div class="row">
    <div class="col-md-12">
        <h6 class="fw-bold border-bottom pb-1 mb-2">Follow-up Remarks:</h6>
        <div class="followup-modal-list followup-history-scrollbox" style="max-height: 320px; overflow-y: auto; overscroll-behavior: contain; padding-right: 6px;">
        @forelse ($followup_remarks as $fremark)
            <div class="mb-2 p-2 bg-white border rounded shadow-sm">
                <div class="fw-semibold mb-1">{{ $fremark->remarks }}</div>
                @php
                    $statusLabel = match ($fremark->followup_status) {
                        'Open' => '<span class="badge bg-warning text-dark">Open</span>',
                        'In-Progress' => '<span class="badge bg-primary">In Progress</span>',
                        'Cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                        'Confirmed' => '<span class="badge bg-success">Confirm</span>',
                        'Completed' => '<span class="badge bg-success">Completed</span>',
                        'Quotation' => '<span class="badge bg-info text-dark">Quotation</span>',
                        'Hold' => '<span class="badge bg-secondary">Hold</span>',
                        default => '<span class="badge bg-light text-dark">' . $fremark->followup_status . '</span>',
                    };
                @endphp
                <small>{!! $statusLabel !!} <span class="text-muted">•
                        {{ $fremark->created_at->diffForHumans() }}</span> <strong>Follow-up Date:</strong><span
                        class="badge bg-info text-dark">
                        {{ \Carbon\Carbon::parse($fremark->followup_date)->format('d-m-Y') }}
                    </span></small>

            </div>

        @empty

            <p class="text-muted">No follow-up remarks yet.</p>
        @endforelse
        </div>
    </div>
</div>
