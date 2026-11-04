@extends('layouts.user_type.auth')

@section('content')
    <style>
        .iti.iti--allow-dropdown.iti--show-flags {
            width: 100%;
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header pb-0 bg-white">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Add New Customer</h5>
                        <a href="{{ url('customers') }}" class="btn bg-gradient-secondary btn-sm mb-0 text-uppercase">
                            <i class="fa fa-arrow-left me-1"></i> Customer List
                        </a>
                    </div>
                </div>
                <div class="card-body px-4 pt-4 pb-2">
                    <form method="post" enctype="multipart/form-data" action="{{ url('customers/store') }}">
                        @csrf
                        @if (count($errors) > 0)
                            <div class="alert alert-danger text-white alert-dismissible fade show" role="alert">
                                <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                <span class="alert-text"><strong>Error!</strong>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Customer Details</h6>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Customer Type</label>
                                    <select name="customer_type" class="form-control no-select2" required>
                                        <option value="Individual">Individual</option>
                                        <option value="Group">Group</option>
                                        <option value="Corporate">Corporate </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        WhatsApp <input type="checkbox" name="whatsapp_check" class="ms-1"> <br>
                                        Customer Cell <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" id="phone0" class="form-control" required name="customer_cell">
                                    <div class="invalid-feedback0"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Contact - WhatsApp</label>
                                    <input type="text" id="whatsapp_number" class="form-control" name="customer_whatsapp">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Contact - Other / PTCL</label>
                                    <input type="text" class="form-control" name="customer_phone_2">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-control-label">Customer Address</label>
                                    <input type="text" name="customer_address" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Customer Email</label>
                                    <input type="text" name="customer_email" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Customer Reference</label>
                                    <input type="text" class="form-control" name="customer_reference">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Remarks</label>
                                    <input type="text" class="form-control" name="customer_remarks">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Sales Person <span class="text-danger">*</span></label>
                                    <select name="sale_person" class="form-control" required>
                                        <option value="">Select</option>
                                        @forelse ($sale_persons as $sp)
                                            <option value="{{ $sp['id'] }}">{{ $sp['name'] }}</option>
                                        @empty
                                            <option value="">No Results Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="Verified">Verified</option>
                                        <option value="UnVerified">Un-verified</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Accounts Customer Rating</label>
                                    <input type="text" class="form-control" name="accounts_customer_rating">
                                </div>
                            </div>
                        </div>

                        <hr class="horizontal dark">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Country</label>
                                    <select name="country" class="form-control" id="country-dropdown">
                                        <option value="">Select Country</option>
                                        @forelse ($countries as $con)
                                            <option value="{{ $con->name }}">{{ $con->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">City</label>
                                    <select name="city" class="form-control ajax-city-select2" id="city-dropdown" data-placeholder="Select City">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Customer Image</label>
                            <input type="file" class="form-control" name="customer_image" />
                        </div>

                        <!-- Dynamic Fields Container -->
                        <div class="customer_records d-none"></div>
                        <div class="customer_records_dynamic"></div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn bg-gradient-primary btn-md">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('.extra-fields-customer').click(function() {
            $('.customer_records').clone().appendTo('.customer_records_dynamic');
            $('.customer_records_dynamic .customer_records').addClass('single remove');
            $('.single .extra-fields-customer').remove();
            $('.single').append(
                '<a href="#" class="remove-field btn-remove-customer btn btn-danger">Remove Fields</a>');
            $('.customer_records_dynamic > .single').attr("class", "remove");

            $('.customer_records_dynamic input').each(function() {
                var count = 0;
                var fieldname = $(this).attr("name");
                $(this).attr('name', fieldname + count);
                count++;
            });

        });

        $(document).on('click', '.remove-field', function(e) {
            $(this).parent('.remove').remove();
            e.preventDefault();
        });
        // Ajax to get City
        $(document).ready(function() {
            // Country dropdown is retained, but City dropdown is now an independent searchable AJAX dropdown.
        });
    </script>

    <script>
    // Get Already Customer
        $("#phone0").on("keyup change", function(e) {
            let val = $(this).val();

            $.ajax({
                url: "{{ url('check_customer_number') }}/" + val,
                type: "GET",
                data: {
                    number: val
                },
                dataType: 'json',
                success: function(result) {
                    if (result.getCell == true) {
                        alert('Customer already exists!');

                    }
                }
            });
        })
    </script>
@endpush
