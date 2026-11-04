@extends('layouts.user_type.auth')

@section('content')
    <!-- Quill Editor & SweetAlert2 -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        select.muted {
            color: #999;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered li {
            color: #000 !important;
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545 !important;
            background-color: #fff8f8 !important;
        }
    </style>

    <div class="col-md-12 col-lg-12 col-xl-12">
        <div class="az-content p-0">
            <div class="container-fluid p-0">
                <div class="az-content-body d-flex flex-column">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show text-white text-xs py-2 px-3 mb-3" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> 
                            <strong>Validation Notice:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close text-xs text-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('message'))
                        <div class="alert alert-success alert-dismissible fade show text-white text-xs py-2 px-3 mb-3" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') }}
                            <button type="button" class="btn-close text-xs text-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ url('update_inquiry', $inquiry->id_inquiry) }}" method="post" id="submit_inquiry" novalidate>
                        @csrf
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-white border-bottom pb-3 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">
                                    <i class="fa fa-edit text-primary me-2"></i> Edit Inquiry #{{ $inquiry->id_inquiry }}
                                </h5>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    Status: <strong class="text-primary">{{ $inquiry->status ?? 'Open' }}</strong>
                                </span>
                            </div>
                            
                            <div class="card-body p-4">
                                {{-- Section: Search & Existing Customer --}}
                                <div class="row g-4 align-items-start">

                                    {{-- Search Input --}}
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-semibold text-secondary">
                                                <i class="fa fa-search me-1"></i> Search by Name / Contact
                                            </label>
                                            <input class="form-control shadow-none border-radius-md" id="contact_search"
                                                type="search" placeholder="Type to search customer...">
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-warning flex-fill" onclick="clear_feilds()"
                                                id="clear_customer_information">
                                                <i class="fa fa-eraser me-1"></i> Clear
                                            </button>
                                            <a href="#customer_div"
                                                class="btn btn-success text-white flex-fill toggleCustomerBtn"
                                                id="add_new_customer_btn">
                                                <i class="fa fa-pencil me-1"></i> Customer Form
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Search Result --}}
                                    <div class="col-lg-4" id="search_result"
                                        style="max-height: 200px; overflow-y: auto; padding: 10px;">
                                        <!-- Search results will populate here -->
                                    </div>

                                    {{-- Selected Info --}}
                                    <div class="col-lg-4" id="customer_details" style="border-left: 1px dashed #e3e7ed; padding-left: 20px;">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="small text-secondary fw-semibold">
                                                <i class="fa fa-id-card me-1 text-info"></i> Customer Information
                                            </div>
                                            <img src="{{ asset('img/default_user.png') }}" class="rounded-circle border shadow-sm" style="width: 50px; height: 50px; object-fit: cover;" alt="Profile">
                                        </div>
                                        <p class="mb-1"><i class="fa fa-user text-primary me-2"></i> Customer:
                                            <span class="fw-semibold text-dark text-decoration-underline">{{ $inquiry->customer->customer_name ?? 'N/A' }}</span>
                                        </p>
                                        <p class="mb-1"><i class="fa fa-phone text-success me-2"></i> Contact#:
                                            <span class="fw-semibold text-dark text-decoration-underline">{{ $inquiry->customer->customer_cell ?? 'N/A' }}</span>
                                        </p>
                                        <p class="mb-1"><i class="fa fa-envelope text-danger me-2"></i> Email:
                                            <span class="fw-semibold text-dark text-decoration-underline">{{ $inquiry->customer->customer_email ?? 'N/A' }}</span>
                                        </p>
                                        <p class="mb-1"><i class="fa fa-location-dot text-info me-2"></i> City:
                                            <span class="fw-semibold text-dark text-decoration-underline">{{ $inquiry->customer->city ?? 'N/A' }}</span>
                                        </p>
                                        <p class="mb-1"><i class="fa fa-info-circle text-warning me-2"></i> Type:
                                            <span class="fw-semibold text-dark text-decoration-underline">{{ $inquiry->customer->customer_type ?? 'Individual' }}</span>
                                        </p>
                                    </div>
                                </div>

                                <input type="hidden" name="searched_customer_id" id="searched_customer_id" value="{{ $inquiry->customer_id }}">

                                <div id="customer_div" style="display: block;">
                                    <hr class="my-4">
                                    {{-- Section: Customer Details --}}
                                    <h5 class="fw-bold mb-3 d-flex justify-content-between align-items-center">
                                        <span><i class="fa fa-user-edit text-primary me-1"></i> Customer Details</span>
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <label class="form-label">Customer Name <i class="fa fa-info-circle"
                                                    style="color:red;" data-toggle="tooltip"
                                                    title="This field is required"></i></label>
                                            <input id="customer_name"
                                                onkeyup="this.value=this.value.replace(/[^a-zA-Z0-9 ]/g, '')"
                                                class="form-control" required name="customer_name" placeholder="Full Name"
                                                type="text" value="{{ $inquiry->customer->customer_name ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Customer Type</label>
                                            <select name="customer_type" id="customer_type" class="form-select no-select2">
                                                <option value="Individual" {{ (($inquiry->customer->customer_type ?? '') == 'Individual') ? 'selected' : '' }}>Individual</option>
                                                <option value="Group" {{ (($inquiry->customer->customer_type ?? '') == 'Group') ? 'selected' : '' }}>Group</option>
                                                <option value="Corporate" {{ (($inquiry->customer->customer_type ?? '') == 'Corporate') ? 'selected' : '' }}>Corporate</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Customer Cell <i class="fa fa-info-circle"
                                                    style="color:red;" data-toggle="tooltip"
                                                    title="This field is required"></i></label>
                                            <input type="tel" id="customer_cell" placeholder="(92) 123-4567890"
                                                maxlength="14" class="form-control phone" name="customer_cell" required value="{{ $inquiry->customer->customer_cell ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-md-6">
                                            <label class="form-label">WhatsApp</label>
                                            <input type="text" id="whatsapp_number" class="form-control"
                                                name="customer_whatsapp" placeholder="e.g. (92) 123-4567890" value="{{ $inquiry->customer->whatsapp_number ?? $inquiry->customer->customer_phone1 ?? '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="customer_email" id="customer_email"
                                                class="form-control" placeholder="e.g. example@gmail.com" value="{{ $inquiry->customer->customer_email ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-md-8">
                                            <label class="form-label">Address</label>
                                            <input type="text" name="customer_address" id="customer_address"
                                                class="form-control" placeholder="e.g. Complete Address Detail" value="{{ $inquiry->customer->customer_address ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">City</label>
                                            <select name="customer_city" id="customer_city" class="form-control ajax-city-select2" data-placeholder="Select City">
                                                @if(isset($inquiry->customer) && $inquiry->customer->city)
                                                    <option value="{{ $inquiry->customer->city }}" selected>{{ $inquiry->customer->city }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- Section: Travel & Services --}}
                                <h5 class="fw-bold mb-3"><i class="fa fa-plane text-primary me-1"></i> Travel & Services Information</h5>
                                
                                @php
                                    $servicesArray = is_array($inquiry->services) ? $inquiry->services : (json_decode($inquiry->services, true) ?? []);
                                    $firstServiceId = !empty($servicesArray) ? $servicesArray[0] : null;
                                    $serviceSubServices = json_decode($inquiry->services_sub_services, true) ?? [];
                                    if (!$firstServiceId && !empty($serviceSubServices)) {
                                        if (str_contains($serviceSubServices[0], '/')) {
                                            [$firstServiceId] = explode('/', $serviceSubServices[0]);
                                        }
                                    }
                                @endphp

                                <div class="row mt-2">
                                    <div class="rmv_service col-lg-4 mg-t-20 mg-lg-t-0">
                                        <label class="form-label">Services:
                                            <i class="fa fa-info-circle" style="color:red;" data-toggle="tooltip"
                                                title="This field is required"></i>
                                        </label>
                                        <select name="services[]" id="services" class="form-control service_dis no-select2" required>
                                            <option value="">Select Services</option>
                                            @forelse ($services as $service)
                                                <option value="{{ $service->id_other_services }}" {{ ($service->id_other_services == $firstServiceId) ? 'selected' : '' }}>
                                                    {{ $service->service_name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No Results Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    
                                    {{-- Sub Services hidden matching Add Inquiry layout --}}
                                    <div class="rmv_service col-lg-6 mg-t-20 mg-lg-t-0" style="display: none !important;">
                                        <div class="form-group" style="display: none !important;">
                                            <select style="display: none !important;" name="sub_services[]" id="sub_services" multiple="multiple">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 mg-t-20 mg-lg-t-0">
                                        <div class="form-group">
                                            <label class="form-label">Inquiry Type:
                                                <i class="fa fa-info-circle" style="color:red;"
                                                    data-toggle="tooltip" title="This field is required"></i>
                                            </label>
                                            <select name="inquiry_type" id="inquiry_type" class="form-control no-select2" required>
                                                <option value="" disabled>Select Inquiry Type</option>
                                                @forelse ($inquiry_types as $inq_type)
                                                    <option value="{{ $inq_type->type_id }}" {{ ($inq_type->type_id == $inquiry->inquiry_type) ? 'selected' : '' }}>
                                                        {{ $inq_type->type_name }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No Results Found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mg-t-20 mg-md-t-0">
                                        <label class="form-label">Inquiry Category:</label>
                                        <select name="inquiry_category" id="inquiry_category" class="form-control no-select2">
                                            <option value="">Select Inquiry Category</option>
                                            <option value="Economy" {{ ($inquiry->inquiry_category == 'Economy') ? 'selected' : '' }}>Economy</option>
                                            <option value="Standard" {{ ($inquiry->inquiry_category == 'Standard') ? 'selected' : '' }}>Standard</option>
                                            <option value="2 - Star" {{ ($inquiry->inquiry_category == '2 - Star') ? 'selected' : '' }}>2 - Star</option>
                                            <option value="3 - Star" {{ ($inquiry->inquiry_category == '3 - Star') ? 'selected' : '' }}>3 - Star</option>
                                            <option value="4 - Star" {{ ($inquiry->inquiry_category == '4 - Star') ? 'selected' : '' }}>4 - Star</option>
                                            <option value="5 - Star" {{ ($inquiry->inquiry_category == '5 - Star') ? 'selected' : '' }}>5 - Star</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row" id="append_services"></div>

                                <div class="row">
                                    <div class="col-lg-4 mg-t-20 mg-lg-t-0 mt-2">
                                        <div class="form-group">
                                            <label class="form-label">Sale Reference</label>
                                            <select class="form-control no-select2" id="sale_reference" name="sale_reference" required>
                                                <option value="" disabled>Select Sale Reference</option>
                                                @forelse ($sales_reference as $sale_ref)
                                                    <option value="{{ $sale_ref->type_id }}" {{ ($sale_ref->type_id == $inquiry->sales_reference) ? 'selected' : '' }}>
                                                        {{ $sale_ref->type_name }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No Results Found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 mg-t-20 mg-md-t-0 mt-2">
                                        <label class="form-label">Travel Date:
                                            <i class="fa fa-info-circle" style="color:red;" data-toggle="tooltip"
                                                title="This field is required"></i></label>
                                        <input type="text" readonly name="travel_date" id="travel_date"
                                            class="form-control fc-datepicker2" placeholder="YYYY-MM-DD" required
                                            value="{{ $inquiry->travel_date ?? '' }}" />
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group ml-2 mt-2">
                                            <label class="form-label">Sales Person
                                                <i class="fa fa-info-circle" style="color:red;" data-toggle="tooltip"
                                                    title="This field is required"></i></label>
                                            <select name="sale_person" class="form-control no-select2" id="sale_person" required>
                                                <option value="">Select Sales Person</option>
                                                @forelse ($sale_persons as $sp)
                                                    <option value="{{ $sp['id'] }}" {{ ($sp['id'] == $inquiry->saleperson) ? 'selected' : '' }}>
                                                        {{ $sp['name'] }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No Results Found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Remarks section --}}
                                <div class="col-lg-12 mg-t-20 mg-md-t-0 mt-3">
                                    <label class="form-label">Remarks:
                                        <i class="fa fa-info-circle" style="color:red;" data-toggle="tooltip"
                                            title="This field is required"></i></label>
                                    <div style="width: 100%; margin-top: 10px"
                                        class="col-md-12 ql-wrapper ql-wrapper-demo">
                                        <div id="toolbar-container">
                                            <span class="ql-formats">
                                                <select class="ql-header">
                                                    <option value="1"></option>
                                                    <option value="2"></option>
                                                    <option value="3"></option>
                                                    <option value="4"></option>
                                                    <option value="5"></option>
                                                    <option value="6"></option>
                                                    <option selected></option>
                                                </select>
                                                <button class="ql-bold"></button>
                                                <button class="ql-italic"></button>
                                                <button class="ql-underline"></button>
                                                <button class="ql-strike"></button>
                                            </span>
                                            <span class="ql-formats">
                                                <button class="ql-list" value="ordered"></button>
                                                <button class="ql-list" value="bullet"></button>
                                            </span>
                                            <span class="ql-formats">
                                                <button class="ql-link"></button>
                                                <button class="ql-image"></button>
                                                <button class="ql-video"></button>
                                            </span>
                                        </div>
                                        <div id="editor-container" onkeyup="saveQuillContent()">{!! $inquiry->remarks ?? '' !!}</div>
                                        <textarea name="remarks" id="remarks" style="display: none;">{{ $inquiry->remarks ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4 me-3 d-flex justify-content-end align-items-center gap-2">
                                <a href="{{ url('inquiry') }}" class="btn btn-danger text-white shadow-sm mb-0 px-4 text-uppercase fw-bold">
                                   Cancel
                                </a>
                                <button type="button" id="update_submit_btn" onclick="check_validation()" class="btn btn-primary shadow-sm mb-0 px-4 text-uppercase fw-bold" style="background-color: var(--bs-primary) !important; border-color: var(--bs-primary) !important;">
                                    <i class="fa fa-paper-plane me-2"></i> Update Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Quill Editor & SweetAlert2 -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        document.querySelector(".toggleCustomerBtn").addEventListener("click", function() {
            const customerDiv = document.getElementById("customer_div");
            if (customerDiv.style.display === "none" || customerDiv.style.display === "") {
                customerDiv.style.display = "block";
                this.innerHTML = '<i class="fa fa-minus me-1"></i> Hide Customer Form';
            } else {
                customerDiv.style.display = "none";
                this.innerHTML = '<i class="fa fa-pencil me-1"></i> Show Customer Form';
            }
        });
    </script>

    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'e.g. Number Of Person, Total Travel Days, etc.',
            modules: {
                toolbar: '#toolbar-container'
            }
        });
        
        setTimeout(function() {
            if ($('.ql-header').hasClass('select2-hidden-accessible')) {
                $('.ql-header').select2('destroy');
            }
        }, 100);

        function saveQuillContent() {
            if (quill && quill.root) {
                var remarks = quill.root.innerHTML;
                document.getElementById('remarks').value = remarks;
            }
        }

        function clear_feilds() {
            var customerDetails =
                '<div class="small text-secondary mb-2 fw-semibold">' +
                '<i class="fa fa-id-card me-1 text-info"></i> Customer Information:' +
                '</div>' +
                '<p class="mb-1"><i class="fa fa-user text-primary me-1"></i> Customer: ' +
                '<span class="fw-semibold text-dark text-decoration-underline"></span>' +
                '</p>' +
                '<p class="mb-1"><i class="fa fa-phone text-success me-1"></i> Contact#: ' +
                '<span class="fw-semibold text-dark text-decoration-underline"></span>' +
                '</p>' +
                '<p class="mb-1"><i class="fa fa-envelope text-danger me-1"></i> Email: ' +
                '<span class="fw-semibold text-dark text-decoration-underline"></span>' +
                '</p>' +
                '<p class="mb-1"><i class="fa fa-location-dot text-info me-1"></i> City: ' +
                '<span class="fw-semibold text-dark text-decoration-underline"></span>' +
                '</p>' +
                '<p class="mb-1"><i class="fa fa-info-circle text-warning me-1"></i> Type: ' +
                '<span class="fw-semibold text-dark text-decoration-underline"></span>' +
                '</p>';

            $("#search_result").empty();
            $("#customer_details").empty().append(customerDetails);
            $('#searched_customer_id').val('');
            $('#customer_name').val('');
            $('#customer_type').val('Individual');
            $('#customer_cell').val('');
            $('#whatsapp_number').val('');
            $('#customer_address').val('');
            $('#customer_city').val('').trigger('change');
            $('#customer_email').val('');
        }

        $(document).ready(function() {
            var typingTimer;
            $("#contact_search").on('input keydown', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function() {
                    let val = $("#contact_search").val();
                    if (val.length > 0) {
                        $.ajax({
                            url: "{{ url('customer_list') }}/" + val,
                            type: "GET",
                            success: function(result) {
                                $("#search_result").empty().html(result);
                            }
                        });
                    } else {
                        $("#search_result").empty();
                    }
                }, 500);
            });

            $('#search_result').on('click', '.clickable-data', function() {
                var primaryId = $(this).data('id');
                $.ajax({
                    url: '{{ url('get_customer_details') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: primaryId
                    },
                    success: function(response) {
                        $('#customer_details').empty().html(response.html);
                        $('#searched_customer_id').val(primaryId);

                        var c = response.customer;
                        if (c) {
                            $('#customer_name').val(c.customer_name || '');
                            $('#customer_type').val(c.customer_type || 'Individual');
                            $('#customer_cell').val(c.customer_cell || '');
                            $('#whatsapp_number').val(c.customer_phone1 || c.whatsapp_number || '');
                            $('#customer_email').val(c.customer_email || '');
                            $('#customer_address').val(c.customer_address || '');
                            if (c.city || c.city_id) {
                                var cityName = c.city || c.city_id;
                                if ($('#customer_city').find("option[value='" + cityName + "']").length) {
                                    $('#customer_city').val(cityName).trigger('change');
                                } else { 
                                    var newOption = new Option(cityName, cityName, true, true);
                                    $('#customer_city').append(newOption).trigger('change');
                                }
                            }

                            var customerDiv = document.getElementById("customer_div");
                            if (customerDiv) {
                                customerDiv.style.display = "block";
                            }
                        }

                        $('#search_result').empty();
                        $('#contact_search').val('');

                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Customer Selected",
                            showConfirmButton: false,
                            timer: 1800
                        });
                    }
                });
            });

            $('.fc-datepicker2').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'yy-mm-dd',
            });

            $('#services').on('change', function() {
                var val = $(this).val();
                var val_text = $("#services option:selected").text();
                var val_text_res = $.trim(val_text);
                
                $('#inquiry_type option').each(function() {
                    var get_text_res = $.trim($(this).text());
                    if (get_text_res == val_text_res) {
                        $(this).prop('selected', true);
                    }
                });
            });
        });

        function check_validation() {
            var errors = [];
            $('.is-invalid').removeClass('is-invalid');

            // 1. Customer Name
            var custName = $.trim($('#customer_name').val());
            if (custName.length < 1) {
                errors.push("Customer Name is required");
                $('#customer_name').addClass('is-invalid');
            }

            // 2. Customer Cell
            var custCell = $.trim($('#customer_cell').val());
            if (custCell.length < 1) {
                errors.push("Customer Cell / Contact is required");
                $('#customer_cell').addClass('is-invalid');
            }

            // 3. Services
            var servicesVal = $('#services').val();
            if (!servicesVal || servicesVal === "" || servicesVal === "Select Services") {
                errors.push("Please select a Service");
                $('#services').addClass('is-invalid');
            }

            // 4. Inquiry Type
            var inqTypeVal = $('#inquiry_type').val();
            if (!inqTypeVal || inqTypeVal === "" || inqTypeVal === "Select Inquiry Type") {
                errors.push("Please select an Inquiry Type");
                $('#inquiry_type').addClass('is-invalid');
            }

            // 5. Travel Date
            var travelDateVal = $.trim($('#travel_date').val());
            if (travelDateVal.length < 1) {
                errors.push("Travel Date is required");
                $('#travel_date').addClass('is-invalid');
            }

            // 6. Sales Person
            var salesPersonVal = $('#sale_person').val();
            if (!salesPersonVal || salesPersonVal === "" || salesPersonVal === "Select Sales Person") {
                errors.push("Please select a Sales Person");
                $('#sale_person').addClass('is-invalid');
            }

            // 7. Remarks
            saveQuillContent();
            var remarksHtml = $('#remarks').val();
            var strippedRemarks = remarksHtml.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, '').trim();

            if (strippedRemarks.length < 1) {
                errors.push("Remarks field is required");
                $('#editor-container').addClass('is-invalid border border-danger');
            } else {
                $('#editor-container').removeClass('is-invalid border border-danger');
            }

            if (errors.length > 0) {
                var errorHtml = '<ul class="text-start mb-0 ps-3">' + errors.map(function(e) { return '<li>' + e + '</li>'; }).join('') + '</ul>';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please Complete Required Fields',
                        html: errorHtml,
                        confirmButtonColor: '#0d6efd',
                        confirmButtonText: 'OK, I will fix it'
                    });
                } else {
                    alert("Please complete the following required fields:\n- " + errors.join("\n- "));
                }

                // Scroll to first invalid field
                var firstInvalid = $('.is-invalid').first();
                if (firstInvalid.length) {
                    firstInvalid.focus();
                    $('html, body').animate({
                        scrollTop: firstInvalid.offset().top - 120
                    }, 300);
                }
            } else {
                saveQuillContent();
                $('#update_submit_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Updating Inquiry...');
                document.getElementById('submit_inquiry').submit();
            }
        }
    </script>
@endpush

