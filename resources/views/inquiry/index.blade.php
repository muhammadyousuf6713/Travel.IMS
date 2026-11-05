@extends('layouts.user_type.auth')

@section('content')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <style>
      
    </style>

    <div class="container-fluid">

        {{-- 🔍 Filter Panel --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header p-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Filter Inquiries</h6>
                <button class="btn btn-sm btn-outline-primary" type="button" id="toggleFilterBtn">
                    <i class="fa fa-chevron-up me-1" id="filterIcon"></i> Toggle Filters
                </button>
            </div>
            <div id="filterCollapse" style="display: none;">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row g-3">
                            {{-- Filters --}}
                            <div class="col-6 col-md-3">
                                <label class="form-label">Inquiry Type</label>
                                <select class="form-select" name="inquiry_type">
                                    <option value="">All</option>
                                    @foreach ($data['inquiry_type'] as $type)
                                        <option value="{{ $type->type_id }}">{{ $type->type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">Sales Person</label>
                                <select class="form-select" name="sales_person">
                                    <option value="">All</option>
                                    @foreach ($sales_person as $sp)
                                        <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All</option>
                                    <option value="Open">Open</option>
                                    <option value="In-Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                    <option value="Confirmed">Confirm</option>
                                    <option value="Quotation">Quotation</option>
                                    <option value="Hold">Hold</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">ID #</label>
                                <input type="text" class="form-control" name="id_inquiry" placeholder="e.g. 01, 02, 30">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">Follow Up Date</label>
                                <select class="form-select" name="fud_filter">
                                    <option value="">All</option>
                                    <option value="today">Today</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" placeholder="Name">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="customer_cell" placeholder="Phone">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" name="customer_email" placeholder="Email">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">Sales Reference</label>
                                <select class="form-select" name="sales_reference">
                                    <option value="">All</option>
                                    @foreach ($data['sales_reference'] ?? [] as $ref)
                                        <option value="{{ $ref->type_id }}">{{ $ref->type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control" name="date_from">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control" name="date_to">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Filter
                            </button>
                            <button type="button" id="resetFilters" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 📋 Inquiries Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between align-items-center mb-3">
                            <div>
                                <!-- <h5 class="mb-0">All Inquiries</h5> -->
                                <small class="text-muted">
                                    <i class="fa fa-info-circle me-1"></i>
                                    Click on the <strong>ID #</strong> to view details.
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                @can('Inquiry edit')
                                <button type="button" class="btn btn-sm btn-info d-none" id="bulkAssignBtn">
                                    <i class="fa fa-users-cog"></i> Bulk Assign
                                </button>
                                @endcan
                                @can('Reviews sync')
                                <button type="button" class="btn btn-sm btn-success d-none" id="syncReviewsBtn">
                                    <i class="fa fa-star"></i> Sync to Reviews
                                </button>
                                @endcan
                                @can('Inquiry add')
                                <a href="{{ url('inquiry/create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> New Inquiry
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-2 pt-0 pb-2">
                        <div id="inquiryTableAlert" class="alert alert-danger d-none d-flex align-items-center justify-content-between mx-2 mt-2" role="alert">
                            <span><i class="fa fa-triangle-exclamation me-2"></i><span id="inquiryTableAlertMsg">Something went wrong while loading inquiries.</span></span>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-3" id="inquiryTableRetryBtn">
                                <i class="fa fa-rotate-right me-1"></i> Retry
                            </button>
                        </div>
                        <div class="table-responsive p-0">
                            <table id="example23" class="table table-sm table-bordered nowrap align-middle"
                                width="100%">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th style="width: 2%;"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                        <th style="width: 80px; min-width: 80px;">ID #</th>
                                        <th>Customer</th>
                                        <th>Number</th>
                                        <th>Inquiry Type</th>
                                        <th>SP</th>
                                        <th>SR</th>
                                        <th>FUD</th>
                                        <th>TD</th>
                                        <th>Status</th>
                                        <th class="none">Services</th>
                                        <th class="none">City</th>
                                        <th class="none">Created At</th>
                                        <th class="none">Remarks</th>
                                        <th class="none">Created By</th>
                                        <th class="none">Updated At</th>
                                        <th class="all" style="width: 130px; min-width: 130px; text-align: center;">Action</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        <th class="none"></th>
                                        <th class="none"></th>
                                        <th class="none"></th>
                                        <th class="none"></th>
                                        <th class="none"></th>
                                        <th class="none"></th>
                                        <th class="all"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- 📝 Modals (Follow-up & Progress) --}}
    <div class="modal fade" id="followUpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inquiry Follow-up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="followup-content">Loading...</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="progressRemarkModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-3 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-semibold">
                        <i class="fa fa-pencil-alt me-1 text-primary"></i> Add Progress Remark
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProgressForm">
                        @csrf
                        <input type="hidden" name="inquiry_id" id="progress_inquiry_id">

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="progress_type" required>
                                    <option value="">Select Type</option>
                                    @foreach ($followup_types as $type)
                                        <option value="{{ $type->id_follow_up_types }}">{{ $type->type_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Progress Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="progress_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="progress_status" required>
                                    <option value="">Select Status</option>
                                    <option value="Open">Open</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">User</label>
                                <select class="form-select" name="progress_user">
                                    <option value="">Select User</option>
                                    @foreach ($sales_person as $user)
                                        <option @if ($user->id == auth()->user()->id) selected @endif
                                            value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Remarks <span class="text-danger">*</span></label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter your remarks..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fa fa-plus me-1"></i> Add Remark
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-list text-primary me-1"></i> Progress Remarks
                    </h6>
                    <div id="progressRemarksList" class="small" style="max-height: 250px; overflow-y: auto;">
                        Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="changeSalesPersonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-semibold">
                        <i class="fa fa-user-edit me-1 text-warning"></i> Change Sales Person
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="changeSalesPersonForm">
                        @csrf
                        <input type="hidden" name="inquiry_id" id="salesperson_inquiry_id">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Sales Person <span class="text-danger">*</span></label>
                            <select class="form-select" name="sales_person_id" id="sales_person_select" required>
                                <option value="">Select Sales Person</option>
                                @foreach ($sales_person as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="fa fa-save me-1"></i> Update
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bulkAssignmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-semibold">
                        <i class="fa fa-users-cog me-1 text-info"></i> Bulk Assign Sales Person
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkAssignmentForm">
                        @csrf
                        <div id="bulkInquiryIds"></div>
                        
                        <div class="alert alert-info text-white">
                            <i class="fa fa-info-circle me-1"></i> You are about to assign <strong id="selectedCount">0</strong> inquiries.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Sales Person <span class="text-danger">*</span></label>
                            <select class="form-select" name="sales_person_id" required>
                                <option value="">Select Sales Person</option>
                                @foreach ($sales_person as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-info px-4">
                                <i class="fa fa-save me-1"></i> Assign
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    @can('Reviews sync')
    <div class="modal fade" id="syncReviewsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-semibold">
                        <i class="fa fa-star me-1 text-warning"></i> Sync to Reviews
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="syncReviewsForm">
                        @csrf
                        <div id="syncReviewIds"></div>

                        <div id="syncPreviewBox" class="mb-3"></div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold">Assign reviews to <span class="text-danger">*</span></label>
                            <select class="form-select no-select2" name="assigned_to" id="syncAssignTo" required>
                                <option value="auto">Auto: rotate among review officers (Integration Settings)</option>
                                @foreach ($sales_person as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" id="syncAutoHint"></small>
                        </div>
                        <small class="text-muted">Only <strong>Completed</strong> inquiries are synced. Review due date = completion date + days set in Integration Settings.</small>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-success px-4" id="syncReviewsSubmit" disabled>
                                <i class="fa fa-star me-1"></i> Sync
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan

    {{-- 🎨 Styles: Loaded from public/assets/css/custom.css --}}
@endsection
@push('scripts')

    <script>
        $(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        const INQ_SESSION_KEY = 'ims_filters_inquiry_state';

        function saveInquiryFilters() {
            try {
                var state = {
                    inquiry_type: $('select[name="inquiry_type"]').val() || '',
                    sales_person: $('select[name="sales_person"]').val() || '',
                    status: $('select[name="status"]').val() || '',
                    id_inquiry: $('input[name="id_inquiry"]').val() || '',
                    fud_filter: $('select[name="fud_filter"]').val() || '',
                    customer_name: $('input[name="customer_name"]').val() || '',
                    customer_cell: $('input[name="customer_cell"]').val() || '',
                    customer_email: $('input[name="customer_email"]').val() || '',
                    sales_reference: $('select[name="sales_reference"]').val() || '',
                    date_from: $('input[name="date_from"]').val() || '',
                    date_to: $('input[name="date_to"]').val() || '',
                    table_search: $('#example23_filter input').length ? $('#example23_filter input').val() : ''
                };
                sessionStorage.setItem(INQ_SESSION_KEY, JSON.stringify(state));
            } catch (e) {}
        }

        function restoreInquiryFilters() {
            var urlParams = new URLSearchParams(window.location.search);
            var hasUrlParams = urlParams.toString().length > 0;
            var state = null;

            if (!hasUrlParams) {
                try {
                    var raw = sessionStorage.getItem(INQ_SESSION_KEY);
                    if (raw) state = JSON.parse(raw);
                } catch (e) {}
            }

            var hasActiveFilters = false;

            if (state) {
                if (state.inquiry_type) { $('select[name="inquiry_type"]').val(state.inquiry_type).trigger('change.select2'); hasActiveFilters = true; }
                if (state.sales_person) { $('select[name="sales_person"]').val(state.sales_person).trigger('change.select2'); hasActiveFilters = true; }
                if (state.status) { $('select[name="status"]').val(state.status).trigger('change.select2'); hasActiveFilters = true; }
                if (state.id_inquiry) { $('input[name="id_inquiry"]').val(state.id_inquiry); hasActiveFilters = true; }
                if (state.fud_filter) { $('select[name="fud_filter"]').val(state.fud_filter).trigger('change.select2'); hasActiveFilters = true; }
                if (state.customer_name) { $('input[name="customer_name"]').val(state.customer_name); hasActiveFilters = true; }
                if (state.customer_cell) { $('input[name="customer_cell"]').val(state.customer_cell); hasActiveFilters = true; }
                if (state.customer_email) { $('input[name="customer_email"]').val(state.customer_email); hasActiveFilters = true; }
                if (state.sales_reference) { $('select[name="sales_reference"]').val(state.sales_reference).trigger('change.select2'); hasActiveFilters = true; }
                if (state.date_from) { $('input[name="date_from"]').val(state.date_from); hasActiveFilters = true; }
                if (state.date_to) { $('input[name="date_to"]').val(state.date_to); hasActiveFilters = true; }
            } else if (hasUrlParams) {
                const filters = {
                    'sales_person': 'select[name="sales_person"]',
                    'salesperson_id': 'select[name="sales_person"]',
                    'inquiry_type': 'select[name="inquiry_type"]',
                    'status': 'select[name="status"]',
                    'fud_filter': 'select[name="fud_filter"]',
                    'sales_reference': 'select[name="sales_reference"]',
                    'id_inquiry': 'input[name="id_inquiry"]',
                    'date_from': 'input[name="date_from"]',
                    'date_to': 'input[name="date_to"]'
                };
                for (const [param, selector] of Object.entries(filters)) {
                    if (urlParams.has(param)) {
                        const val = urlParams.get(param);
                        const $el = $(selector);
                        if ($el.length && val) {
                            $el.val(val).trigger('change.select2');
                            hasActiveFilters = true;
                        }
                    }
                }
                saveInquiryFilters();
            }

            if (hasActiveFilters) {
                $('#filterCollapse').show();
                $('#filterIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }

            return state;
        }

        $(document).ready(function() {
            restoreInquiryFilters();

            // Click progress remarks button
            $(document).on('click', '.view-progress', function() {
                var inquiry_id = $(this).data('id');
                $('#progress_inquiry_id').val(inquiry_id);

                $('#progressRemarkModal').modal('show');

                // Load remarks
                $.get('{{ url('get_progress_remarks') }}/' + inquiry_id, function(data) {
                    $('#progressRemarksList').html(data.html);
                });
            });
            $('#progressRemarkModal').on('show.bs.modal', function(e) {
                var inquiryId = $('#progress_inquiry_id').val();
                if (inquiryId) {
                    $.get('/get_progress_remarks/' + inquiryId, function(data) {
                        $('#progressRemarksList').html(data.html);
                    });
                }
            });
            // Submit progress form
            $('#addProgressForm').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var inquiry_id = $('#progress_inquiry_id').val();
                $.ajax({
                    url: '{{ url('add_progress_remark') }}',
                    method: 'POST',
                    data: form.serialize(),
                    success: function() {
                        form[0].reset();
                        // Reload list
                        $.get('{{ url('get_progress_remarks') }}/' + inquiry_id, function(
                            data) {
                            $('#progressRemarksList').html(data.html);
                        });
                        $('#example23').DataTable().ajax.reload(null, false);
                        toastr.success("Progress remark added successfully!", "Success");

                    },
                    error: function() {
                        alert('Error saving remark.');
                    }
                });
            });

            // Follow-up button click
            $(document).on('click', '.view-followup', function() {
                var inquiry_id = $(this).data('id');
                $('#followUpModal').modal('show');
                $('#followup-content').html('Loading...');
                $.get("{{ url('/get_follow_up') }}/" + inquiry_id, function(data) {
                    $('#followup-content').html(data);
                });
            });

            // Submit follow-up form
            $(document).on('submit', '#addFollowupForm', function(e) {
                e.preventDefault();
                var form = $(this);
                var inquiryId = form.find('input[name="inquiry_id"]').val();

                $.ajax({
                    url: "{{ url('add_followup_remarks') }}",
                    type: "POST",
                    data: form.serialize(),
                    success: function() {
                        reloadFollowups(inquiryId);
                        form[0].reset();
                        $('#example23').DataTable().ajax.reload(null, false);
                        toastr.success("Follow-up added successfully!", "Success");

                    },
                    error: function() {
                        alert('Failed to add follow-up.');
                    }
                });
            });

            function reloadFollowups(inquiryId) {
                $.ajax({
                    url: "{{ url('get_follow_up') }}/" + inquiryId,
                    type: "GET",
                    success: function(response) {
                        $('#followup-content').html(response);
                    }
                });
            }

            // Change Sales Person button click
            $(document).on('click', '.change-salesperson', function() {
                var inquiry_id = $(this).data('id');
                var current_sp = $(this).data('current-sp');
                
                $('#salesperson_inquiry_id').val(inquiry_id);
                
                // Pre-select the current sales person
                if (current_sp) {
                    $('#sales_person_select').val(current_sp);
                } else {
                    $('#sales_person_select').val('');
                }
                
                $('#changeSalesPersonModal').modal('show');
            });

            // Submit change sales person form
            $('#changeSalesPersonForm').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                
                $.ajax({
                    url: '{{ url('update_sales_person') }}',
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#changeSalesPersonModal').modal('hide');
                            form[0].reset();
                            $('#example23').DataTable().ajax.reload(null, false);
                            toastr.success(response.message, "Success");
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Error updating sales person.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg, "Error");
                    }
                });
            });

            // Bulk Assignment Logic
            // Handle Check All
            $(document).on('change', '#checkAll', function() {
                $('.inquiry-checkbox').prop('checked', $(this).prop('checked'));
                toggleBulkButton();
            });

            // Handle Individual Checkbox
            $(document).on('change', '.inquiry-checkbox', function() {
                if (!$(this).prop('checked')) {
                    $('#checkAll').prop('checked', false);
                }
                toggleBulkButton();
            });

            // Prevent row expansion when clicking checkbox
            $(document).on('click', '.inquiry-checkbox', function(e) {
                e.stopPropagation();
            });

            function toggleBulkButton() {
                if ($('.inquiry-checkbox:checked').length > 0) {
                    $('#bulkAssignBtn').removeClass('d-none');
                    $('#syncReviewsBtn').removeClass('d-none');
                } else {
                    $('#bulkAssignBtn').addClass('d-none');
                    $('#syncReviewsBtn').addClass('d-none');
                }
            }

            // Open Bulk Modal
            $('#bulkAssignBtn').click(function() {
                var selected = [];
                $('.inquiry-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) return;

                $('#selectedCount').text(selected.length);
                $('#bulkInquiryIds').empty();
                selected.forEach(function(id) {
                    $('#bulkInquiryIds').append('<input type="hidden" name="inquiry_ids[]" value="' + id + '">');
                });

                $('#bulkAssignmentModal').modal('show');
            });

            // Submit Bulk Form
            $('#bulkAssignmentForm').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                
                $.ajax({
                    url: '{{ url('bulk_update_sales_person') }}',
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#bulkAssignmentModal').modal('hide');
                            form[0].reset();
                            $('#checkAll').prop('checked', false);
                            $('#bulkAssignBtn').addClass('d-none');
                            $('#example23').DataTable().ajax.reload(null, false);
                            toastr.success(response.message, "Success");
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Error updating sales persons.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg, "Error");
                    }
                });
            });

        });
    </script>



    <script type="text/javascript">
        $(document).ready(function() {
            
            // Pre-fill filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('salesperson_id')) {
                $('select[name="sales_person"]').val(urlParams.get('salesperson_id')).trigger('change.select2');
            }
            if (urlParams.has('status')) {
                $('select[name="status"]').val(urlParams.get('status')).trigger('change.select2');
            }

            // ✅ Show a friendly inline banner instead of the native alert() popup on Ajax errors
            $.fn.dataTable.ext.errorMode = 'none';

            var table = $('#example23').DataTable({
                responsive: {
                    details: {
                        type: 'column',
                        target: '.dtr-control',
                        renderer: function (api, rowIdx, columns) {
                            var data = api.row(rowIdx).data();
                            var rowId = data.id_inquiry;

                            // Left Column Data
                            var services = data.services || '-';
                            var city = data.city || '-';
                            var createdBy = data.created_by || '-';
                            var createdAt = data.created_at || '-';
                            var updatedAt = data.updated_at || '-';
                            
                            // To match formats: Ensure created_at and updated_at are standard (Y-m-d H:i)
                            // We can use JS Date parsing to match them if necessary, but assume server sends them somewhat formatted.

                            var leftCol = '<div class="col-12 col-md-5 mb-3 mb-md-0">';
                            leftCol += '<h6 class="fw-bold mb-3 border-bottom pb-2">Inquiry Details</h6>';
                            leftCol += '<div class="mb-2"><strong><i class="fa fa-tags text-secondary me-2"></i>Services:</strong><br><div class="mt-1">' + services + '</div></div><hr class="my-2">';
                            leftCol += '<div class="mb-2"><strong><i class="fa fa-city text-secondary me-2"></i>City:</strong> ' + city + '</div><hr class="my-2">';
                            leftCol += '<div class="mb-2"><strong><i class="fa fa-user-plus text-secondary me-2"></i>Created By:</strong> ' + createdBy + '</div><hr class="my-2">';
                            leftCol += '<div class="mb-2"><strong><i class="fa fa-clock text-secondary me-2"></i>Created At:</strong> ' + createdAt + '</div><hr class="my-2">';
                            leftCol += '<div class="mb-2"><strong><i class="fa fa-history text-secondary me-2"></i>Updated At:</strong> ' + updatedAt + '</div>';
                            leftCol += '</div>';

                            var rightCol = '<div class="col-12 col-md-7 ps-md-4 border-start-md">';
                            rightCol += '<h6 class="fw-bold mb-3 border-bottom pb-2">Remarks & History</h6>';
                            rightCol += '<div class="mb-3"><strong><i class="fa fa-comment text-secondary me-2"></i>Remarks:</strong><br><div class="text-muted mt-1" style="word-wrap: break-word; white-space: normal; overflow-wrap: break-word;">' + (data.initial_remarks || '-') + '</div></div><hr class="my-2">';
                            rightCol += '<div class="mt-3"><strong><i class="fa fa-history text-secondary me-2"></i>Follow-Up History:</strong><br>';
                            rightCol += '<div id="followup-container-' + rowId + '" class="mt-2 followup-history-scrollbox">';
                            rightCol += '<div class="text-center my-3"><i class="fa fa-spinner fa-spin fa-lg text-primary"></i></div>';
                            rightCol += '</div>';
                            rightCol += '<div class="text-center mt-2 mb-3"><button type="button" class="btn btn-sm btn-outline-primary load-more-followups" data-id="' + rowId + '" data-offset="0" style="display:none;">Show More</button></div><hr class="my-2">';
                            
                            // Add Progress Remarks here
                            if(data.progress_remarks_html) {
                                rightCol += '<div class="mt-3"><strong><i class="fa fa-tasks text-success me-2"></i>Progress Remarks:</strong><br>';
                                rightCol += '<div class="mt-2">' + data.progress_remarks_html + '</div></div>';
                            }
                            
                            rightCol += '</div></div>';

                            var html = '<div class="container-fluid py-3 px-2 bg-light rounded shadow-sm border"><div class="row">' + leftCol + rightCol + '</div></div>';

                            // Delay AJAX call slightly to allow DOM insertion
                            setTimeout(function() {
                                loadFollowups(rowId, 0, true);
                            }, 100);

                            return html;
                        }
                    }
                },
                paging: true,
                searching: true, // global search box
                ordering: true,
                orderCellsTop: true,
                order: [[1, 'desc']], // Default sort by ID (column 1 now)
                info: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                    lengthMenu: "_MENU_",
                    paginate: {
                        previous: '<',
                        next: '>'
                    },
                    processing: '<div class="d-flex align-items-center justify-content-center gap-2 py-3"><i class="fa fa-spinner fa-spin text-primary"></i><span class="text-secondary">Loading inquiries…</span></div>',
                    emptyTable: 'No inquiries found.',
                    zeroRecords: 'No matching inquiries found for the current filters/search.'
                },
                stateSave: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pageLength: 25,
                orderCellsTop: true,
              

                createdRow: function(row, data, dataIndex) {
                    if (data.row_class) {
                        $(row).addClass(data.row_class);
                    }
                },
                ajax: {
                    url: "{{ url('inquiry_ajax_list') }}",
                    data: function(d) {
                        d.inquiry_type = $('select[name="inquiry_type"]').val();
                        d.sales_person = $('select[name="sales_person"]').val();
                        d.status = $('select[name="status"]').val();
                        d.id_inquiry = $('input[name="id_inquiry"]').val();
                        d.date_from = $('input[name="date_from"]').val();
                        d.date_to = $('input[name="date_to"]').val();
                        d.customer_name = $('input[name="customer_name"]').val();
                        d.customer_cell = $('input[name="customer_cell"]').val();
                        d.customer_email = $('input[name="customer_email"]').val();
                        d.sales_reference = $('select[name="sales_reference"]').val();
                        d.fud_filter = $('select[name="fud_filter"]').val();
                        
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('salesperson_id') && !d.sales_person) {
                            d.sales_person = urlParams.get('salesperson_id');
                        }
                        if (urlParams.has('status') && !d.status) {
                            d.status = urlParams.get('status');
                        }
                    }
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'id_inquiry',
                        name: 'inquiry.id_inquiry',
                        className: 'dtr-control'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'customer_cell',
                        name: 'customer_cell'
                    },

                    /*{
                        data: 'customer_info',
                        name: 'customer_info'
                    },*/

                    {
                        data: 'inquiry_type_name',
                        name: 'inquiry_type_name'
                    },
                    {
                        data: 'salesperson_name',
                        name: 'salesperson_name'
                    },
                    {
                        data: 'sales_ref_name',
                        name: 'sales_ref_name'
                    },
                    {
                        data: 'followup_date',
                        name: 'followup_date'
                    },
                    {
                        data: 'travel_date',
                        name: 'travel_date'
                    },
                    {
                        data: 'status',
                        name: 'inquiry.status'
                    },
                    {
                        data: 'services',
                        name: 'inquiry.services_sub_services'
                    },

                    {
                        data: 'city',
                        name: 'inquiry.city'
                    },
                    

                    {
                        data: 'created_at',
                        name: 'inquiry.created_at'
                    },
                    {
                        data: 'remarks',
                        name: 'inquiry.remarks',
                        className: 'remarks-content'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'updated_at',
                        name: 'inquiry.updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 250, -1],
                    [10, 25, 50, 100, 250],
                ],
                columnDefs: [{
                    targets: -1,
                    className: 'text-center'
                }],
                dom: "<'row mb-3'<'col-md-8 d-flex align-items-center gap-2'B l><'col-md-4'f>>t<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        className: 'btn btn-sm btn-primaryHtml5',
                        exportOptions: {
                            columns: [1, 2, 3, 5, 6, 7, 8, 9, 10, 11] // Skip checkbox and action columns
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 8; // Reduce font size to fit everything
                            doc.styles.tableHeader.fontSize = 9;
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-sm btn-primaryHtml5',
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        }
                    }
                ]
            });

            // ✅ Friendly Ajax error handling — no native alert() popups
            table.on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables error:', message);
                let friendlyMsg = 'We could not load the inquiries list. Please try again.';
                try {
                    const xhr = settings.jqXHR;
                    if (xhr && xhr.responseJSON && xhr.responseJSON.error) {
                        friendlyMsg = xhr.responseJSON.error;
                    }
                } catch (err) {}
                $('#inquiryTableAlertMsg').text(friendlyMsg);
                $('#inquiryTableAlert').removeClass('d-none');
                toastr.error(friendlyMsg, 'Failed to load inquiries');
            });

            table.on('xhr.dt', function() {
                const xhr = table.ajax.xhr ? table.ajax.xhr() : null;
                if (xhr && xhr.status >= 200 && xhr.status < 300) {
                    $('#inquiryTableAlert').addClass('d-none');
                }
            });

            $('#inquiryTableRetryBtn').on('click', function() {
                $('#inquiryTableAlert').addClass('d-none');
                table.ajax.reload();
            });

            // ✅ Per-column search (thead)
            $('#example23 thead tr:eq(1) th').each(function(i) {
                $('input', this).on('keyup change clear', function() {
                    if (table.column(i).search() !== this.value) {
                        table.column(i).search(this.value).draw();
                    }
                });
            });
            $('select[name="fud_filter"]').on('change', function() {
                table.ajax.reload();
            });

            // ✅ Custom filter form (top)
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                saveInquiryFilters();
                table.ajax.reload();
            });

            // Auto save on any filter change
            $('#filterForm').on('change input', 'input, select', function() {
                saveInquiryFilters();
            });

            // Auto save search term on keyup
            $(document).on('keyup input search', '#example23_filter input', function() {
                saveInquiryFilters();
            });

            // ✅ Reset filters button
            $('#resetFilters').on('click', function() {
                try {
                    sessionStorage.removeItem(INQ_SESSION_KEY);
                } catch (e) {}
                $('#filterForm')[0].reset();
                $('#filterForm select').val('').trigger('change.select2');
                table.search('').draw();
                table.ajax.reload();
            });

            // Restore saved search on initial load
            try {
                var savedState = JSON.parse(sessionStorage.getItem(INQ_SESSION_KEY) || '{}');
                if (savedState.table_search) {
                    table.search(savedState.table_search).draw();
                }
            } catch (e) {}

            // Isolate wheel scrolling so cursor hovering over followups/remarks scrolls ONLY that box, NEVER the screen
            $(document).on('wheel', '.followup-history-scrollbox, .followup-modal-list, #progressRemarksList', function(e) {
                var el = this;
                var scrollTop = el.scrollTop;
                var scrollHeight = el.scrollHeight;
                var height = el.clientHeight;
                var delta = e.originalEvent.deltaY;
                var up = delta < 0;

                if (!up && delta > (scrollHeight - height - scrollTop)) {
                    el.scrollTop = scrollHeight;
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                } else if (up && -delta > scrollTop) {
                    el.scrollTop = 0;
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                e.stopPropagation();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#toggleFilterBtn').on('click', function() {
                $('#filterCollapse').slideToggle(200);
                $('#filterIcon').toggleClass('fa-chevron-up fa-chevron-down');
            });
        });
    </script>
    <script>
        function loadFollowups(inquiryId, offset, initial = false) {
            var container = $('#followup-container-' + inquiryId);
            var btn = container.siblings('.text-center').find('.load-more-followups');
            
            // Always ensure the container has the scrollbox class and styles applied
            container.addClass('followup-history-scrollbox');

            if (!initial) {
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            }

            $.ajax({
                url: "{{ url('get_more_followups') }}",
                type: 'GET',
                data: { id: inquiryId, offset: offset },
                success: function(response) {
                    if (initial) {
                        container.html(response.html || '<div class="text-muted fst-italic">No follow-ups found.</div>');
                    } else {
                        container.append(response.html);
                    }

                    if (response.has_more) {
                        btn.show().prop('disabled', false).html('Show More').data('offset', offset + 5);
                    } else {
                        btn.hide();
                    }
                },
                error: function() {
                    if (initial) container.html('<div class="text-danger">Failed to load follow-ups.</div>');
                    btn.prop('disabled', false).html('Show More');
                }
            });
        }

        $(document).on('click', '.load-more-followups', function() {
            var id = $(this).data('id');
            var offset = $(this).data('offset');
            loadFollowups(id, offset, false);
        });
    </script>
    @can('Reviews sync')
    <script>
        // Sync to Reviews (Reviews module)
        $(function() {
            $('#syncReviewsBtn').on('click', function() {
                var ids = $('.inquiry-checkbox:checked').map(function() { return $(this).val(); }).get();
                if (!ids.length) return;

                $('#syncReviewIds').html(ids.map(function(id) {
                    return '<input type="hidden" name="inquiry_ids[]" value="' + id + '">';
                }).join(''));
                $('#syncReviewsSubmit').prop('disabled', true);
                $('#syncAutoHint').text('');
                $('#syncPreviewBox').html('<div class="text-muted text-sm"><i class="fa fa-spinner fa-spin me-1"></i> Checking selected inquiries...</div>');
                $('#syncReviewsModal').modal('show');

                $.post('{{ route('reviews.sync_preview') }}', { _token: '{{ csrf_token() }}', inquiry_ids: ids })
                    .done(function(r) {
                        var html = '<div class="alert alert-info text-white mb-0">'
                            + 'Selected: <strong>' + r.selected + '</strong> &nbsp;·&nbsp; Will be synced: <strong>' + r.eligible + '</strong>';
                        if (r.already) html += '<br><small>Already in Reviews (skipped): ' + r.already + '</small>';
                        if (r.not_completed) html += '<br><small>Not Completed (skipped): ' + r.not_completed + '</small>';
                        html += '</div>';
                        $('#syncPreviewBox').html(html);
                        $('#syncReviewsSubmit').prop('disabled', r.eligible < 1);
                        if (!r.rotation_count) {
                            $('#syncAutoHint').text('No review officers are set in Integration Settings. With "Auto" the reviews will be Unassigned.');
                        }
                    })
                    .fail(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not check the selected inquiries.';
                        $('#syncPreviewBox').html('<div class="alert alert-danger text-white mb-0">' + $('<div>').text(msg).html() + '</div>');
                    });
            });

            $('#syncReviewsForm').on('submit', function(e) {
                e.preventDefault();
                var $btn = $('#syncReviewsSubmit').prop('disabled', true);
                $.post('{{ route('reviews.sync') }}', $(this).serialize())
                    .done(function(r) {
                        $('#syncReviewsModal').modal('hide');
                        $('#checkAll').prop('checked', false);
                        $('.inquiry-checkbox').prop('checked', false);
                        $('#bulkAssignBtn, #syncReviewsBtn').addClass('d-none');
                        toastr.success(r.message, 'Reviews');
                    })
                    .fail(function(xhr) {
                        toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Sync failed.', 'Error');
                        $btn.prop('disabled', false);
                    });
            });
        });
    </script>
    @endcan
@endpush
