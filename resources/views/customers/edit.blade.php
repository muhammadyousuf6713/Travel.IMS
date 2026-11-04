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
                    <h5 class="mb-0 fw-bold">Edit Customer</h5>
                    <a href="{{ url('customers') }}" class="btn bg-gradient-secondary btn-sm mb-0 text-uppercase">
                        <i class="fa fa-arrow-left me-1"></i> Customer List
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pt-4 pb-2">
                <form method="post" enctype="multipart/form-data" action="{{ route('customers.update', $customer->id_customers) }}">
                    @csrf
                    @method('PUT')

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
                                <input type="text" name="customer_name" class="form-control"
                                       value="{{ old('customer_name', $customer->customer_name) }}" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Customer Type</label>
                                <select name="customer_type" class="form-control no-select2" required>
                                    <option value="Individual" {{ $customer->customer_type == 'Individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="Group" {{ $customer->customer_type == 'Group' ? 'selected' : '' }}>Group</option>
                                    <option value="Corporate" {{ $customer->customer_type == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    WhatsApp <input type="checkbox" name="whatsapp_check" class="ms-1" {{ $customer->whatsapp_check ? 'checked' : '' }}>
                                    <br> Customer Cell <span class="text-danger">*</span>
                                </label>
                                <input type="tel" id="phone0" class="form-control" required
                                       name="customer_cell" value="{{ old('customer_cell', $customer->customer_cell) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Contact - WhatsApp</label>
                                <input type="text" id="whatsapp_number" class="form-control"
                                       name="customer_whatsapp" value="{{ old('customer_whatsapp', $customer->whatsapp_number) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Contact - Other / PTCL</label>
                                <input type="text" class="form-control" name="customer_phone_2"
                                       value="{{ old('customer_phone_2', $customer->customer_phone2) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-control-label">Customer Address</label>
                                <input type="text" name="customer_address" class="form-control"
                                       value="{{ old('customer_address', $customer->customer_address) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Customer Email</label>
                                <input type="email" name="customer_email" class="form-control"
                                       value="{{ old('customer_email', $customer->customer_email) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Customer Reference</label>
                                <input type="text" class="form-control" name="customer_reference"
                                       value="{{ old('customer_reference', $customer->customer_reference) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Remarks</label>
                                <input type="text" class="form-control" name="customer_remarks"
                                       value="{{ old('customer_remarks', $customer->customer_remarks) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Sales Person <span class="text-danger">*</span></label>
                                <select name="sale_person" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($sale_persons as $sp)
                                        <option value="{{ $sp['id'] }}"
                                            {{ $customer->sale_person == $sp['id'] ? 'selected' : '' }}>
                                            {{ $sp['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="Verified" {{ $customer->status == 'Verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="UnVerified" {{ $customer->status == 'UnVerified' ? 'selected' : '' }}>Un-verified</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Accounts Customer Rating</label>
                                <input type="text" class="form-control" name="accounts_customer_rating"
                                       value="{{ old('accounts_customer_rating', $customer->accounts_customer_rating) }}">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="horizontal dark">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Country</label>
                                <select name="country" class="form-control" id="country-dropdown">
                                    <option>Select Country</option>
                                    @foreach ($countries as $con)
                                        <option value="{{ $con->name }}"
                                            {{ $customer->country == $con->name ? 'selected' : '' }}>
                                            {{ $con->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">City</label>
                                <select name="city" class="form-control ajax-city-select2" id="city-dropdown" data-placeholder="Select City">
                                    @if(isset($customer) && $customer->city_id)
                                        <option value="{{ $customer->city_id }}" selected>
                                            {{ $customer->city_id }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-control-label">Customer Image</label>
                        @if($customer->customer_image && $customer->customer_image !== 'default_user' && file_exists(public_path('uploads/customer_images/' . $customer->customer_image)))
                            <div class="mb-2">
                                <img src="{{ asset('uploads/customer_images/'.$customer->customer_image) }}"
                                     alt="Customer Image" style="max-width: 140px; border-radius: 8px;">
                                <br>
                                <a href="#" class="text-danger small" onclick="document.getElementById('image-delete').value = '1'; this.style.display='none'; return false;">
                                    <i class="fa fa-trash me-1"></i> Remove current image
                                </a>
                                <input type="hidden" name="remove_image" id="image-delete" value="0">
                            </div>
                        @endif
                        <input type="file" class="form-control" name="customer_image">
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn bg-gradient-primary btn-md">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize country-city dropdown
    // Existing customer check
    $("#phone0").on("keyup change", function(e) {
        let val = $(this).val();
        if(val !== "{{ $customer->customer_cell }}") {
            $.ajax({
                url: "{{ url('check_customer_number') }}/" + val,
                type: "GET",
                dataType: 'json',
                success: function(result) {
                    if(result.getCell) {
                        alert('This number is already registered to another customer!');
                    }
                }
            });
        }
    });
</script>
@endpush
