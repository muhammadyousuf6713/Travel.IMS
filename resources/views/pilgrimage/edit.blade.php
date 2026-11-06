@extends('layouts.user_type.auth')

@section('content')
<style>
.custom-toggle-switch {
    position: relative;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    margin-bottom: 0;
}
.custom-toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}
.custom-toggle-switch .toggle-slider {
    position: relative;
    display: inline-block;
    width: 42px;
    height: 22px;
    background-color: #cbd5e1;
    border-radius: 22px;
    transition: background-color 0.25s ease, box-shadow 0.25s ease;
    flex-shrink: 0;
}
.custom-toggle-switch .toggle-slider::after {
    content: "";
    position: absolute;
    top: 2px;
    left: 2px;
    width: 18px;
    height: 18px;
    background-color: #ffffff;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.custom-toggle-switch input:checked + .toggle-slider {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
}
.custom-toggle-switch input:checked + .toggle-slider::after {
    transform: translateX(20px);
}
.custom-toggle-switch .toggle-label {
    margin-left: 8px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #2d3748;
    cursor: pointer;
}
.hotel-row, .flight-row, .pilgrim-row, .custom-leg-row {
    transition: all 0.2s ease-in-out;
}
.hotel-row:hover, .flight-row:hover, .pilgrim-row:hover, .custom-leg-row:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.06) !important;
}
</style>

