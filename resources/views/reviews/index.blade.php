@extends('layouts.user_type.auth')

@section('content')
<style>
    /* ---------- Page ---------- */
    .rv-card { border: 0; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
    .rv-tabs { display: flex; gap: .25rem; border-bottom: 1px solid #eef0f3; padding: 0 1.25rem; overflow-x: auto; }
    .rv-tab { background: none; border: 0; border-bottom: 2px solid transparent; padding: .9rem .75rem .75rem;
        font-size: .82rem; font-weight: 600; color: #67748e; white-space: nowrap; display: flex; align-items: center; gap: .45rem; }
    .rv-tab:hover { color: #1e293b; }
    .rv-tab.active { color: #1e293b; border-bottom-color: #1e293b; }
    .rv-tab .cnt { font-size: .7rem; font-weight: 700; background: #f1f3f5; color: #67748e; border-radius: 999px; padding: 1px 8px; }
    .rv-tab.active .cnt { background: #1e293b; color: #fff; }
    .rv-tab .cnt.warn { background: #fff4e5; color: #b45309; }
    .rv-tab .cnt.danger { background: #fdecec; color: #b91c1c; }

    .rv-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; padding: 1rem 1.25rem; }
    .rv-search { position: relative; flex: 1 1 260px; max-width: 360px; }
    .rv-search i { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: #adb5bd; font-size: .8rem; }
    .rv-search input { padding-left: 2rem; }

    /* ---------- Table ---------- */
    #reviewsTable { margin: 0 !important; border-collapse: collapse !important; width: 100% !important; }
    #reviewsTable thead th { background: #f8f9fa; color: #8392ab; font-size: .68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .04em; border-bottom: 1px solid #eef0f3 !important; border-top: 0 !important; padding: .7rem .75rem; white-space: nowrap; }
    #reviewsTable tbody td { padding: .8rem .75rem; vertical-align: middle; border-bottom: 1px solid #f1f3f5; font-size: .82rem; color: #344767; }
    #reviewsTable tbody tr:hover td { background: #fafbfc; }
    #reviewsTable th:first-child, #reviewsTable td:first-child { padding-left: 1.25rem; width: 36px; }
    #reviewsTable th:last-child, #reviewsTable td:last-child { padding-right: 1.25rem; }
    #reviewsTable.dataTable thead .sorting:before, #reviewsTable.dataTable thead .sorting:after { opacity: .25; }

    .rv-person { display: flex; align-items: center; gap: .65rem; min-width: 0; }
    .rv-avatar { width: 36px; height: 36px; border-radius: 50%; color: #fff; font-size: .75rem; font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .rv-avatar.sm { width: 26px; height: 26px; font-size: .62rem; }
    .rv-name { font-weight: 700; color: #1e293b; font-size: .85rem; max-width: 240px; }
    .rv-sub { font-size: .72rem; color: #8392ab; display: flex; align-items: center; flex-wrap: wrap; gap: .35rem; }
    .rv-dot { width: 3px; height: 3px; border-radius: 50%; background: #cbd3da; display: inline-block; }
    .rv-link { color: #4f6bed; font-weight: 600; }
    .rv-link:hover { text-decoration: underline; }
    .rv-date { font-weight: 600; color: #344767; }
    .rv-muted { color: #adb5bd; }
    .rv-due { font-size: .72rem; color: #8392ab; }
    .rv-due.today { color: #b45309; font-weight: 700; }
    .rv-due.overdue { color: #b91c1c; font-weight: 700; }
    .rv-assignee { font-weight: 600; max-width: 150px; }
    .rv-stars { color: #f5b301; font-size: .75rem; letter-spacing: 1px; }

    .rv-badge { display: inline-block; font-size: .7rem; font-weight: 700; padding: .25rem .6rem; border-radius: 999px; white-space: nowrap; }
    .rv-pending { background: #f1f3f5; color: #495057; }
    .rv-requested { background: #e8f0fe; color: #1d4ed8; }
    .rv-not_reachable { background: #fff4e5; color: #b45309; }
    .rv-received { background: #e6f7ee; color: #15803d; }
    .rv-declined { background: #fdecec; color: #b91c1c; }
    .rv-unassigned { background: #fff; color: #b91c1c; border: 1px dashed #f1b5b5; }

    .rv-actions { display: flex; gap: .4rem; justify-content: flex-end; align-items: center; }
    .rv-btn { height: 32px; min-width: 32px; padding: 0 .6rem; border-radius: .5rem; border: 1px solid #e3e7ec; background: #fff;
        color: #495057; display: inline-flex; align-items: center; justify-content: center; gap: .4rem; font-size: .78rem;
        font-weight: 600; cursor: pointer; transition: all .15s ease; text-decoration: none; }
    .rv-btn:hover { border-color: #c9d1da; background: #f8f9fa; color: #1e293b; }
    .rv-btn i { font-size: .85rem; }
    .rv-btn-wa { color: #128c4b; border-color: #cfeede; background: #f2fbf6; }
    .rv-btn-wa:hover { background: #25d366; border-color: #25d366; color: #fff; }
    .rv-btn-wa i { font-size: 1rem; }
    .rv-btn-primary { background: #1e293b; border-color: #1e293b; color: #fff; }
    .rv-btn-primary:hover { background: #0f172a; border-color: #0f172a; color: #fff; }
    .rv-btn-disabled { opacity: .35; cursor: not-allowed; }

    /* ---------- Footer / pagination ---------- */
    .rv-footer { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .5rem; padding: .85rem 1.25rem; }
    .rv-footer .dataTables_info { font-size: .78rem; color: #8392ab; padding: 0 !important; }
    .rv-footer .pagination { margin: 0; gap: .25rem; }
    .rv-footer .page-link { width: auto !important; min-width: 32px; height: 32px; padding: 0 .65rem !important; border-radius: .5rem !important;
        border: 1px solid #e3e7ec !important; color: #344767 !important; font-size: .78rem; display: flex; align-items: center;
        justify-content: center; margin: 0 !important; background: #fff; box-shadow: none !important; }
    .rv-footer .page-item.active .page-link { background: #1e293b !important; border-color: #1e293b !important; color: #fff !important; }
    .rv-footer .page-item.disabled .page-link { color: #c3cad2 !important; }
    .dataTables_processing { border: 0 !important; box-shadow: 0 4px 12px rgba(0,0,0,.08) !important; border-radius: .5rem !important; }

    .rv-empty { padding: 2.5rem 1rem; text-align: center; color: #8392ab; }
    .rv-empty i { font-size: 1.6rem; color: #cbd3da; display: block; margin-bottom: .5rem; }

    /* ---------- Modals ---------- */
    .rv-modal .modal-content { border: 0; border-radius: 1rem; }
    .rv-modal .modal-header { border-bottom: 1px solid #eef0f3; padding: 1rem 1.25rem; }
    .rv-modal .modal-title { font-size: .95rem; font-weight: 700; color: #1e293b; }
    .rv-modal .modal-subtitle { font-size: .75rem; color: #8392ab; }
    .rv-modal .modal-body { padding: 1.25rem; }
    .rv-modal .modal-footer { border-top: 1px solid #eef0f3; padding: .75rem 1.25rem; }
    .rv-label { font-size: .75rem; font-weight: 700; color: #344767; margin-bottom: .35rem; display: block; }

    .outcome-group { display: grid; grid-template-columns: repeat(3, 1fr); gap: .5rem; }
    .outcome-group input { display: none; }
    .outcome-group label { border: 1px solid #e3e7ec; border-radius: .5rem; padding: .55rem .6rem; font-size: .78rem; font-weight: 600;
        color: #495057; cursor: pointer; display: flex; align-items: center; gap: .45rem; margin: 0; }
    .outcome-group label i { color: #8392ab; width: 14px; text-align: center; }
    .outcome-group input:checked + label { border-color: #1e293b; background: #f8f9fb; color: #1e293b; box-shadow: 0 0 0 1px #1e293b inset; }
    .outcome-group input:checked + label i { color: #1e293b; }

    .chip-group { display: flex; flex-wrap: wrap; gap: .4rem; }
    .chip-group input { display: none; }
    .chip-group label { border: 1px solid #e3e7ec; border-radius: 999px; padding: .3rem .8rem; font-size: .75rem; font-weight: 600; color: #495057; cursor: pointer; margin: 0; }
    .chip-group input:checked + label { background: #1e293b; border-color: #1e293b; color: #fff; }

    .rating-stars { display: inline-flex; gap: .15rem; }
    .rating-stars input { display: none; }
    .rating-stars label { font-size: 1.35rem; color: #dee2e6; cursor: pointer; margin: 0; }
    .rating-stars label.on { color: #f5b301; }

    .timeline { position: relative; padding-left: 1.25rem; }
    .timeline::before { content: ""; position: absolute; left: 5px; top: 4px; bottom: 4px; width: 2px; background: #eef0f3; }
    .tl-item { position: relative; padding-bottom: 1rem; }
    .tl-item::before { content: ""; position: absolute; left: -1.25rem; top: 4px; width: 12px; height: 12px; border-radius: 50%;
        background: #fff; border: 2px solid #adb5bd; }
    .tl-item.received::before { border-color: #15803d; background: #15803d; }
    .tl-item.declined::before { border-color: #b91c1c; }
    .tl-head { display: flex; justify-content: space-between; gap: .5rem; font-size: .8rem; }
    .tl-head strong { color: #1e293b; }
    .tl-meta { font-size: .72rem; color: #8392ab; white-space: nowrap; }
    .tl-body { font-size: .8rem; color: #495057; margin-top: .2rem; }
    .info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem; background: #f8f9fa; border-radius: .75rem; padding: .85rem 1rem; margin-bottom: 1.25rem; }
    .info-grid .k { font-size: .68rem; text-transform: uppercase; letter-spacing: .04em; color: #8392ab; font-weight: 700; }
    .info-grid .v { font-size: .82rem; font-weight: 700; color: #1e293b; }
    @media (max-width: 576px) { .info-grid, .outcome-group { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="container-fluid py-3">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
        <div>
            <h5 class="mb-0 font-weight-bolder text-dark">Reviews</h5>
            <p class="text-sm text-secondary mb-0">
                Ask customers of completed inquiries for a review and track the follow-ups.
                @unless($canSeeAll) Showing reviews assigned to you. @endunless
            </p>
        </div>
    </div>

    <div class="card rv-card">

        {{-- Tabs --}}
        @php
            $tabs = [
                'open' => 'To follow up',
                'due_today' => 'Due today',
                'overdue' => 'Overdue',
                'received' => 'Received',
                'declined' => 'Declined',
                'all' => 'All',
            ];
        @endphp
        <div class="rv-tabs" id="reviewTabs">
            @foreach($tabs as $key => $label)
                @php
                    $n = $counts[$key] ?? 0;
                    $cls = ($key === 'due_today' && $n > 0) ? 'warn' : (($key === 'overdue' && $n > 0) ? 'danger' : '');
                @endphp
                <button type="button" class="rv-tab {{ $key === 'open' ? 'active' : '' }}" data-tab="{{ $key }}">
                    {{ $label }} <span class="cnt {{ $cls }}" id="count_{{ $key }}">{{ $n }}</span>
                </button>
            @endforeach
        </div>

        {{-- Toolbar --}}
        <div class="rv-toolbar">
            <div class="rv-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="rvSearch" class="form-control form-control-sm" placeholder="Search customer, phone or inquiry #">
            </div>
            <div class="ms-auto d-flex gap-2 align-items-center">
                @if($canSeeAll)
                    <button type="button" class="btn btn-sm btn-outline-dark mb-0 d-none text-nowrap" id="bulkReassignBtn" style="white-space:nowrap;">
                        <i class="fa-solid fa-user-pen me-1"></i> Reassign <span id="selCount"></span>
                    </button>
                    <select id="assigneeFilter" class="form-select form-select-sm no-select2" style="min-width: 200px;">
                        <option value="">All review officers</option>
                        <option value="none">Unassigned</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table id="reviewsTable" class="table align-items-center mb-0" width="100%">
                <thead>
                    <tr>
                        <th>@if($canSeeAll)<input type="checkbox" id="checkAllReviews" class="form-check-input">@endif</th>
                        <th>Customer</th>
                        <th>Completed</th>
                        <th>Next follow-up</th>
                        <th>Assigned to</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- ================= Log follow-up ================= --}}
<div class="modal fade rv-modal" id="followupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="modal-title">Log follow-up</div>
                    <div class="modal-subtitle" id="fuCustomer"></div>
                </div>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
            </div>
            <form id="followupForm">
                @csrf
                <input type="hidden" id="fuReviewId">
                <div class="modal-body">
                    <span class="rv-label">What happened?</span>
                    @php
                        $outcomeIcons = [
                            'link_sent' => 'fa-regular fa-paper-plane',
                            'will_do_later' => 'fa-regular fa-clock',
                            'not_reachable' => 'fa-solid fa-phone-slash',
                            'received' => 'fa-regular fa-circle-check',
                            'declined' => 'fa-regular fa-circle-xmark',
                            'note' => 'fa-regular fa-note-sticky',
                        ];
                    @endphp
                    <div class="outcome-group mb-3">
                        @foreach($outcomes as $key => $label)
                            <input type="radio" name="outcome" value="{{ $key }}" id="oc_{{ $key }}" {{ $loop->first ? 'checked' : '' }}>
                            <label for="oc_{{ $key }}"><i class="{{ $outcomeIcons[$key] ?? 'fa-regular fa-circle' }}"></i>{{ $label }}</label>
                        @endforeach
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="rv-label">Contacted via</label>
                            <select name="channel" class="form-select form-select-sm no-select2">
                                @foreach($channels as $ch)
                                    <option value="{{ $ch }}">{{ $ch }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4" id="nextDateWrap">
                            <label class="rv-label">Next follow-up <span class="text-danger" id="nextReqStar">*</span></label>
                            <input type="date" name="next_followup_date" id="fuNextDate" class="form-control form-control-sm">
                        </div>

                        <div class="col-12 received-only d-none">
                            <label class="rv-label">Where did the customer post it?</label>
                            <div class="chip-group">
                                @foreach($platforms as $p)
                                    <input type="checkbox" name="platforms[]" value="{{ $p }}" id="plat_{{ $loop->index }}">
                                    <label for="plat_{{ $loop->index }}">{{ $p }}</label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-4 received-only d-none">
                            <label class="rv-label">Rating</label>
                            <div class="rating-stars" id="ratingStars">
                                @for($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}">
                                    <label for="star{{ $i }}" data-v="{{ $i }}"><i class="fa-solid fa-star"></i></label>
                                @endfor
                            </div>
                        </div>
                        <div class="col-md-8 received-only d-none">
                            <label class="rv-label">Review link <span class="text-secondary fw-normal">(optional)</span></label>
                            <input type="text" name="review_link" class="form-control form-control-sm" placeholder="https://">
                        </div>
                        <div class="col-12 received-only d-none">
                            <label class="rv-label">Customer's comment <span class="text-secondary fw-normal">(optional)</span></label>
                            <textarea name="customer_comment" rows="2" class="form-control form-control-sm"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="rv-label">Remarks</label>
                            <textarea name="remarks" rows="2" class="form-control form-control-sm" placeholder="Short note about this contact"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light mb-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dark mb-0 px-4" id="fuSubmit">Save follow-up</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= History ================= --}}
<div class="modal fade rv-modal" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="modal-title">Review history</div>
                    <div class="modal-subtitle" id="hiCustomer"></div>
                </div>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="historyBody"></div>
        </div>
    </div>
</div>

@if($canSeeAll)
{{-- ================= Reassign ================= --}}
<div class="modal fade rv-modal" id="reassignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="modal-title">Reassign reviews</div>
                    <div class="modal-subtitle"><span id="reassignCount">0</span> selected</div>
                </div>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
            </div>
            <form id="reassignForm">
                @csrf
                <div id="reassignIds"></div>
                <div class="modal-body">
                    <label class="rv-label">Assign to</label>
                    <select name="assigned_to" class="form-select form-select-sm no-select2" required>
                        <option value="">Select a person</option>
                        <option value="none">Unassigned</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light mb-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dark mb-0 px-4">Reassign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(function () {
    let currentTab = 'open';
    const esc = s => $('<div>').text(s == null ? '' : s).html();

    function initTooltips() {
        if (!window.bootstrap || !bootstrap.Tooltip) return;
        document.querySelectorAll('#reviewsTable [data-bs-toggle="tooltip"]').forEach(el => {
            const t = bootstrap.Tooltip.getInstance(el);
            if (t) t.dispose();
            new bootstrap.Tooltip(el, { container: 'body' });
        });
    }

    const table = $('#reviewsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        pageLength: 25,
        lengthChange: false,
        order: [[3, 'asc']],
        dom: 't<"rv-footer"ip>',
        ajax: {
            url: '{{ route('reviews.data') }}',
            data: function (d) {
                d.tab = currentTab;
                d.assigned_to = $('#assigneeFilter').val() || '';
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'customer', name: 'customer' },
            { data: 'completed', name: 'completed', searchable: false },
            { data: 'followup', name: 'followup', searchable: false },
            { data: 'assignee', name: 'assignee', orderable: false },
            { data: 'status_badge', orderable: false, searchable: false },
            { data: 'result', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            emptyTable: '<div class="rv-empty"><i class="fa-regular fa-star"></i>No reviews in this list</div>',
            zeroRecords: '<div class="rv-empty"><i class="fa-solid fa-magnifying-glass"></i>No matching reviews</div>',
            info: 'Showing _START_–_END_ of _TOTAL_',
            infoEmpty: '',
            infoFiltered: '',
            processing: 'Loading…',
            paginate: { previous: '<i class="fa-solid fa-chevron-left"></i>', next: '<i class="fa-solid fa-chevron-right"></i>' }
        },
        drawCallback: function () {
            $('#checkAllReviews').prop('checked', false);
            toggleReassign();
            initTooltips();
        }
    });

    let searchTimer;
    $('#rvSearch').on('input', function () {
        clearTimeout(searchTimer);
        const v = this.value;
        searchTimer = setTimeout(() => table.search(v).draw(), 350);
    });

    function refreshCounts() {
        $.get('{{ route('reviews.counts') }}', function (c) {
            $.each(c, function (k, v) {
                const $el = $('#count_' + k).text(v);
                if (k === 'due_today') $el.toggleClass('warn', v > 0);
                if (k === 'overdue') $el.toggleClass('danger', v > 0);
            });
        });
    }

    $('#reviewTabs').on('click', '.rv-tab', function () {
        $('.rv-tab').removeClass('active');
        $(this).addClass('active');
        currentTab = $(this).data('tab');
        table.ajax.reload();
    });

    $('#assigneeFilter').on('change', () => table.ajax.reload());

    /* ---------- Log follow-up ---------- */
    function syncOutcomeFields() {
        const o = $('input[name="outcome"]:checked').val();
        const closing = (o === 'received' || o === 'declined');
        $('.received-only').toggleClass('d-none', o !== 'received');
        $('#nextDateWrap').toggleClass('d-none', closing);
        $('#fuNextDate').prop('disabled', closing);
        $('#nextReqStar').toggleClass('d-none', o === 'note');
    }
    $(document).on('change', 'input[name="outcome"]', syncOutcomeFields);

    $('#ratingStars').on('click', 'label', function () {
        const v = $(this).data('v');
        $('#ratingStars label').each(function () { $(this).toggleClass('on', $(this).data('v') <= v); });
    });

    $(document).on('click', '.log-followup', function () {
        $('#followupForm')[0].reset();
        $('#ratingStars label').removeClass('on');
        $('#fuReviewId').val($(this).data('id'));
        $('#fuCustomer').text($(this).data('customer') || '');
        const d = new Date(); d.setDate(d.getDate() + 2);
        $('#fuNextDate').val(d.toISOString().slice(0, 10));
        syncOutcomeFields();
        $('#followupModal').modal('show');
    });

    $('#followupForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#fuReviewId').val();
        const $btn = $('#fuSubmit').prop('disabled', true);
        $.ajax({
            url: '{{ url('reviews') }}/' + id + '/followup',
            method: 'POST',
            data: $(this).serialize(),
            success: function (r) {
                $('#followupModal').modal('hide');
                toastr.success(r.message || 'Saved');
                table.ajax.reload(null, false);
                refreshCounts();
            },
            error: function (xhr) {
                let msg = 'Could not save the follow-up.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    else if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            },
            complete: () => $btn.prop('disabled', false)
        });
    });

    /* ---------- History ---------- */
    $(document).on('click', '.view-history', function () {
        const id = $(this).data('id');
        $('#hiCustomer').text($(this).data('customer') || '');
        $('#historyBody').html('<div class="rv-empty"><i class="fa-solid fa-spinner fa-spin"></i></div>');
        $('#historyModal').modal('show');

        $.get('{{ url('reviews') }}/' + id + '/history', function (r) {
            const rv = r.review;
            const stars = n => n ? '<span class="rv-stars">' + Array.from({ length: 5 }, (_, i) => '<i class="fa-' + (i < n ? 'solid' : 'regular') + ' fa-star"></i>').join('') + '</span>' : '';

            let html = '<div class="info-grid">'
                + '<div><div class="k">Status</div><div class="v">' + esc(rv.status) + '</div></div>'
                + '<div><div class="k">Assigned to</div><div class="v">' + esc(rv.assignee || 'Unassigned') + '</div></div>'
                + '<div><div class="k">Completed</div><div class="v">' + esc(rv.completed_at || '—') + '</div></div>'
                + '<div><div class="k">Review due</div><div class="v">' + esc(rv.due_date || '—') + '</div></div>'
                + '</div>';

            if (rv.rating || (rv.platforms && rv.platforms.length) || rv.customer_comment) {
                html += '<div class="border rounded-3 p-3 mb-3 text-sm">'
                    + stars(rv.rating)
                    + (rv.platforms && rv.platforms.length ? '<span class="ms-2 text-secondary">' + esc(rv.platforms.join(', ')) + '</span>' : '')
                    + (rv.review_link ? ' · <a href="' + esc(rv.review_link) + '" target="_blank" class="rv-link">Open review</a>' : '')
                    + (rv.customer_comment ? '<div class="mt-2 fst-italic text-secondary">“' + esc(rv.customer_comment) + '”</div>' : '')
                    + '</div>';
            }

            if (!r.followups.length) {
                html += '<div class="rv-empty"><i class="fa-regular fa-comments"></i>No follow-ups logged yet</div>';
            } else {
                html += '<div class="timeline">';
                r.followups.forEach(function (f) {
                    const cls = f.outcome === 'Review received' ? 'received' : (f.outcome === 'Declined' ? 'declined' : '');
                    html += '<div class="tl-item ' + cls + '">'
                        + '<div class="tl-head"><strong>' + esc(f.outcome) + (f.channel ? ' <span class="text-secondary fw-normal">via ' + esc(f.channel) + '</span>' : '') + '</strong>'
                        + '<span class="tl-meta">' + esc(f.date) + ' · ' + esc(f.user) + '</span></div>'
                        + (f.remarks ? '<div class="tl-body">' + esc(f.remarks) + '</div>' : '')
                        + (f.rating ? '<div class="tl-body">' + stars(f.rating) + '</div>' : '')
                        + (f.platforms.length ? '<div class="tl-meta mt-1">Posted on ' + esc(f.platforms.join(', ')) + '</div>' : '')
                        + (f.next ? '<div class="tl-meta mt-1">Next follow-up ' + esc(f.next) + '</div>' : '')
                        + '</div>';
                });
                html += '</div>';
            }
            $('#historyBody').html(html);
        }).fail(function (xhr) {
            $('#historyBody').html('<div class="text-danger text-sm">' + esc((xhr.responseJSON && xhr.responseJSON.message) || 'Could not load history.') + '</div>');
        });
    });

    /* ---------- Reassign (admins) ---------- */
    function toggleReassign() {
        const n = $('.review-checkbox:checked').length;
        $('#bulkReassignBtn').toggleClass('d-none', n === 0);
        $('#selCount').text(n ? '(' + n + ')' : '');
    }
    $(document).on('change', '#checkAllReviews', function () {
        $('.review-checkbox').prop('checked', $(this).prop('checked'));
        toggleReassign();
    });
    $(document).on('change', '.review-checkbox', toggleReassign);

    $('#bulkReassignBtn').on('click', function () {
        const ids = $('.review-checkbox:checked').map(function () { return $(this).val(); }).get();
        if (!ids.length) return;
        $('#reassignCount').text(ids.length);
        $('#reassignIds').html(ids.map(id => '<input type="hidden" name="review_ids[]" value="' + id + '">').join(''));
        $('#reassignModal').modal('show');
    });

    $('#reassignForm').on('submit', function (e) {
        e.preventDefault();
        $.post('{{ route('reviews.reassign') }}', $(this).serialize())
            .done(function (r) {
                $('#reassignModal').modal('hide');
                toastr.success(r.message);
                table.ajax.reload(null, false);
                refreshCounts();
            })
            .fail(xhr => toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Could not reassign.'));
    });
});
</script>
@endpush
