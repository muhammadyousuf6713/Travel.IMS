@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-3">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.15rem;">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Booking &bull; <span class="badge bg-dark">{{ $booking->crm_id }}</span>
            </h5>
            <span class="text-xs text-secondary">Update passenger details, flights, and accommodations</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pilgrimage.voucher.show', $booking->id) }}" target="_blank" class="btn btn-outline-success btn-sm mb-0">
                <i class="fa-solid fa-file-invoice me-1"></i> Voucher
            </a>
            <a href="{{ route('pilgrimage.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger text-white alert-dismissible fade show py-2 px-3 mb-3" role="alert">
            <span class="text-xs font-weight-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}</span>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger text-white alert-dismissible fade show py-2 px-3 mb-3" role="alert">
            <span class="text-xs font-weight-bold">Please correct the errors below:</span>
            <ul class="mb-0 text-xxs mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('pilgrimage.update', $booking->id) }}" id="bookingEditForm">
        @csrf
        @method('PUT')

        <!-- 1. Reference & Lead Passenger -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-id-card text-primary me-1"></i> 1. Booking Reference & Lead Passenger
                </span>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">CRM ID <span class="text-danger">*</span></label>
                        <input type="text" name="crm_id" class="form-control form-control-sm font-weight-bold text-primary" value="{{ old('crm_id', $booking->crm_id) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Service Category <span class="text-danger">*</span></label>
                        <select name="service_type" class="form-select form-select-sm" required>
                            <option value="umrah" {{ old('service_type', $booking->service_type) == 'umrah' ? 'selected' : '' }}>Umrah</option>
                            <option value="hajj" {{ old('service_type', $booking->service_type) == 'hajj' ? 'selected' : '' }}>Hajj</option>
                            <option value="tour" {{ old('service_type', $booking->service_type) == 'tour' ? 'selected' : '' }}>Group Tour</option>
                            <option value="corporate" {{ old('service_type', $booking->service_type) == 'corporate' ? 'selected' : '' }}>Corporate</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Submitting Agent</label>
                        <input type="text" class="form-control form-control-sm bg-light" value="{{ $booking->submittingAgent->name ?? 'Unassigned' }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Party Size</label>
                        <input type="text" class="form-control form-control-sm bg-light font-weight-bold" id="totalPaxDisplay" value="{{ $booking->total_persons }} Persons" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Lead Passenger Name <span class="text-danger">*</span></label>
                        <input type="text" name="leader_name" class="form-control form-control-sm" value="{{ old('leader_name', $booking->leader_name) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Passport No</label>
                        <input type="text" name="leader_passport_no" class="form-control form-control-sm" value="{{ old('leader_passport_no', $booking->leader_passport_no) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Contact Phone</label>
                        <input type="text" name="leader_contact_no" class="form-control form-control-sm" value="{{ old('leader_contact_no', $booking->leader_contact_no) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Special Notes</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="1">{{ old('notes', $booking->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Co-Travelers Repeater -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-users text-primary me-1"></i> 2. Co-Travelers
                </span>
                <button type="button" class="btn btn-xs btn-outline-primary mb-0" id="btnAddPilgrim">
                    <i class="fa-solid fa-plus me-1"></i> Add Traveler
                </button>
            </div>
            <div class="card-body p-3">
                <div id="pilgrimsContainer">
                    @foreach($booking->pilgrims as $idx => $pilgrim)
                        <div class="pilgrim-row card border border-radius-md p-2 mb-2 bg-light position-relative">
                            <button type="button" class="btn-close text-dark position-absolute top-0 end-0 m-2 btn-remove-row" style="font-size: 9px;"></button>
                            <div class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="pilgrims[{{ $idx }}][name]" class="form-control form-control-sm" value="{{ $pilgrim->name }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Passport No</label>
                                    <input type="text" name="pilgrims[{{ $idx }}][passport_no]" class="form-control form-control-sm" value="{{ $pilgrim->passport_no }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Gender</label>
                                    <select name="pilgrims[{{ $idx }}][gender]" class="form-select form-select-sm">
                                        <option value="Male" {{ $pilgrim->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $pilgrim->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Age Category</label>
                                    <select name="pilgrims[{{ $idx }}][age_type]" class="form-select form-select-sm">
                                        <option value="adult" {{ $pilgrim->age_type == 'adult' ? 'selected' : '' }}>Adult</option>
                                        <option value="child" {{ $pilgrim->age_type == 'child' ? 'selected' : '' }}>Child</option>
                                        <option value="infant" {{ $pilgrim->age_type == 'infant' ? 'selected' : '' }}>Infant</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 3. Flights Repeater -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-plane-departure text-primary me-1"></i> 3. Flight Segments
                </span>
                <button type="button" class="btn btn-xs btn-outline-primary mb-0" id="btnAddFlight">
                    <i class="fa-solid fa-plus me-1"></i> Add Flight
                </button>
            </div>
            <div class="card-body p-3">
                <div id="flightsContainer">
                    @foreach($booking->flights as $fIdx => $flight)
                        <div class="flight-row card border border-radius-md p-2 mb-2 bg-light position-relative">
                            <button type="button" class="btn-close text-dark position-absolute top-0 end-0 m-2 btn-remove-row" style="font-size: 9px;"></button>
                            <div class="row g-2 align-items-center">
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Flight No <span class="text-danger">*</span></label>
                                    <input type="text" name="flights[{{ $fIdx }}][flight_no]" class="form-control form-control-sm font-weight-bold" value="{{ $flight->flight_no }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Airline</label>
                                    <input type="text" name="flights[{{ $fIdx }}][airline]" class="form-control form-control-sm" value="{{ $flight->airline }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">From City <span class="text-danger">*</span></label>
                                    <input type="text" name="flights[{{ $fIdx }}][from_city]" class="form-control form-control-sm" value="{{ $flight->from_city }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">To City <span class="text-danger">*</span></label>
                                    <input type="text" name="flights[{{ $fIdx }}][to_city]" class="form-control form-control-sm" value="{{ $flight->to_city }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">ETD <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="flights[{{ $fIdx }}][etd]" class="form-control form-control-sm" value="{{ $flight->etd ? $flight->etd->format('Y-m-d\TH:i') : '' }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">PNR</label>
                                    <input type="text" name="flights[{{ $fIdx }}][pnr]" class="form-control form-control-sm" value="{{ $flight->pnr }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 4. Hotel Accommodations & Ziyaraat -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-hotel text-primary me-1"></i> 4. Hotel Accommodations & Ziyaraat
                </span>
                <button type="button" class="btn btn-xs btn-outline-primary mb-0" id="btnAddHotel">
                    <i class="fa-solid fa-plus me-1"></i> Add Hotel Stay
                </button>
            </div>
            <div class="card-body p-3">
                <div id="hotelsContainer">
                    @foreach($booking->hotels as $hIdx => $hotel)
                        <div class="hotel-row card border border-radius-md p-3 mb-2 bg-white shadow-none position-relative">
                            <button type="button" class="btn-close text-dark position-absolute top-0 end-0 m-2 btn-remove-row" style="font-size: 9px;"></button>
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">City <span class="text-danger">*</span></label>
                                    <select name="hotels[{{ $hIdx }}][city]" class="form-select form-select-sm hotel-city-select" required>
                                        <option value="Makkah" {{ $hotel->city == 'Makkah' ? 'selected' : '' }}>Makkah</option>
                                        <option value="Madinah" {{ $hotel->city == 'Madinah' ? 'selected' : '' }}>Madinah</option>
                                        <option value="Jeddah" {{ $hotel->city == 'Jeddah' ? 'selected' : '' }}>Jeddah</option>
                                        <option value="Riyadh" {{ $hotel->city == 'Riyadh' ? 'selected' : '' }}>Riyadh</option>
                                        <option value="Taif" {{ $hotel->city == 'Taif' ? 'selected' : '' }}>Taif</option>
                                        <option value="Other" {{ !in_array($hotel->city, ['Makkah', 'Madinah', 'Jeddah', 'Riyadh', 'Taif']) ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Hotel Name <span class="text-danger">*</span></label>
                                    <input type="text" name="hotels[{{ $hIdx }}][hotel_name]" class="form-control form-control-sm" value="{{ $hotel->hotel_name }}" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Voucher / Res Ref</label>
                                    <input type="text" name="hotels[{{ $hIdx }}][voucher_no]" class="form-control form-control-sm" value="{{ $hotel->voucher_no }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Room Type <span class="text-danger">*</span></label>
                                    <select name="hotels[{{ $hIdx }}][room_type]" class="form-select form-select-sm" required>
                                        <option value="Double" {{ $hotel->room_type == 'Double' ? 'selected' : '' }}>Double</option>
                                        <option value="Triple" {{ $hotel->room_type == 'Triple' ? 'selected' : '' }}>Triple</option>
                                        <option value="Quad" {{ $hotel->room_type == 'Quad' ? 'selected' : '' }}>Quad</option>
                                        <option value="Quint" {{ $hotel->room_type == 'Quint' ? 'selected' : '' }}>Quint</option>
                                        <option value="Hexa" {{ $hotel->room_type == 'Hexa' ? 'selected' : '' }}>Hexa</option>
                                        <option value="Penta" {{ $hotel->room_type == 'Penta' ? 'selected' : '' }}>Penta</option>
                                        <option value="Suite" {{ $hotel->room_type == 'Suite' ? 'selected' : '' }}>Suite</option>
                                        <option value="Sharing" {{ $hotel->room_type == 'Sharing' ? 'selected' : '' }}>Sharing</option>
                                    </select>
                                </div>

                                <div class="col-md-1">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Qty</label>
                                    <input type="number" name="hotels[{{ $hIdx }}][room_qty]" class="form-control form-control-sm text-center" value="{{ $hotel->room_qty }}" min="1" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Duration</label>
                                    <input type="text" class="form-control form-control-sm text-center bg-light hotel-nights-display" value="{{ $hotel->nights }} Nights" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-In <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="hotels[{{ $hIdx }}][check_in]" class="form-control form-control-sm hotel-checkin-input" value="{{ $hotel->check_in ? $hotel->check_in->format('Y-m-d\TH:i') : '' }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-Out <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="hotels[{{ $hIdx }}][check_out]" class="form-control form-control-sm hotel-checkout-input" value="{{ $hotel->check_out ? $hotel->check_out->format('Y-m-d\TH:i') : '' }}" required>
                                </div>

                                <div class="col-md-4 d-flex align-items-center mt-3">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="hotels[{{ $hIdx }}][schedule_ziyaraat]" value="1" id="ziyaraatToggleEdit{{ $hIdx }}" {{ $hotel->schedule_ziyaraat ? 'checked' : '' }}>
                                        <label class="form-check-label text-xs text-dark font-weight-semibold" for="ziyaraatToggleEdit{{ $hIdx }}">
                                            Auto-Schedule Ziyaraat (+48h)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="card shadow-xs border-0 border-radius-lg p-3 bg-light d-flex flex-row justify-content-between align-items-center">
            <a href="{{ route('pilgrimage.index') }}" class="btn btn-outline-secondary btn-sm mb-0">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 mb-0 shadow-sm">
                <i class="fa-solid fa-floppy-disk me-1"></i> Update Booking
            </button>
        </div>

    </form>
</div>
@endsection
