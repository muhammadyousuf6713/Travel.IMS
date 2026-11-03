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
</style>
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
                        <div class="pilgrim-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border">
                                    <i class="fa-solid fa-user-plus text-primary me-1"></i> Co-Traveler #{{ $loop->iteration }}
                                </span>
                                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Traveler">
                                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                                </button>
                            </div>
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
                        <div class="flight-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border">
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
                        <div class="hotel-row card border border-radius-md p-3 mb-3 bg-white shadow-none position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <span class="badge bg-light text-dark text-xxs font-weight-bolder text-uppercase border">
                                    <i class="fa-solid fa-hotel text-primary me-1"></i> Hotel Stay #{{ $loop->iteration }}
                                </span>
                                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Hotel">
                                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                                </button>
                            </div>
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
                                    <label class="custom-toggle-switch">
                                        <input type="checkbox" name="hotels[{{ $hIdx }}][schedule_ziyaraat]" value="1" id="ziyaraatToggleEdit{{ $hIdx }}" {{ $hotel->schedule_ziyaraat ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                        <span class="toggle-label">Auto-Schedule Ziyaraat (+48h)</span>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    let pilgrimCount = {{ count($booking->pilgrims) }};
    let flightCount = {{ count($booking->flights) }};
    let hotelCount = {{ count($booking->hotels) }};

    const pilgrimsContainer = document.getElementById('pilgrimsContainer');
    const flightsContainer = document.getElementById('flightsContainer');
    const hotelsContainer = document.getElementById('hotelsContainer');
    const totalPaxDisplay = document.getElementById('totalPaxDisplay');

    function updatePaxCount() {
        const count = 1 + document.querySelectorAll('.pilgrim-row').length;
        if (totalPaxDisplay) {
            totalPaxDisplay.value = `${count} ${count === 1 ? 'Person (Lead)' : 'Persons (1 Lead + ' + (count - 1) + ' Co-Travelers)'}`;
        }
    }

    document.getElementById('btnAddPilgrim').addEventListener('click', function () {
        pilgrimCount++;
        const row = document.createElement('div');
        row.className = 'pilgrim-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border">
                    <i class="fa-solid fa-user-plus text-primary me-1"></i> Co-Traveler #${pilgrimCount}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Traveler">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
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
        row.className = 'flight-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border">
                    <i class="fa-solid fa-plane-departure text-primary me-1"></i> Flight Segment #${flightCount}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Flight">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
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
        row.className = 'hotel-row card border border-radius-md p-3 mb-3 bg-white shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-light text-dark text-xxs font-weight-bolder text-uppercase border">
                    <i class="fa-solid fa-hotel text-primary me-1"></i> Hotel Stay #${hotelCount}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Hotel">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
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
                    <label class="custom-toggle-switch">
                        <input type="checkbox" name="hotels[${hotelCount}][schedule_ziyaraat]" value="1" id="ziyaraatToggleEdit${hotelCount}" checked>
                        <span class="toggle-slider"></span>
                        <span class="toggle-label">Auto-Schedule Ziyaraat (+48h)</span>
                    </label>
                </div>
            </div>
        `;
        hotelsContainer.appendChild(row);
        attachDateChangeListeners(row);
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-remove-row');
        if (btn) {
            const card = btn.closest('.pilgrim-row, .flight-row, .hotel-row');
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
