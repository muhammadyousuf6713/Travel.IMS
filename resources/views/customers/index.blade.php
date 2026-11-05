@extends('layouts.user_type.auth')

@section('content')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <div class="container-fluid">

        {{-- 🔍 Filter Panel --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header p-3 d-flex justify-content-between align-items-center cursor-pointer" id="toggleFilterBtn">
                <h6 class="fw-bold mb-0 text-primary"><i class="fa fa-filter me-2"></i>Filter Customers</h6>
                <button class="btn btn-sm btn-link p-0 text-primary">
                    <i class="fa fa-chevron-down" id="filterIcon"></i>
                </button>
            </div>
            <div id="filterCollapse" style="display: none;">
                <div class="card-body bg-light rounded-bottom">
                    <form id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Customer Name</label>
                                <input type="text" class="form-control form-control-sm" name="customer_name" placeholder="Name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Mobile</label>
                                <input type="text" class="form-control form-control-sm" name="customer_cell" placeholder="Mobile">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Email</label>
                                <input type="text" class="form-control form-control-sm" name="customer_email" placeholder="Email">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">City</label>
                                <input type="text" class="form-control form-control-sm" name="city" placeholder="City">
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

        {{-- 📋 Customers Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 shadow-sm border-0 p-2">
                    <div class="card-header bg-white pb-0">
                        <div class="d-flex flex-row justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold"><i class="fa fa-users me-2 text-primary"></i>Customers List</h5>
                            <a href="{{ url('customers/create') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus me-1"></i> Add Customers
                            </a>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table id="customers-table" class="table table-sm table-striped table-bordered nowrap align-middle w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">S.No</th>
                                        <th style="width: 40px;">IM</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>SP</th>
                                        <th>Mobile</th>
                                        <th style="width: 30px;">WA</th>
                                        <th>Other/PTCL</th>
                                        <th>Email</th>
                                        <th class="none">Address</th>
                                        <th>Country</th>
                                        <th>City</th>
                                        <th>Created</th>
                                        <th class="none">Updated</th>
                                        <th style="width: 80px;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        @foreach (range(1, 14) as $i)
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('customers.index') }}",
                    data: function(d) {
                        d.customer_name = $('input[name="customer_name"]').val();
                        d.customer_cell = $('input[name="customer_cell"]').val();
                        d.customer_email = $('input[name="customer_email"]').val();
                        d.city = $('input[name="city"]').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'customer_type', name: 'customer_type' },
                    { data: 'sale_person_name', name: 'salePerson.name', defaultContent: '-' },
                    { data: 'customer_mobile', name: 'customer_cell' },
                    { data: 'whatsapp_enabled', name: 'whatsapp_check', orderable: false, searchable: false },
                    { data: 'customer_phone1', name: 'customer_phone1' },
                    { data: 'customer_email', name: 'customer_email' },
                    { data: 'customer_address', name: 'customer_address' },
                    { data: 'country', name: 'country' },
                    { data: 'city_name', name: 'city', defaultContent: '-' },
                    { data: 'created_at', name: 'created_at', render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '-';
                    }},
                    { data: 'updated_at', name: 'updated_at', render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '-';
                    }},
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                dom: "<'row mb-3'<'col-md-8 d-flex align-items-center gap-2'B l><'col-md-4'f>>t<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        className: 'btn btn-sm btn-primaryHtml5',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 7, 8, 10, 11]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-sm btn-primaryHtml5',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 7, 8, 10, 11]
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
            $('#customers-table tfoot th').each(function() {
                var that = this;
                $('input', this).on('keyup change clear', function() {
                    if (table.column($(that).index() + ':visible').search() !== this.value) {
                        table.column($(that).index() + ':visible').search(this.value).draw();
                    }
                });
            });

            // Session-based filter persistence for Customers
            const CUST_SESSION_KEY = 'ims_filters_customers_state';

            function saveCustomerFilters() {
                try {
                    var state = {
                        customer_name: $('input[name="customer_name"]').val() || '',
                        customer_cell: $('input[name="customer_cell"]').val() || '',
                        customer_email: $('input[name="customer_email"]').val() || '',
                        city: $('input[name="city"]').val() || '',
                        customer_type: $('select[name="customer_type"]').val() || '',
                        customer_category: $('select[name="customer_category"]').val() || '',
                        table_search: $('.dataTables_filter input').length ? $('.dataTables_filter input').val() : ''
                    };
                    sessionStorage.setItem(CUST_SESSION_KEY, JSON.stringify(state));
                } catch(e) {}
            }

            function restoreCustomerFilters() {
                try {
                    var raw = sessionStorage.getItem(CUST_SESSION_KEY);
                    if (!raw) return;
                    var state = JSON.parse(raw);
                    var hasActive = false;
                    if (state.customer_name) { $('input[name="customer_name"]').val(state.customer_name); hasActive = true; }
                    if (state.customer_cell) { $('input[name="customer_cell"]').val(state.customer_cell); hasActive = true; }
                    if (state.customer_email) { $('input[name="customer_email"]').val(state.customer_email); hasActive = true; }
                    if (state.city) { $('input[name="city"]').val(state.city); hasActive = true; }
                    if (state.customer_type) { $('select[name="customer_type"]').val(state.customer_type).trigger('change.select2'); hasActive = true; }
                    if (state.customer_category) { $('select[name="customer_category"]').val(state.customer_category).trigger('change.select2'); hasActive = true; }

                    if (hasActive) {
                        $('#filterCollapse').show();
                        $('#filterIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    }
                    if (state.table_search) {
                        table.search(state.table_search).draw();
                    }
                } catch(e) {}
            }
            restoreCustomerFilters();

            $('#filterForm').on('submit', function(e) { 
                e.preventDefault(); 
                saveCustomerFilters();
                table.ajax.reload(); 
            });

            $('#filterForm').on('change input', 'input, select', function() {
                saveCustomerFilters();
            });

            $(document).on('keyup input search', '.dataTables_filter input', function() {
                saveCustomerFilters();
            });

            $('#resetFilters').on('click', function() { 
                try { sessionStorage.removeItem(CUST_SESSION_KEY); } catch(e) {}
                $('#filterForm')[0].reset(); 
                $('#filterForm select').val('').trigger('change.select2');
                table.search('').draw();
                table.ajax.reload(); 
            });
            $('#toggleFilterBtn').on('click', function() { $('#filterCollapse').slideToggle(200); $('#filterIcon').toggleClass('fa-chevron-down fa-chevron-up'); });
        });
    </script>
    <style>
        .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.65rem; border-radius: 0.35rem; }
    </style>
@endpush