<div class="container-fluid py-3">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <div>
            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.15rem;">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Booking Ref: <span class="badge bg-dark">{{ $booking->crm_id }}</span>
            </h5>
            <span class="text-xs text-secondary">Update passenger details, flights, and accommodations for pilgrimage & customized itineraries</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('pilgrimage.voucher.show', $booking->id) }}" target="_blank" class="btn btn-outline-success btn-sm mb-0">
                <i class="fa-solid fa-file-invoice me-1"></i> Voucher
            </a>
            <a href="{{ route('pilgrimage.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Bookings
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
        <!-- Hidden CRM ID -->
        <input type="hidden" name="crm_id" value="{{ $booking->crm_id }}">

        <!-- 1. Reference & Lead Passenger -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-id-card text-primary me-1"></i> 1. Booking Information & Lead Passenger
                </span>
                <span class="text-xxs text-secondary">Reference: <strong class="text-dark">{{ $booking->crm_id }}</strong></span>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Service Category <span class="text-danger">*</span></label>
                        <select name="service_type" class="form-select form-select-sm" required>
                            <option value="umrah" {{ old('service_type', $booking->service_type) == 'umrah' ? 'selected' : '' }}>Umrah Package</option>
                            <option value="hajj" {{ old('service_type', $booking->service_type) == 'hajj' ? 'selected' : '' }}>Hajj Pilgrimage</option>
                            <option value="tour" {{ old('service_type', $booking->service_type) == 'tour' ? 'selected' : '' }}>Group / Custom Tour</option>
                            <option value="corporate" {{ old('service_type', $booking->service_type) == 'corporate' ? 'selected' : '' }}>Corporate Travel</option>
                            <option value="ticketing" {{ old('service_type', $booking->service_type) == 'ticketing' ? 'selected' : '' }}>Flight Ticketing & Hotel</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Sales Person / Agent</label>
                        @if($isBossOrAdmin)
                            <select name="submitting_agent_id" class="form-select form-select-sm">
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('submitting_agent_id', $booking->submitting_agent_id) == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control form-control-sm bg-light" value="{{ $booking->submittingAgent->name ?? Auth::user()->name }}" readonly>
                        @endif
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Lead Passenger Name <span class="text-danger">*</span></label>
                        <input type="text" name="leader_name" class="form-control form-control-sm font-weight-bold" value="{{ old('leader_name', $booking->leader_name) }}" required placeholder="e.g. Muhammad Yousuf">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Total Travelers (PAX)</label>
                        <input type="text" id="totalPaxDisplay" class="form-control form-control-sm bg-light font-weight-bold text-primary" readonly value="{{ count($booking->pilgrims) + 1 }} Persons">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Passport No <span class="text-muted text-xxs font-weight-normal">(Optional)</span></label>
                        <input type="text" name="leader_passport_no" class="form-control form-control-sm" value="{{ old('leader_passport_no', $booking->leader_passport_no) }}" placeholder="e.g. AB1234567">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Contact Phone / WhatsApp <span class="text-muted text-xxs font-weight-normal">(Optional)</span></label>
                        <input type="text" name="leader_contact_no" class="form-control form-control-sm" value="{{ old('leader_contact_no', $booking->leader_contact_no) }}" placeholder="+92 300 1234567">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Notes / Instructions</label>
                        <input type="text" name="notes" class="form-control form-control-sm" value="{{ old('notes', $booking->notes) }}" placeholder="Special VIP requests...">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Co-Travelers Repeater -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-users text-primary me-1"></i> 2. Co-Travelers & Family Members
                </span>
                <button type="button" class="btn btn-xs btn-outline-primary mb-0" id="btnAddPilgrim">
                    <i class="fa-solid fa-user-plus me-1"></i> Add Co-Traveler
                </button>
            </div>
            <div class="card-body p-3">
                <div id="pilgrimsContainer">
                    @foreach($booking->pilgrims as $pIdx => $p)
                        <div class="pilgrim-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border pilgrim-badge">
                                    <i class="fa-solid fa-user-plus text-primary me-1"></i> Co-Traveler #{{ $loop->iteration }}
                                </span>
                                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Traveler">
                                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                                </button>
                            </div>
                            <div class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="pilgrims[{{ $pIdx }}][name]" class="form-control form-control-sm pilgrim-name-input" value="{{ $p->name }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Passport No</label>
                                    <input type="text" name="pilgrims[{{ $pIdx }}][passport_no]" class="form-control form-control-sm" value="{{ $p->passport_no }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Gender</label>
                                    <select name="pilgrims[{{ $pIdx }}][gender]" class="form-select form-select-sm">
                                        <option value="Male" {{ $p->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $p->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Age Category</label>
                                    <select name="pilgrims[{{ $pIdx }}][age_type]" class="form-select form-select-sm">
                                        <option value="adult" {{ $p->age_type == 'adult' ? 'selected' : '' }}>Adult</option>
                                        <option value="child" {{ $p->age_type == 'child' ? 'selected' : '' }}>Child</option>
                                        <option value="infant" {{ $p->age_type == 'infant' ? 'selected' : '' }}>Infant</option>
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
                        <div class="flight-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border flight-badge">
                                    <i class="fa-solid fa-plane-departure text-primary me-1"></i> Flight Segment #{{ $loop->iteration }}
                                </span>
                                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Flight">
                                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                                </button>
                            </div>
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
                                    <input type="text" name="flights[{{ $fIdx }}][from_city]" class="form-control form-control-sm" list="citiesDatalist" value="{{ $flight->from_city }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">To City <span class="text-danger">*</span></label>
                                    <input type="text" name="flights[{{ $fIdx }}][to_city]" class="form-control form-control-sm" list="citiesDatalist" value="{{ $flight->to_city }}" required>
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

        <!-- 4. Hotel Accommodations & Stays -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-hotel text-primary me-1"></i> 4. Hotel Accommodations & Stays
                </span>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('hotels.index') }}" target="_blank" class="btn btn-xs btn-outline-secondary mb-0">
                        <i class="fa-solid fa-gear me-1"></i> Manage Hotels
                    </a>
                    <button type="button" class="btn btn-xs btn-outline-primary mb-0" id="btnAddHotel">
                        <i class="fa-solid fa-plus me-1"></i> Add Hotel Stay
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <div id="hotelsContainer">
                    @foreach($booking->hotels as $hIdx => $hotel)
                        <div class="hotel-row card border border-radius-md p-3 mb-3 bg-white shadow-none position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <span class="badge bg-light text-dark text-xxs font-weight-bolder text-uppercase border hotel-badge">
                                    <i class="fa-solid fa-hotel text-primary me-1"></i> Hotel Stay #{{ $loop->iteration }}
                                </span>
                                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Hotel">
                                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                                </button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">City <span class="text-danger">*</span></label>
                                    <input type="text" name="hotels[{{ $hIdx }}][city]" class="form-control form-control-sm hotel-city-input" list="citiesDatalist" value="{{ $hotel->city }}" placeholder="Search City..." required>
                                </div>

                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label text-xxs font-weight-bold text-secondary mb-0">Hotel Name <span class="text-danger">*</span></label>
                                        <button type="button" class="btn btn-link text-primary p-0 m-0 text-xxs btn-quick-add-hotel" title="Quick add hotel">
                                            <i class="fa-solid fa-plus-circle me-1"></i> New Hotel
                                        </button>
                                    </div>
                                    <select name="hotels[{{ $hIdx }}][hotel_name]" class="form-select form-select-sm hotel-name-select" data-selected="{{ $hotel->hotel_name }}" required>
                                        <option value="{{ $hotel->hotel_name }}" selected>{{ $hotel->hotel_name }}</option>
                                        <option value="__CUSTOM__">+ Add New Hotel...</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Voucher / Res Ref</label>
                                    <input type="text" name="hotels[{{ $hIdx }}][voucher_no]" class="form-control form-control-sm" value="{{ $hotel->voucher_no }}" placeholder="Res Ref">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Room Type <span class="text-danger">*</span></label>
                                    <select name="hotels[{{ $hIdx }}][room_type]" class="form-select form-select-sm" required>
                                        <option value="Sharing" {{ $hotel->room_type == 'Sharing' ? 'selected' : '' }}>Sharing Bed</option>
                                        <option value="Quint" {{ $hotel->room_type == 'Quint' ? 'selected' : '' }}>Quint (5 Bed)</option>
                                        <option value="Quad" {{ $hotel->room_type == 'Quad' ? 'selected' : '' }}>Quad (4 Bed)</option>
                                        <option value="Triple" {{ $hotel->room_type == 'Triple' ? 'selected' : '' }}>Triple (3 Bed)</option>
                                        <option value="Double" {{ $hotel->room_type == 'Double' ? 'selected' : '' }}>Double (2 Bed)</option>
                                        <option value="Single" {{ $hotel->room_type == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Hexa" {{ $hotel->room_type == 'Hexa' ? 'selected' : '' }}>Hexa (6 Bed)</option>
                                        <option value="Penta" {{ $hotel->room_type == 'Penta' ? 'selected' : '' }}>Penta</option>
                                        <option value="Suite" {{ $hotel->room_type == 'Suite' ? 'selected' : '' }}>Executive Suite</option>
                                    </select>
                                </div>

                                <div class="col-md-1">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Qty</label>
                                    <input type="number" name="hotels[{{ $hIdx }}][room_qty]" class="form-control form-control-sm text-center" value="{{ $hotel->room_qty }}" min="1" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-In <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="hotels[{{ $hIdx }}][check_in]" class="form-control form-control-sm hotel-checkin-input" value="{{ $hotel->check_in ? $hotel->check_in->format('Y-m-d\TH:i') : '' }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-Out <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="hotels[{{ $hIdx }}][check_out]" class="form-control form-control-sm hotel-checkout-input" value="{{ $hotel->check_out ? $hotel->check_out->format('Y-m-d\TH:i') : '' }}" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Duration</label>
                                    <input type="text" class="form-control form-control-sm text-center bg-light hotel-nights-display" value="{{ $hotel->nights }} Nights" readonly>
                                </div>

                                <div class="col-md-2 d-flex align-items-center mt-3">
                                    <label class="custom-toggle-switch">
                                        <input type="checkbox" name="hotels[{{ $hIdx }}][schedule_ziyaraat]" value="1" {{ $hotel->schedule_ziyaraat ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                        <span class="toggle-label text-xxs">Ziyaraat (+48h)</span>
                                    </label>
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

<!-- Global Cities Datalist -->
<datalist id="citiesDatalist">
    <option value="Makkah, Saudi Arabia">
    <option value="Madinah, Saudi Arabia">
    <option value="Jeddah, Saudi Arabia">
    <option value="Riyadh, Saudi Arabia">
    <option value="Taif, Saudi Arabia">
    <option value="Dubai, United Arab Emirates">
    <option value="Istanbul, Turkey">
    <option value="Cairo, Egypt">
    <option value="Karachi, Pakistan">
    <option value="Lahore, Pakistan">
    <option value="Islamabad, Pakistan">
    <option value="London, United Kingdom">
    <option value="Doha, Qatar">
    <option value="Muscat, Oman">
    <option value="Amman, Jordan">
    <option value="Baghdad, Iraq">
    <option value="Najaf, Iraq">
</datalist>

<!-- Quick Add Hotel Modal -->
<div class="modal fade" id="quickAddHotelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-light border-bottom py-2 px-3">
                <h6 class="modal-title font-weight-bold text-dark mb-0">
                    <i class="fa-solid fa-hotel text-primary me-1"></i> Quick Add New Hotel
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddHotelForm">
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label text-xs font-weight-bold mb-1">Hotel Name <span class="text-danger">*</span></label>
                        <input type="text" id="quick_hotel_name" class="form-control form-control-sm" placeholder="e.g. Pullman Zamzam / Dar Al Taqwa" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label text-xs font-weight-bold mb-1">City <span class="text-danger">*</span></label>
                            <input type="text" id="quick_hotel_city" class="form-control form-control-sm" list="citiesDatalist" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs font-weight-bold mb-1">Rating</label>
                            <select id="quick_hotel_rating" class="form-select form-select-sm">
                                <option value="5 Star">5 Star</option>
                                <option value="4 Star">4 Star</option>
                                <option value="3 Star" selected>3 Star</option>
                                <option value="2 Star">2 Star</option>
                                <option value="Economy">Economy</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-xs font-weight-bold mb-1">Haram / Center Distance</label>
                        <input type="text" id="quick_hotel_distance" class="form-control form-control-sm" placeholder="e.g. 150m from Haram courtyard / Clock Tower">
                    </div>
                    <div class="mb-1">
                        <label class="form-label text-xs font-weight-bold mb-1">Address / Landmark</label>
                        <input type="text" id="quick_hotel_address" class="form-control form-control-sm" placeholder="Street or Area">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm mb-0 shadow-sm" id="btnSaveQuickHotel">
                        <i class="fa-solid fa-save me-1"></i> Save & Use Hotel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pilgrimsContainer = document.getElementById('pilgrimsContainer');
    const flightsContainer = document.getElementById('flightsContainer');
    const hotelsContainer = document.getElementById('hotelsContainer');
    const totalPaxDisplay = document.getElementById('totalPaxDisplay');
    let activeHotelSelectForQuickAdd = null;

    // --- SEQUENTIAL RENUMBERING ON ADD & DELETE ---
    function renumberTravelers() {
        const rows = document.querySelectorAll('.pilgrim-row');
        rows.forEach((row, idx) => {
            const seq = idx + 2; // Co-Traveler #2, #3, etc. (Lead is #1)
            const badge = row.querySelector('.pilgrim-badge');
            if (badge) {
                badge.innerHTML = `<i class="fa-solid fa-user-plus text-primary me-1"></i> Co-Traveler #${seq}`;
            }
            row.querySelectorAll('input, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/pilgrims\[\d+\]/, `pilgrims[${idx}]`);
                }
            });
        });
        updatePaxCount();
    }

    function renumberFlights() {
        const rows = document.querySelectorAll('.flight-row');
        rows.forEach((row, idx) => {
            const seq = idx + 1; // Flight Segment #1, #2...
            const badge = row.querySelector('.flight-badge');
            if (badge) {
                badge.innerHTML = `<i class="fa-solid fa-plane-departure text-primary me-1"></i> Flight Segment #${seq}`;
            }
            row.querySelectorAll('input, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/flights\[\d+\]/, `flights[${idx}]`);
                }
            });
        });
    }

    function renumberHotels() {
        const rows = document.querySelectorAll('.hotel-row');
        rows.forEach((row, idx) => {
            const seq = idx + 1; // Hotel Stay #1, #2...
            const badge = row.querySelector('.hotel-badge');
            if (badge) {
                badge.innerHTML = `<i class="fa-solid fa-hotel text-primary me-1"></i> Hotel Stay #${seq}`;
            }
            row.querySelectorAll('input, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/hotels\[\d+\]/, `hotels[${idx}]`);
                }
            });
        });
    }

    function updatePaxCount() {
        const count = 1 + document.querySelectorAll('.pilgrim-row').length;
        if (totalPaxDisplay) {
            totalPaxDisplay.value = `${count} ${count === 1 ? 'Person (Lead Passenger)' : 'Persons (1 Lead + ' + (count - 1) + ' Co-Travelers)'}`;
        }
    }

    // --- ADD TRAVELER ---
    document.getElementById('btnAddPilgrim').addEventListener('click', function () {
        const newIdx = document.querySelectorAll('.pilgrim-row').length;
        const seq = newIdx + 2;
        const row = document.createElement('div');
        row.className = 'pilgrim-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border pilgrim-badge">
                    <i class="fa-solid fa-user-plus text-primary me-1"></i> Co-Traveler #${seq}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Traveler">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="pilgrims[${newIdx}][name]" class="form-control form-control-sm pilgrim-name-input" placeholder="Full Name" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Passport No</label>
                    <input type="text" name="pilgrims[${newIdx}][passport_no]" class="form-control form-control-sm" placeholder="Passport No">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Gender</label>
                    <select name="pilgrims[${newIdx}][gender]" class="form-select form-select-sm">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Age Category</label>
                    <select name="pilgrims[${newIdx}][age_type]" class="form-select form-select-sm">
                        <option value="adult" selected>Adult</option>
                        <option value="child">Child</option>
                        <option value="infant">Infant</option>
                    </select>
                </div>
            </div>
        `;
        pilgrimsContainer.appendChild(row);
        renumberTravelers();
    });

    // --- ADD FLIGHT ---
    document.getElementById('btnAddFlight').addEventListener('click', function () {
        const newIdx = document.querySelectorAll('.flight-row').length;
        const seq = newIdx + 1;
        const row = document.createElement('div');
        row.className = 'flight-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border flight-badge">
                    <i class="fa-solid fa-plane-departure text-primary me-1"></i> Flight Segment #${seq}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Flight">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
            <div class="row g-2 align-items-center">
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Flight No <span class="text-danger">*</span></label>
                    <input type="text" name="flights[${newIdx}][flight_no]" class="form-control form-control-sm font-weight-bold" placeholder="SV-702" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Airline</label>
                    <input type="text" name="flights[${newIdx}][airline]" class="form-control form-control-sm" placeholder="Saudia">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">From City <span class="text-danger">*</span></label>
                    <input type="text" name="flights[${newIdx}][from_city]" class="form-control form-control-sm" list="citiesDatalist" placeholder="Madinah (MED)" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">To City <span class="text-danger">*</span></label>
                    <input type="text" name="flights[${newIdx}][to_city]" class="form-control form-control-sm" list="citiesDatalist" placeholder="Karachi (KHI)" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">ETD <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="flights[${newIdx}][etd]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">PNR</label>
                    <input type="text" name="flights[${newIdx}][pnr]" class="form-control form-control-sm" placeholder="PNR Ref">
                </div>
            </div>
        `;
        flightsContainer.appendChild(row);
        renumberFlights();
    });

    // --- ADD HOTEL ---
    document.getElementById('btnAddHotel').addEventListener('click', function () {
        const newIdx = document.querySelectorAll('.hotel-row').length;
        const seq = newIdx + 1;
        const row = document.createElement('div');
        row.className = 'hotel-row card border border-radius-md p-3 mb-3 bg-white shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-light text-dark text-xxs font-weight-bolder text-uppercase border hotel-badge">
                    <i class="fa-solid fa-hotel text-primary me-1"></i> Hotel Stay #${seq}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Hotel">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">City <span class="text-danger">*</span></label>
                    <input type="text" name="hotels[${newIdx}][city]" class="form-control form-control-sm hotel-city-input" list="citiesDatalist" value="Madinah" placeholder="Search City..." required>
                </div>

                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-xxs font-weight-bold text-secondary mb-0">Hotel Name <span class="text-danger">*</span></label>
                        <button type="button" class="btn btn-link text-primary p-0 m-0 text-xxs btn-quick-add-hotel" title="Quick add hotel">
                            <i class="fa-solid fa-plus-circle me-1"></i> New Hotel
                        </button>
                    </div>
                    <select name="hotels[${newIdx}][hotel_name]" class="form-select form-select-sm hotel-name-select" required>
                        <option value="">Select or Type City First...</option>
                        <option value="__CUSTOM__">+ Add New Hotel...</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Voucher / Res Ref</label>
                    <input type="text" name="hotels[${newIdx}][voucher_no]" class="form-control form-control-sm" placeholder="Res Ref">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Room Type <span class="text-danger">*</span></label>
                    <select name="hotels[${newIdx}][room_type]" class="form-select form-select-sm" required>
                        <option value="Sharing">Sharing Bed</option>
                        <option value="Quint">Quint (5 Bed)</option>
                        <option value="Quad">Quad (4 Bed)</option>
                        <option value="Triple">Triple (3 Bed)</option>
                        <option value="Double" selected>Double (2 Bed)</option>
                        <option value="Single">Single</option>
                        <option value="Hexa">Hexa (6 Bed)</option>
                        <option value="Penta">Penta</option>
                        <option value="Suite">Executive Suite</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Qty</label>
                    <input type="number" name="hotels[${newIdx}][room_qty]" class="form-control form-control-sm text-center" value="1" min="1" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-In <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="hotels[${newIdx}][check_in]" class="form-control form-control-sm hotel-checkin-input" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-Out <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="hotels[${newIdx}][check_out]" class="form-control form-control-sm hotel-checkout-input" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Duration</label>
                    <input type="text" class="form-control form-control-sm text-center bg-light hotel-nights-display" value="—" readonly>
                </div>

                <div class="col-md-2 d-flex align-items-center mt-3">
                    <label class="custom-toggle-switch">
                        <input type="checkbox" name="hotels[${newIdx}][schedule_ziyaraat]" value="1" checked>
                        <span class="toggle-slider"></span>
                        <span class="toggle-label text-xxs">Ziyaraat (+48h)</span>
                    </label>
                </div>
            </div>
        `;
        hotelsContainer.appendChild(row);
        attachDateChangeListeners(row);
        loadHotelsForCity(row);
        renumberHotels();
    });

    // --- DYNAMIC HOTELS FETCH BY CITY ---
    function loadHotelsForCity(row, preferredSelection = null) {
        const cityInput = row.querySelector('.hotel-city-input');
        const hotelSelect = row.querySelector('.hotel-name-select');
        if (!cityInput || !hotelSelect) return;

        const city = cityInput.value.trim();
        const currentVal = preferredSelection || hotelSelect.dataset.selected || hotelSelect.value;

        fetch(`{{ url('api/hotels-by-city') }}?city=${encodeURIComponent(city)}`)
            .then(res => res.json())
            .then(hotels => {
                hotelSelect.innerHTML = '';
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = hotels.length ? '-- Select Hotel from Database --' : '-- No hotels listed for this city --';
                hotelSelect.appendChild(defaultOpt);

                let matched = false;
                hotels.forEach(h => {
                    const opt = document.createElement('option');
                    opt.value = h.name;
                    opt.textContent = `${h.name} (${h.star_rating || 'Hotel'}${h.distance ? ' - ' + h.distance : ''})`;
                    if (currentVal && (currentVal.toLowerCase() === h.name.toLowerCase())) {
                        opt.selected = true;
                        matched = true;
                    }
                    hotelSelect.appendChild(opt);
                });

                if (currentVal && !matched && currentVal !== '__CUSTOM__') {
                    const customOpt = document.createElement('option');
                    customOpt.value = currentVal;
                    customOpt.textContent = `${currentVal} (Saved)`;
                    customOpt.selected = true;
                    hotelSelect.appendChild(customOpt);
                }

                const addOpt = document.createElement('option');
                addOpt.value = '__CUSTOM__';
                addOpt.textContent = '+ Add New Hotel to Database...';
                hotelSelect.appendChild(addOpt);
            })
            .catch(() => {});
    }

    // City change event
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('hotel-city-input')) {
            const row = e.target.closest('.hotel-row');
            if (row) loadHotelsForCity(row);
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('hotel-city-input')) {
            const row = e.target.closest('.hotel-row');
            if (row) loadHotelsForCity(row);
        }
        if (e.target.classList.contains('hotel-name-select')) {
            if (e.target.value === '__CUSTOM__') {
                const row = e.target.closest('.hotel-row');
                activeHotelSelectForQuickAdd = e.target;
                openQuickAddHotelModal(row);
            }
        }
    });

    // Quick Add Hotel Modal Open
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.btn-quick-add-hotel');
        if (trigger) {
            const row = trigger.closest('.hotel-row');
            if (row) {
                activeHotelSelectForQuickAdd = row.querySelector('.hotel-name-select');
                openQuickAddHotelModal(row);
            }
        }
    });

    function openQuickAddHotelModal(row) {
        const cityInput = row ? row.querySelector('.hotel-city-input') : null;
        const currentCity = cityInput ? cityInput.value.trim() : 'Makkah';

        document.getElementById('quick_hotel_city').value = currentCity;
        document.getElementById('quick_hotel_name').value = '';
        document.getElementById('quick_hotel_distance').value = '';
        document.getElementById('quick_hotel_address').value = '';

        const modalEl = document.getElementById('quickAddHotelModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    // Quick Add Hotel Form Submission
    document.getElementById('quickAddHotelForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveQuickHotel');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';

        const name = document.getElementById('quick_hotel_name').value.trim();
        const city = document.getElementById('quick_hotel_city').value.trim();
        const rating = document.getElementById('quick_hotel_rating').value;
        const distance = document.getElementById('quick_hotel_distance').value.trim();
        const address = document.getElementById('quick_hotel_address').value.trim();

        fetch("{{ route('hotels.quick_store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: name,
                city: city,
                star_rating: rating,
                distance: distance,
                address: address
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Save & Use Hotel';

            if (data.success && data.hotel) {
                const modalEl = document.getElementById('quickAddHotelModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                if (activeHotelSelectForQuickAdd) {
                    const label = `${data.hotel.name} (${data.hotel.star_rating || 'Standard'}${data.hotel.distance ? ' - ' + data.hotel.distance : ''})`;
                    const newOpt = new Option(label, data.hotel.name, true, true);
                    const lastOpt = activeHotelSelectForQuickAdd.querySelector('option[value="__CUSTOM__"]');
                    if (lastOpt) {
                        activeHotelSelectForQuickAdd.insertBefore(newOpt, lastOpt);
                    } else {
                        activeHotelSelectForQuickAdd.appendChild(newOpt);
                    }
                    activeHotelSelectForQuickAdd.value = data.hotel.name;
                }
            } else {
                alert('Could not save hotel: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Save & Use Hotel';
            alert('Error adding hotel: ' + err.message);
        });
    });

    // --- REMOVE ROW WITH SEQUENTIAL RENUMBERING ---
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-remove-row');
        if (btn) {
            const card = btn.closest('.pilgrim-row, .flight-row, .hotel-row');
            if (card) {
                const isPilgrim = card.classList.contains('pilgrim-row');
                const isFlight = card.classList.contains('flight-row');
                const isHotel = card.classList.contains('hotel-row');

                card.remove();

                if (isPilgrim) renumberTravelers();
                if (isFlight) renumberFlights();
                if (isHotel) renumberHotels();
            }
        }
    });

    // --- NIGHTS CALCULATION ---
    function attachDateChangeListeners(parent) {
        const inInput = parent.querySelector('.hotel-checkin-input');
        const outInput = parent.querySelector('.hotel-checkout-input');
        const display = parent.querySelector('.hotel-nights-display');

        function recalc() {
            if (inInput && outInput && inInput.value && outInput.value) {
                const d1 = new Date(inInput.value);
                const d2 = new Date(outInput.value);
                const diffTime = d2 - d1;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                display.value = `${diffDays > 0 ? diffDays : 1} ${diffDays === 1 ? 'Night' : 'Nights'}`;
            }
        }

        if (inInput && outInput) {
            inInput.addEventListener('change', recalc);
            outInput.addEventListener('change', recalc);
        }
    }

    // Initial setup
    document.querySelectorAll('.hotel-row').forEach(row => {
        attachDateChangeListeners(row);
        loadHotelsForCity(row);
    });

    renumberTravelers();
    renumberFlights();
    renumberHotels();
});
</script>
@endsection