@extends('layouts.user_type.auth')

@section('content')
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        /* Row highlighting with subtle tints */
        .row-today { 
            background-color: #fffdf5 !important; 
            border-left: 4px solid #ffc107 !important;
        }
        .row-overdue { 
            background-color: #fff9f9 !important; 
            border-left: 4px solid #dc3545 !important;
        }
        .row-upcoming { 
            background-color: #f8fff8 !important; 
            border-left: 4px solid #28a745 !important;
        }
        .row-no-followup {
            border-left: 4px solid #e9ecef !important;
        }

        /* Disable DataTables default sorting and zebra background highlight */
        table.dataTable tbody tr > .sorting_1,
        table.dataTable tbody tr > .sorting_2,
        table.dataTable tbody tr > .sorting_3,
        table.dataTable.display tbody tr.odd,
        table.dataTable.display tbody tr.even,
        table.dataTable.stripe tbody tr.odd,
        table.dataTable.stripe tbody tr.even,
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: transparent !important;
            box-shadow: none !important;
        }

        /* Table header styling */
        #example23 thead th {
            background-color: #ffffff;
            color: #444;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
            border-top: 1px solid #eee !important;
        }

        /* Soften sorting icons */
        table.dataTable thead .sorting:before, 
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:before,
        table.dataTable thead .sorting_desc:after {
            opacity: 0.3 !important;
        }
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_desc:after {
            opacity: 0.8 !important;
            color: #5e72e4 !important; /* Theme color */
        }

        /* Column specific adjustments */
        .remarks-content {
            max-width: 250px;
            white-space: normal;
            font-size: 12px;
        }


        /* Search input styling */
        .dataTables_filter input {
            padding: 5px 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-left: 10px;
        }
    </style>
    <div class="container-fluid">

        {{-- 🔍 Filter Panel --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header p-3 d-flex justify-content-between align-items-center cursor-pointer" id="toggleFilterBtn">
                <h6 class="fw-bold mb-0 text-primary"><i class="fa fa-filter me-2"></i>Filter Follow-ups</h6>
                <button class="btn btn-sm btn-link p-0 text-primary">
                    <i class="fa fa-chevron-down" id="filterIcon"></i>
                </button>
            </div>
            <div id="filterCollapse" style="display: none;">
                <div class="card-body bg-light rounded-bottom">
                    <form id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Assigned User</label>
                                <select class="form-select form-select-sm" name="sales_person">
                                    <option value="">All Users</option>
                                    @foreach ($sales_person as $sp)
                                        <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Status</label>
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="Open">Open</option>
                                    <option value="In-Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                    <option value="Confirmed">Confirmed</option>
                                    <option value="Quotation">Quotation</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">From Date</label>
                                <input type="date" class="form-control form-control-sm" name="date_from">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">To Date</label>
                                <input type="date" class="form-control form-control-sm" name="date_to">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Customer Name</label>
                                <input type="text" class="form-control form-control-sm" name="customer_name" placeholder="Name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Mobile</label>
                                <input type="text" class="form-control form-control-sm" name="customer_cell" placeholder="Mobile">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Inquiry ID</label>
                                <input type="text" class="form-control form-control-sm" name="id_inquiry" placeholder="e.g. 01, 02, 30">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-sm btn-primary px-4">Apply</button>
                            <button type="button" id="resetFilters" class="btn btn-sm btn-outline-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 📋 Follow-ups Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header bg-white pb-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="fa fa-calendar-check me-2 text-warning"></i> Follow-up List
                            </h5>
                            <!-- <button type="button" class="btn btn-sm btn-info d-none" id="bulkAssignBtn">
                                <i class="fa fa-users-cog"></i> Bulk Assign
                            </button> -->
                        </div>
                    </div>
                   <div class="card-body">
                        <div class="table-responsive">
                            <table id="example23" class="table table-bordered nowrap align-middle"
                                width="100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Inq ID</th>
                                        <th>Customer</th>
                                        <th>Number</th>
                                        <th>Type</th>
                                        <th>SP</th>
                                        <th>SR</th>
                                        <th>Follow-up</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th class="none">Remarks</th>
                                        <th class="none">Created By</th>
                                        <th class="none">Updated At</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        @foreach (range(0, 12) as $i)
                                            <th><input type="text" class="form-control form-control-sm" placeholder="Search..." /></th>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Modals --}}
    <div class="modal fade" id="followUpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
                    <h5 class="modal-title fw-semibold"><i class="fa fa-pencil-alt me-1 text-primary"></i> Add Progress Remark</h5>
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
                                        <option value="{{ $type->id_follow_up_types }}">{{ $type->type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Progress Date <span class="text-danger">*</span></label>
                                <input type="date" name="progress_date" class="form-control" required value="{{ date('Y-m-d') }}">
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
                                        <option @if ($user->id == auth()->user()->id) selected @endif value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Remarks <span class="text-danger">*</span></label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter your remarks..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-success px-4">Add Remark</button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Progress History</h6>
                    <div id="progressRemarksList" class="small" style="max-height: 250px; overflow-y: auto;">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bulkAssignmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-semibold">Bulk Assign Sales Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkAssignmentForm">
                        @csrf
                        <div id="bulkInquiryIds"></div>
                        <div class="alert alert-info">Selected: <strong id="selectedCount">0</strong></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Sales Person <span class="text-danger">*</span></label>
                            <select class="form-select" name="sales_person_id" required>
                                <option value="">Select Sales Person</option>
                                @foreach ($sales_person as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-info px-4">Assign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#example23').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                createdRow: function(row, data, dataIndex) {
                    if (data.row_class) {
                        $(row).addClass(data.row_class);
                    }
                },
                ajax: {
                    url: "{{ route('followups.data') }}",
                    data: function(d) {
                        d.sales_person = $('select[name="sales_person"]').val();
                        d.status = $('select[name="status"]').val();
                        d.date_from = $('input[name="date_from"]').val();
                        d.date_to = $('input[name="date_to"]').val();
                        d.customer_name = $('input[name="customer_name"]').val();
                        d.customer_cell = $('input[name="customer_cell"]').val();
                        d.id_inquiry = $('input[name="id_inquiry"]').val();
                        
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('sales_person') && !d.sales_person) {
                            d.sales_person = urlParams.get('sales_person');
                        }
                        if (urlParams.has('filter') && !d.filter) {
                            d.filter = urlParams.get('filter');
                        }
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'id_inquiry', name: 'followup_remarks.inquiry_id' },
                    { data: 'customer_name', name: 'customers.customer_name' },
                    { data: 'customer_cell', name: 'customers.customer_cell' },
                    { data: 'inquiry_type', name: 'inquirytypes.type_name' },
                    { data: 'saleperson', name: 'sp.name' },
                    { data: 'sales_reference', name: 'sales_reference.type_name' },
                    { data: 'followup_date', name: 'followup_remarks.followup_date' },
                    { data: 'status', name: 'followup_remarks.followup_status' },
                    { data: 'created_at', name: 'followup_remarks.created_at' },
                    { data: 'remarks', name: 'followup_remarks.remarks', className: 'none' },
                    { data: 'created_by', name: 'cb.name', className: 'none' },
                    { data: 'updated_at', name: 'followup_remarks.updated_at', className: 'none' },
                ],
                dom: "<'row mb-3'<'col-md-8 d-flex align-items-center gap-2'B l><'col-md-4'f>>t<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        className: 'btn btn-sm btn-primaryHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-sm btn-primaryHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    }
                ],
                lengthMenu: [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                order: [[0, 'desc']],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>',
                    paginate: {
                        next: '>',
                        previous: '<'
                    }
                }
            });

            // Column Search
            $('#example23 tfoot th').each(function() {
                var that = this;
                $('input', this).on('keyup change clear', function() {
                    if (table.column($(that).index() + ':visible').search() !== this.value) {
                        table.column($(that).index() + ':visible').search(this.value).draw();
                    }
                });
            });

            // Session-based filter persistence for Follow-ups
            const FOL_SESSION_KEY = 'ims_filters_followups_state';

            function saveFollowupFilters() {
                try {
                    var state = {
                        sales_person: $('select[name="sales_person"]').val() || '',
                        status: $('select[name="status"]').val() || '',
                        date_from: $('input[name="date_from"]').val() || '',
                        date_to: $('input[name="date_to"]').val() || '',
                        customer_name: $('input[name="customer_name"]').val() || '',
                        customer_cell: $('input[name="customer_cell"]').val() || '',
                        id_inquiry: $('input[name="id_inquiry"]').val() || '',
                        table_search: $('.dataTables_filter input').length ? $('.dataTables_filter input').val() : ''
                    };
                    sessionStorage.setItem(FOL_SESSION_KEY, JSON.stringify(state));
                } catch(e) {}
            }

            function restoreFollowupFilters() {
                var urlParams = new URLSearchParams(window.location.search);
                var hasUrl = urlParams.toString().length > 0;
                var state = null;
                if (!hasUrl) {
                    try {
                        var raw = sessionStorage.getItem(FOL_SESSION_KEY);
                        if (raw) state = JSON.parse(raw);
                    } catch(e) {}
                }

                var hasActive = false;
                if (state) {
                    if (state.sales_person) { $('select[name="sales_person"]').val(state.sales_person).trigger('change.select2'); hasActive = true; }
                    if (state.status) { $('select[name="status"]').val(state.status).trigger('change.select2'); hasActive = true; }
                    if (state.date_from) { $('input[name="date_from"]').val(state.date_from); hasActive = true; }
                    if (state.date_to) { $('input[name="date_to"]').val(state.date_to); hasActive = true; }
                    if (state.customer_name) { $('input[name="customer_name"]').val(state.customer_name); hasActive = true; }
                    if (state.customer_cell) { $('input[name="customer_cell"]').val(state.customer_cell); hasActive = true; }
                    if (state.id_inquiry) { $('input[name="id_inquiry"]').val(state.id_inquiry); hasActive = true; }
                }

                if (hasActive) {
                    $('#filterCollapse').show();
                    $('#filterIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }
                return state;
            }

            var savedFolState = restoreFollowupFilters();
            if (savedFolState && savedFolState.table_search) {
                table.search(savedFolState.table_search).draw();
            }

            // Filters form events
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                saveFollowupFilters();
                table.ajax.reload();
            });

            $('#filterForm').on('change input', 'input, select', function() {
                saveFollowupFilters();
            });

            $(document).on('keyup input search', '.dataTables_filter input', function() {
                saveFollowupFilters();
            });

            $('#resetFilters').on('click', function() {
                try { sessionStorage.removeItem(FOL_SESSION_KEY); } catch(e) {}
                $('#filterForm')[0].reset();
                $('#filterForm select').val('').trigger('change.select2');
                table.search('').draw();
                table.ajax.reload();
            });
            $('#toggleFilterBtn').on('click', function() { $('#filterCollapse').slideToggle(200); $('#filterIcon').toggleClass('fa-chevron-down fa-chevron-up'); });

            // Follow-up & Progress Modals (Similar to inquiry index)
            $(document).on('click', '.view-progress', function() {
                var id = $(this).data('id');
                $('#progress_inquiry_id').val(id);
                $('#progressRemarkModal').modal('show');
                $.get('{{ url('get_progress_remarks') }}/' + id, function(data) { $('#progressRemarksList').html(data.html); });
            });

            $('#addProgressForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ url('add_progress_remark') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function() {
                        toastr.success("Remark added");
                        table.ajax.reload(null, false);
                        $.get('{{ url('get_progress_remarks') }}/' + $('#progress_inquiry_id').val(), function(data) { $('#progressRemarksList').html(data.html); });
                    }
                });
            });

            $(document).on('click', '.view-followup', function() {
                var id = $(this).data('id');
                $('#followUpModal').modal('show');
                $.get("{{ url('/get_follow_up') }}/" + id, function(data) {
                    $('#followup-content').html(data);
                    if (typeof toggleFollowupDate === 'function') {
                        toggleFollowupDate();
                    }
                });
            });

            $(document).on('change', '#followupStatus', function() {
                if (typeof toggleFollowupDate === 'function') {
                    toggleFollowupDate();
                }
            });

            $(document).on('submit', '#addFollowupForm', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('add_followup_remarks') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function() {
                        toastr.success("Success");
                        var id = $('#addFollowupForm').find('input[name="inquiry_id"]').val();
                        $.get("{{ url('/get_follow_up') }}/" + id, function(data) { $('#followup-content').html(data); });
                        table.ajax.reload(null, false);
                    }
                });
            });

            // Bulk actions
            $(document).on('change', '#checkAll', function() { $('.inquiry-checkbox').prop('checked', $(this).prop('checked')); toggleBulkButton(); });
            $(document).on('change', '.inquiry-checkbox', function() { toggleBulkButton(); });
            function toggleBulkButton() { if ($('.inquiry-checkbox:checked').length > 0) $('#bulkAssignBtn').removeClass('d-none'); else $('#bulkAssignBtn').addClass('d-none'); }

            $('#bulkAssignBtn').click(function() {
                var selected = [];
                $('.inquiry-checkbox:checked').each(function() { selected.push($(this).val()); });
                $('#selectedCount').text(selected.length);
                $('#bulkInquiryIds').empty();
                selected.forEach(function(id) { $('#bulkInquiryIds').append('<input type="hidden" name="inquiry_ids[]" value="' + id + '">'); });
                $('#bulkAssignmentModal').modal('show');
            });

            $('#bulkAssignmentForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ url('bulk_update_sales_person') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function() {
                        toastr.success("Assigned");
                        $('#bulkAssignmentModal').modal('hide');
                        $('#checkAll').prop('checked', false);
                        table.ajax.reload();
                    }
                });
            });
        });
    </script>
 
@endpush
