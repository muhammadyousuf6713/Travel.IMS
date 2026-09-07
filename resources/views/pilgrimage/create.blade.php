@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-3">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.15rem;">
                <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> New Booking Intake
            </h5>
            <span class="text-xs text-secondary">Record traveler details, flights, and accommodations</span>
        </div>
        <a href="{{ route('pilgrimage.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
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

    <form method="POST" action="{{ route('pilgrimage.store') }}" id="bookingIntakeForm">
        @csrf

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
                        <input type="text" name="crm_id" class="form-control form-control-sm font-weight-bold text-primary" value="{{ old('crm_id', $suggestedCrmId) }}" required placeholder="e.g. CRM-10234">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Service Category <span class="text-danger">*</span></label>
                        <select name="service_type" class="form-select form-select-sm" required>
                            <option value="umrah" {{ old('service_type') == 'umrah' ? 'selected' : '' }}>Umrah</option>
                            <option value="hajj" {{ old('service_type') == 'hajj' ? 'selected' : '' }}>Hajj</option>
                            <option value="tour" {{ old('service_type') == 'tour' ? 'selected' : '' }}>Group Tour</option>
                            <option value="corporate" {{ old('service_type') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Submitting Agent <span class="text-danger">*</span></label>
                        @if($isBossOrAdmin)
                            <select name="submitting_agent_id" class="form-select form-select-sm" required>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ (old('submitting_agent_id', Auth::id()) == $agent->id) ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control form-control-sm bg-light" value="{{ Auth::user()->name }}" readonly>
                            <input type="hidden" name="submitting_agent_id" value="{{ Auth::id() }}">
                        @endif
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Total Party Size</label>
                        <input type="text" class="form-control form-control-sm bg-light font-weight-bold" id="totalPaxDisplay" value="1 Person (Lead)" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Lead Passenger Name <span class="text-danger">*</span></label>
                        <input type="text" name="leader_name" class="form-control form-control-sm" value="{{ old('leader_name') }}" required placeholder="Full Name">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Passport No <span class="text-muted text-xxs font-weight-normal">(Optional)</span></label>
                        <input type="text" name="leader_passport_no" class="form-control form-control-sm" value="{{ old('leader_passport_no') }}" placeholder="Passport No">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Contact Phone <span class="text-muted text-xxs font-weight-normal">(Optional)</span></label>
                        <input type="text" name="leader_contact_no" class="form-control form-control-sm" value="{{ old('leader_contact_no') }}" placeholder="+92 300 0000000">
                    </div>

                    <div class="col-12">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Special Notes / Requests</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="1" placeholder="Special preferences or instructions...">{{ old('notes') }}</textarea>
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
                <div id="pilgrimsContainer"></div>
                <div id="noPilgrimsNotice" class="text-secondary text-xxs fst-italic">No additional travelers added yet. Click 'Add Traveler' above to add members.</div>
            </div>
        </div>

        <!-- 3. Flight Segments -->
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
                    <!-- Default Flight Row -->
                    <div class="flight-row card border border-radius-md p-2 mb-2 bg-light position-relative">
                        <button type="button" class="btn-close text-dark position-absolute top-0 end-0 m-2 btn-remove-row" style="font-size: 9px;"></button>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Flight No <span class="text-danger">*</span></label>
                                <input type="text" name="flights[0][flight_no]" class="form-control form-control-sm font-weight-bold" placeholder="SV-701" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Airline</label>
                                <input type="text" name="flights[0][airline]" class="form-control form-control-sm" placeholder="Saudia">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">From City <span class="text-danger">*</span></label>
                                <input type="text" name="flights[0][from_city]" class="form-control form-control-sm" placeholder="Karachi (KHI)" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">To City <span class="text-danger">*</span></label>
                                <input type="text" name="flights[0][to_city]" class="form-control form-control-sm" placeholder="Jeddah (JED)" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">ETD (Departure) <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="flights[0][etd]" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">PNR</label>
                                <input type="text" name="flights[0][pnr]" class="form-control form-control-sm" placeholder="PNR Ref">
                            </div>
                        </div>
                    </div>
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
                    <!-- Default Hotel Row -->
                    <div class="hotel-row card border border-radius-md p-3 mb-2 bg-white shadow-none position-relative">
                        <button type="button" class="btn-close text-dark position-absolute top-0 end-0 m-2 btn-remove-row" style="font-size: 9px;"></button>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">City <span class="text-danger">*</span></label>
                                <select name="hotels[0][city]" class="form-select form-select-sm hotel-city-select" required>
                                    <option value="Makkah" selected>Makkah</option>
                                    <option value="Madinah">Madinah</option>
                                    <option value="Jeddah">Jeddah</option>
                                    <option value="Riyadh">Riyadh</option>
                                    <option value="Taif">Taif</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Hotel Name <span class="text-danger">*</span></label>
                                <input type="text" name="hotels[0][hotel_name]" class="form-control form-control-sm" placeholder="e.g. Pullman Zamzam" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Voucher / Res Ref</label>
                                <input type="text" name="hotels[0][voucher_no]" class="form-control form-control-sm" placeholder="Res Ref">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Room Type <span class="text-danger">*</span></label>
                                <select name="hotels[0][room_type]" class="form-select form-select-sm" required>
                                    <option value="Double" selected>Double</option>
                                    <option value="Triple">Triple</option>
                                    <option value="Quad">Quad</option>
                                    <option value="Quint">Quint</option>
                                    <option value="Hexa">Hexa</option>
                                    <option value="Penta">Penta</option>
                                    <option value="Suite">Suite</option>
                                    <option value="Sharing">Sharing</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Qty</label>
                                <input type="number" name="hotels[0][room_qty]" class="form-control form-control-sm text-center" value="1" min="1" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Duration</label>
                                <input type="text" class="form-control form-control-sm text-center bg-light hotel-nights-display" value="—" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-In <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="hotels[0][check_in]" class="form-control form-control-sm hotel-checkin-input" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-Out <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="hotels[0][check_out]" class="form-control form-control-sm hotel-checkout-input" required>
                            </div>

                            <div class="col-md-4 d-flex align-items-center mt-3">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="hotels[0][schedule_ziyaraat]" value="1" id="ziyaraatToggle0" checked>
                                    <label class="form-check-label text-xs text-dark font-weight-semibold" for="ziyaraatToggle0">
                                        Auto-Schedule Ziyaraat (+48h)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Optional Services -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-train text-primary me-1"></i> 5. Additional Transfers & Services <span class="badge bg-light text-secondary text-xxs border ms-1">Optional</span>
                </span>
                <button type="button" class="btn btn-xs btn-outline-secondary mb-0" id="btnAddCustomLeg">
                    <i class="fa-solid fa-plus me-1"></i> Add Service
                </button>
            </div>
            <div class="card-body p-3">
                <div id="customLegsContainer"></div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="card shadow-xs border-0 border-radius-lg p-3 bg-light d-flex flex-row justify-content-between align-items-center">
            <a href="{{ route('pilgrimage.index') }}" class="btn btn-outline-secondary btn-sm mb-0">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 mb-0 shadow-sm" id="btnSubmitBooking">
                <i class="fa-solid fa-paper-plane me-1"></i> Submit Booking
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let pilgrimCount = 0;
    let flightCount = 1;
    let hotelCount = 1;
    let customCount = 0;

    const pilgrimsContainer = document.getElementById('pilgrimsContainer');
    const noPilgrimsNotice = document.getElementById('noPilgrimsNotice');
    const flightsContainer = document.getElementById('flightsContainer');
    const hotelsContainer = document.getElementById('hotelsContainer');
    const customLegsContainer = document.getElementById('customLegsContainer');
    const totalPaxDisplay = document.getElementById('totalPaxDisplay');

    function updatePaxCount() {
        const count = 1 + document.querySelectorAll('.pilgrim-row').length;
        totalPaxDisplay.value = `${count} ${count === 1 ? 'Person (Lead)' : 'Persons (1 Lead + ' + (count - 1) + ' Co-Travelers)'}`;
        if (noPilgrimsNotice) {
            noPilgrimsNotice.style.display = document.querySelectorAll('.pilgrim-row').length > 0 ? 'none' : 'block';
        }
    }

    document.getElementById('btnAddPilgrim').addEventListener('click', function () {
        pilgrimCount++;
        const row = document.createElement('div');
        row.className = 'pilgrim-row card border border-radius-md p-2 mb-2 bg-light position-relative';
        row.innerHTML = `
            <button type="button" class="btn-close text-dark position-absolute top-0 end-0 m-2 btn-remove-row" style="font-size: 9px;"></button>
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="pilgrims[${pilgrimCount}][name]" class="form-control form-control-sm" placeholder="Full Name" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Passport No</label>
                    <input type="text" name="pilgrims[${pilgrimCount}][passport_no]" class="form-control form-control-sm" placeholder="Passport No">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Gender</label>
                    <select name="pilgrims[${pilgrimCount}][gender]" class="form-select form-select-sm">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Age Category</label>
                    <select name="pilgrims[${pilgrimCount}][age_type]" class="form-select form-select-sm">
                        <option value="adult" selected>Adult</option>
                        <option value="child">Child</option>
                        <option value="infant">Infant</option>
                    </select>
                </div>
            </div>
        `;
        pilgrimsContainer.appendChild(row);
        updatePaxCount();
    });

    document.getElementById('btnAddFlight').addEventListener('click', function () {
        flightCount++;
        const row = document.createElement('div');
        row.className = 'flight-row card border border-radius-md p-2 mb-2 bg-light position-relative';
        row.innerHTML = `
            <button type="button" class="btn-close text-dark position-absolute top-0 end-0 m-2 btn-remove-row" style="font-size: 9px;"></button>
            <div class="row g-2 align-items-center">
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Flight No <span class="text-danger">*</span></label>
                    <input type="text" name="flights[${flightCount}][flight_no]" class="form-control form-control-sm font-weight-bold" placeholder="SV-702" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Airline</label>
                    <input type="text" name="flights[${flightCount}][airline]" class="form-control form-control-sm" placeholder="Saudia">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">From City <span class="text-danger">*</span></label>
                    <input type="text" name="flights[${flightCount}][from_city]" class="form-control form-control-sm" placeholder="Madinah (MED)" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">To City <span class="text-danger">*</span></label>
                    <input type="text" name="flights[${flightCount}][to_city]" class="form-control form-control-sm" placeholder="Karachi (KHI)" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">ETD <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="flights[${flightCount}][etd]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">PNR</label>
                    <input type="text" name="flights[${flightCount}][pnr]" class="form-control form-control-sm" placeholder="PNR Ref">
                </div>
            </div>
        `;
        flightsContainer.appendChild(row);
    });

    document.getElementById('btnAddHotel').addEventListener('click', function () {
        hotelCount++;
        const row = document.createElement('div');
        row.className = 'hotel-row card border border-radius-md p-3 mb-2 bg-white shadow-none position-relative';
        row.innerHTML = `
            <button type="button" class="btn-close text-dark position-absolute top-0 end-0 m-2 btn-remove-row" style="font-size: 9px;"></button>
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">City <span class="text-danger">*</span></label>
                    <select name="hotels[${hotelCount}][city]" class="form-select form-select-sm hotel-city-select" required>
                        <option value="Madinah" selected>Madinah</option>
                        <option value="Makkah">Makkah</option>
                        <option value="Jeddah">Jeddah</option>
                        <option value="Riyadh">Riyadh</option>
                        <option value="Taif">Taif</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Hotel Name <span class="text-danger">*</span></label>
                    <input type="text" name="hotels[${hotelCount}][hotel_name]" class="form-control form-control-sm" placeholder="Hotel Name" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Voucher / Res Ref</label>
                    <input type="text" name="hotels[${hotelCount}][voucher_no]" class="form-control form-control-sm" placeholder="Res Ref">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Room Type <span class="text-danger">*</span></label>
                    <select name="hotels[${hotelCount}][room_type]" class="form-select form-select-sm" required>
                        <option value="Double" selected>Double</option>
                        <option value="Triple">Triple</option>
                        <option value="Quad">Quad</option>
                        <option value="Quint">Quint</option>
                        <option value="Hexa">Hexa</option>
                        <option value="Penta">Penta</option>
                        <option value="Suite">Suite</option>
                        <option value="Sharing">Sharing</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Qty</label>
                    <input type="number" name="hotels[${hotelCount}][room_qty]" class="form-control form-control-sm text-center" value="1" min="1" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Duration</label>
                    <input type="text" class="form-control form-control-sm text-center bg-light hotel-nights-display" value="—" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-In <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="hotels[${hotelCount}][check_in]" class="form-control form-control-sm hotel-checkin-input" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-Out <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="hotels[${hotelCount}][check_out]" class="form-control form-control-sm hotel-checkout-input" required>
                </div>

                <div class="col-md-4 d-flex align-items-center mt-3">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="hotels[${hotelCount}][schedule_ziyaraat]" value="1" id="ziyaraatToggle${hotelCount}" checked>
                        <label class="form-check-label text-xs text-dark font-weight-semibold" for="ziyaraatToggle${hotelCount}">
                            Auto-Schedule Ziyaraat (+48h)
                        </label>
                    </div>
                </div>
            </div>
        `;
        hotelsContainer.appendChild(row);
        attachDateChangeListeners(row);
    });

    document.getElementById('btnAddCustomLeg').addEventListener('click', function () {
        customCount++;
        const row = document.createElement('div');
        row.className = 'custom-leg-row card border border-radius-md p-2 mb-2 bg-light position-relative';
        row.innerHTML = `
            <button type="button" class="btn-close text-dark position-absolute top-0 end-0 m-2 btn-remove-row" style="font-size: 9px;"></button>
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Service Title <span class="text-danger">*</span></label>
                    <input type="text" name="custom_legs[${customCount}][title]" class="form-control form-control-sm" placeholder="e.g. Haramain Bullet Train" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Route</label>
                    <input type="text" name="custom_legs[${customCount}][city_or_route]" class="form-control form-control-sm" placeholder="Makkah → Madinah">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Date & Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="custom_legs[${customCount}][event_datetime]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Type</label>
                    <select name="custom_legs[${customCount}][leg_type]" class="form-select form-select-sm">
                        <option value="custom_transfer">Transfer</option>
                        <option value="custom_activity">Excursion</option>
                    </select>
                </div>
            </div>
        `;
        customLegsContainer.appendChild(row);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-row')) {
            const card = e.target.closest('.pilgrim-row, .flight-row, .hotel-row, .custom-leg-row');
            if (card) {
                card.remove();
                updatePaxCount();
            }
        }
    });

    function attachDateChangeListeners(parent) {
        const inInput = parent.querySelector('.hotel-checkin-input');
        const outInput = parent.querySelector('.hotel-checkout-input');
        const display = parent.querySelector('.hotel-nights-display');

        function recalc() {
            if (inInput.value && outInput.value) {
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

    document.querySelectorAll('.hotel-row').forEach(attachDateChangeListeners);
    updatePaxCount();
});
</script>
@endsection
