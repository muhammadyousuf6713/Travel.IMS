@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header pb-0 bg-white">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Create Service</h5>
                        <a href="{{ url('services') }}" class="btn bg-gradient-secondary btn-sm mb-0 text-uppercase">
                            <i class="fa fa-arrow-left me-1"></i> Services List
                        </a>
                    </div>
                </div>
                <div class="card-body px-4 pt-4 pb-2">
                    <form id="myForm" method="post" action="{{ url('services/store') }}">
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

                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Service Details</h6>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-control-label">Service Name <span class="text-danger">*</span></label>
                                    <input type="text" name="service_name" class="form-control" value="{{ old('service_name') }}" placeholder="e.g. Flights, Visa, Hotel" required />
                                    @error('service_name')
                                        <span class="text-danger text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Status</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ url('services') }}" class="btn btn-light m-0 me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary m-0">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
