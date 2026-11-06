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
                <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> New Booking Intake
            </h5>
            <span class="text-xs text-secondary">Record traveler details, flights, and accommodations for pilgrimage & customized itineraries</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border py-2 px-3 text-xs">
                Booking Ref: <strong class="text-primary">{{ $suggestedCrmId }}</strong> <span class="text-xxs text-muted ms-1">(Auto-Generated)</span>
            </span>
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

    <form method="POST" action="{{ route('pilgrimage.store') }}" id="bookingIntakeForm">
        @csrf
        <!-- Auto-Generated CRM ID (Hidden from UI clutter as requested) -->
        <input type="hidden" name="crm_id" value="{{ old('crm_id', $suggestedCrmId) }}">

        <!-- 1. Reference & Lead Passenger -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-id-card text-primary me-1"></i> 1. Booking Information & Lead Passenger
                </span>
                <span class="text-xxs text-secondary">Reference: <strong class="text-dark">{{ $suggestedCrmId }}</strong></span>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Service Category <span class="text-danger">*</span></label>
                        <select name="service_type" class="form-select form-select-sm" required>
                            <option value="umrah" {{ old('service_type') == 'umrah' ? 'selected' : '' }}>Umrah Package</option>
                            <option value="hajj" {{ old('service_type') == 'hajj' ? 'selected' : '' }}>Hajj Pilgrimage</option>
                            <option value="tour" {{ old('service_type') == 'tour' ? 'selected' : '' }}>Group / Custom Tour</option>
                            <option value="corporate" {{ old('service_type') == 'corporate' ? 'selected' : '' }}>Corporate Travel</option>
                            <option value="ticketing" {{ old('service_type') == 'ticketing' ? 'selected' : '' }}>Flight Ticketing & Hotel</option>
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
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Lead Passenger Name <span class="text-danger">*</span></label>
                        <input type="text" name="leader_name" class="form-control form-control-sm" value="{{ old('leader_name') }}" required placeholder="Full Name">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Total Party Size</label>
                        <input type="text" class="form-control form-control-sm bg-light font-weight-bold" id="totalPaxDisplay" value="1 Person (Lead)" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Passport No <span class="text-muted text-xxs font-weight-normal">(Optional)</span></label>
                        <input type="text" name="leader_passport_no" class="form-control form-control-sm" value="{{ old('leader_passport_no') }}" placeholder="e.g. AB1234567">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Contact Phone / WhatsApp <span class="text-muted text-xxs font-weight-normal">(Optional)</span></label>
                        <input type="text" name="leader_contact_no" class="form-control form-control-sm" value="{{ old('leader_contact_no') }}" placeholder="+92 300 1234567">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Package / Tier <span class="text-muted text-xxs font-weight-normal">(Optional)</span></label>
                        <select name="package_tier" class="form-select form-select-sm">
                            <option value="Customized" selected>Customized Itinerary</option>
                            <option value="5-Star Luxury">5-Star Luxury (Clock Tower)</option>
                            <option value="4-Star Premium">4-Star Premium</option>
                            <option value="3-Star Economy">3-Star Economy</option>
                            <option value="Economy Sharing">Economy Sharing</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Special Notes / Preferences</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="1" placeholder="Special preferences, wheelchair assistance, ground handling instructions...">{{ old('notes') }}</textarea>
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
                <div id="noPilgrimsNotice" class="text-secondary text-xxs fst-italic">No additional travelers added yet. Click 'Add Traveler' above to add family or group members.</div>
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
                    <div class="flight-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                            <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border flight-badge">
                                <i class="fa-solid fa-plane-departure text-primary me-1"></i> Flight Segment #1
                            </span>
                            <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Flight">
                                <i class="fa-solid fa-trash-can me-1"></i> Delete
                            </button>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Flight No <span class="text-danger">*</span></label>
                                <input type="text" name="flights[0][flight_no]" class="form-control form-control-sm font-weight-bold" placeholder="e.g. SV-701" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Airline</label>
                                <input type="text" name="flights[0][airline]" class="form-control form-control-sm" placeholder="e.g. Saudia, PIA">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">From City <span class="text-danger">*</span></label>
                                <input type="text" name="flights[0][from_city]" list="worldCitiesDatalist" class="form-control form-control-sm" placeholder="e.g. Karachi" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">To City <span class="text-danger">*</span></label>
                                <input type="text" name="flights[0][to_city]" list="worldCitiesDatalist" class="form-control form-control-sm" placeholder="e.g. Jeddah" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">ETD (Departure) <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="flights[0][etd]" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">PNR / Ref</label>
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
                    <i class="fa-solid fa-hotel text-primary me-1"></i> 4. Hotel Accommodations & Stays
                </span>
                <button type="button" class="btn btn-xs btn-outline-primary mb-0" id="btnAddHotel">
                    <i class="fa-solid fa-plus me-1"></i> Add Hotel Stay
                </button>
            </div>
            <div class="card-body p-3">
                <div id="hotelsContainer">
                    <!-- Default Hotel Row -->
                    <div class="hotel-row card border border-radius-md p-3 mb-3 bg-white shadow-none position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                            <span class="badge bg-light text-dark text-xxs font-weight-bolder text-uppercase border hotel-badge">
                                <i class="fa-solid fa-hotel text-primary me-1"></i> Hotel Stay #1
                            </span>
                            <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Hotel">
                                <i class="fa-solid fa-trash-can me-1"></i> Delete
                            </button>
                        </div>
                        <div class="row g-2">
                            <!-- City -->
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">City <span class="text-danger">*</span></label>
                                <input type="text" name="hotels[0][city]" list="worldCitiesDatalist" class="form-control form-control-sm hotel-city-input" value="Makkah" placeholder="Type city..." required>
                            </div>

                            <!-- Hotel Name & Quick Add Button -->
                            <div class="col-md-4">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1 d-flex justify-content-between align-items-center">
                                    <span>Hotel Name <span class="text-danger">*</span></span>
                                    <a href="javascript:void(0);" class="text-primary text-xxs fw-bold btn-quick-add-trigger"><i class="fa-solid fa-plus me-1"></i>New Hotel</a>
                                </label>
                                <div class="input-group input-group-sm">
                                    <select name="hotels[0][hotel_name]" class="form-select form-select-sm hotel-name-select" required>
                                        <option value="">Select Hotel...</option>
                                        @foreach($initialHotels as $h)
                                            <option value="{{ $h->name }}" data-city="{{ $h->city }}" {{ (stripos($h->city, 'makk') !== false && str_contains($h->name, 'Zamzam')) ? 'selected' : '' }}>
                                                {{ $h->name }} ({{ $h->star_rating ?? 'Standard' }}{{ $h->distance ? ' - ' . $h->distance : '' }})
                                            </option>
                                        @endforeach
                                        <option value="__CUSTOM__" class="fw-bold text-primary">+ Add New Hotel...</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary btn-sm mb-0 btn-quick-add-trigger" title="Quick Add Hotel">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Voucher / Res Ref -->
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Res / Voucher Ref</label>
                                <input type="text" name="hotels[0][voucher_no]" class="form-control form-control-sm" placeholder="Res Ref No">
                            </div>

                            <!-- Room Type -->
                            <div class="col-md-2">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Room Type <span class="text-danger">*</span></label>
                                <select name="hotels[0][room_type]" class="form-select form-select-sm" required>
                                    <option value="Double" selected>Double (2 Beds)</option>
                                    <option value="Triple">Triple (3 Beds)</option>
                                    <option value="Quad">Quad (4 Beds)</option>
                                    <option value="Quint">Quint (5 Beds)</option>
                                    <option value="Hexa">Hexa (6 Beds)</option>
                                    <option value="Penta">Penta</option>
                                    <option value="Suite">Suite Room</option>
                                    <option value="Family Suite">Family Suite</option>
                                    <option value="Single">Single Room</option>
                                    <option value="Sharing">Sharing</option>
                                </select>
                            </div>

                            <!-- Room Qty -->
                            <div class="col-md-1">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Qty</label>
                                <input type="number" name="hotels[0][room_qty]" class="form-control form-control-sm text-center" value="1" min="1" required>
                            </div>

                            <!-- Duration Display -->
                            <div class="col-md-1">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Nights</label>
                                <input type="text" class="form-control form-control-sm text-center bg-light hotel-nights-display" value="—" readonly>
                            </div>

                            <!-- Check-In -->
                            <div class="col-md-4">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-In <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="hotels[0][check_in]" class="form-control form-control-sm hotel-checkin-input" required>
                            </div>

                            <!-- Check-Out -->
                            <div class="col-md-4">
                                <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-Out <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="hotels[0][check_out]" class="form-control form-control-sm hotel-checkout-input" required>
                            </div>

                            <!-- Ziyaraat Auto-Schedule Toggle -->
                            <div class="col-md-4 d-flex align-items-center mt-3">
                                <label class="custom-toggle-switch">
                                    <input type="checkbox" name="hotels[0][schedule_ziyaraat]" value="1" checked>
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label">Auto-Schedule Ziyaraat (+48h)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Optional Services / Transfers -->
        <div class="card shadow-xs border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="text-xs font-weight-bold text-dark text-uppercase">
                    <i class="fa-solid fa-train text-primary me-1"></i> 5. Additional Transfers & Ground Services <span class="badge bg-light text-secondary text-xxs border ms-1">Optional</span>
                </span>
                <button type="button" class="btn btn-xs btn-outline-secondary mb-0" id="btnAddCustomLeg">
                    <i class="fa-solid fa-plus me-1"></i> Add Service
                </button>
            </div>
            <div class="card-body p-3">
                <div id="customLegsContainer"></div>
                <div id="noCustomNotice" class="text-secondary text-xxs fst-italic">No additional ground services or high-speed train legs added. Click 'Add Service' to schedule.</div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="card shadow-xs border-0 border-radius-lg p-3 bg-light d-flex flex-row justify-content-between align-items-center">
            <a href="{{ route('pilgrimage.index') }}" class="btn btn-outline-secondary btn-sm mb-0">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 mb-0 shadow-sm" id="btnSubmitBooking">
                <i class="fa-solid fa-paper-plane me-1"></i> Submit Booking & Generate Itinerary
            </button>
        </div>

    </form>
</div>

<!-- Datalist for World Cities (Auto-Populated) -->
<datalist id="worldCitiesDatalist">
    @foreach($popularCities as $pc)
        <option value="{{ $pc }}">
    @endforeach
</datalist>

<!-- Quick Add Hotel Modal -->
<div class="modal fade" id="quickAddHotelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-3">
                <h6 class="modal-title font-weight-bold">
                    <i class="fa-solid fa-hotel text-primary me-2"></i> Quick Add New Hotel
                </h6>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form id="quickAddHotelForm">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Hotel Name <span class="text-danger">*</span></label>
                            <input type="text" id="quick_hotel_name" class="form-control form-control-sm" placeholder="e.g. Jabal Omar Hilton" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xxs font-weight-bold text-secondary mb-1">City <span class="text-danger">*</span></label>
                            <input type="text" id="quick_hotel_city" list="worldCitiesDatalist" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Category / Star Rating</label>
                            <select id="quick_hotel_rating" class="form-select form-select-sm">
                                <option value="5 Star">5 Star (Luxury / Clock Tower)</option>
                                <option value="4 Star" selected>4 Star</option>
                                <option value="3 Star">3 Star</option>
                                <option value="Standard / Economy">Standard / Economy</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Haram Distance / Location</label>
                            <input type="text" id="quick_hotel_distance" class="form-control form-control-sm" placeholder="e.g. 150m from Haram">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Address / Landmark</label>
                            <input type="text" id="quick_hotel_address" class="form-control form-control-sm" placeholder="Street, Gate proximity">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm mb-0" id="btnSaveQuickHotel">
                            <i class="fa-solid fa-save me-1"></i> Save & Use Hotel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pilgrimsContainer = document.getElementById('pilgrimsContainer');
    const noPilgrimsNotice = document.getElementById('noPilgrimsNotice');
    const flightsContainer = document.getElementById('flightsContainer');
    const hotelsContainer = document.getElementById('hotelsContainer');
    const customLegsContainer = document.getElementById('customLegsContainer');
    const noCustomNotice = document.getElementById('noCustomNotice');
    const totalPaxDisplay = document.getElementById('totalPaxDisplay');

    let activeHotelSelectForQuickAdd = null;

    // --- PAX COUNT UPDATE ---
    function updatePaxCount() {
        const count = 1 + document.querySelectorAll('.pilgrim-row').length;
        totalPaxDisplay.value = `${count} ${count === 1 ? 'Person (Lead)' : 'Persons (1 Lead + ' + (count - 1) + ' Co-Travelers)'}`;
        if (noPilgrimsNotice) {
            noPilgrimsNotice.style.display = document.querySelectorAll('.pilgrim-row').length > 0 ? 'none' : 'block';
        }
    }

    // --- SEQUENTIAL RE-NUMBERING ON ADD / DELETE ---
    function renumberPilgrims() {
        const rows = document.querySelectorAll('.pilgrim-row');
        rows.forEach((row, idx) => {
            const num = idx + 1;
            const badge = row.querySelector('.pilgrim-badge');
            if (badge) {
                badge.innerHTML = `<i class="fa-solid fa-user-plus text-primary me-1"></i> Co-Traveler #${num}`;
            }
            row.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/pilgrims\[\d+\]/, `pilgrims[${idx}]`));
                }
            });
        });
        updatePaxCount();
    }

    function renumberFlights() {
        const rows = document.querySelectorAll('.flight-row');
        rows.forEach((row, idx) => {
            const num = idx + 1;
            const badge = row.querySelector('.flight-badge');
            if (badge) {
                badge.innerHTML = `<i class="fa-solid fa-plane-departure text-primary me-1"></i> Flight Segment #${num}`;
            }
            row.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/flights\[\d+\]/, `flights[${idx}]`));
                }
            });
        });
    }

    function renumberHotels() {
        const rows = document.querySelectorAll('.hotel-row');
        rows.forEach((row, idx) => {
            const num = idx + 1;
            const badge = row.querySelector('.hotel-badge');
            if (badge) {
                badge.innerHTML = `<i class="fa-solid fa-hotel text-primary me-1"></i> Hotel Stay #${num}`;
            }
            row.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/hotels\[\d+\]/, `hotels[${idx}]`));
                }
            });
        });
    }

    function renumberCustomLegs() {
        const rows = document.querySelectorAll('.custom-leg-row');
        if (noCustomNotice) {
            noCustomNotice.style.display = rows.length > 0 ? 'none' : 'block';
        }
        rows.forEach((row, idx) => {
            const num = idx + 1;
            const badge = row.querySelector('.custom-leg-badge');
            if (badge) {
                badge.innerHTML = `<i class="fa-solid fa-train text-primary me-1"></i> Extra Service / Transfer #${num}`;
            }
            row.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/custom_legs\[\d+\]/, `custom_legs[${idx}]`));
                }
            });
        });
    }

    // --- ADD TRAVELER ---
    document.getElementById('btnAddPilgrim').addEventListener('click', function () {
        const nextIdx = document.querySelectorAll('.pilgrim-row').length;
        const row = document.createElement('div');
        row.className = 'pilgrim-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border pilgrim-badge">
                    <i class="fa-solid fa-user-plus text-primary me-1"></i> Co-Traveler #${nextIdx + 1}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Traveler">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="pilgrims[${nextIdx}][name]" class="form-control form-control-sm" placeholder="Full Name" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Passport No</label>
                    <input type="text" name="pilgrims[${nextIdx}][passport_no]" class="form-control form-control-sm" placeholder="e.g. AB1234567">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Gender</label>
                    <select name="pilgrims[${nextIdx}][gender]" class="form-select form-select-sm">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Age Category</label>
                    <select name="pilgrims[${nextIdx}][age_type]" class="form-select form-select-sm">
                        <option value="adult" selected>Adult (12+ yrs)</option>
                        <option value="child">Child (2 - 11 yrs)</option>
                        <option value="infant">Infant (0 - 2 yrs)</option>
                    </select>
                </div>
            </div>
        `;
        pilgrimsContainer.appendChild(row);
        renumberPilgrims();
    });

    // --- ADD FLIGHT ---
    document.getElementById('btnAddFlight').addEventListener('click', function () {
        const nextIdx = document.querySelectorAll('.flight-row').length;
        const row = document.createElement('div');
        row.className = 'flight-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border flight-badge">
                    <i class="fa-solid fa-plane-departure text-primary me-1"></i> Flight Segment #${nextIdx + 1}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Flight">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
            <div class="row g-2 align-items-center">
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Flight No <span class="text-danger">*</span></label>
                    <input type="text" name="flights[${nextIdx}][flight_no]" class="form-control form-control-sm font-weight-bold" placeholder="e.g. SV-702" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Airline</label>
                    <input type="text" name="flights[${nextIdx}][airline]" class="form-control form-control-sm" placeholder="e.g. Saudia">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">From City <span class="text-danger">*</span></label>
                    <input type="text" name="flights[${nextIdx}][from_city]" list="worldCitiesDatalist" class="form-control form-control-sm" placeholder="e.g. Madinah" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">To City <span class="text-danger">*</span></label>
                    <input type="text" name="flights[${nextIdx}][to_city]" list="worldCitiesDatalist" class="form-control form-control-sm" placeholder="e.g. Karachi" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">ETD <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="flights[${nextIdx}][etd]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">PNR / Ref</label>
                    <input type="text" name="flights[${nextIdx}][pnr]" class="form-control form-control-sm" placeholder="PNR Ref">
                </div>
            </div>
        `;
        flightsContainer.appendChild(row);
        renumberFlights();
    });

    // --- ADD HOTEL STAY ---
    document.getElementById('btnAddHotel').addEventListener('click', function () {
        const nextIdx = document.querySelectorAll('.hotel-row').length;
        const row = document.createElement('div');
        row.className = 'hotel-row card border border-radius-md p-3 mb-3 bg-white shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-light text-dark text-xxs font-weight-bolder text-uppercase border hotel-badge">
                    <i class="fa-solid fa-hotel text-primary me-1"></i> Hotel Stay #${nextIdx + 1}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Hotel">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">City <span class="text-danger">*</span></label>
                    <input type="text" name="hotels[${nextIdx}][city]" list="worldCitiesDatalist" class="form-control form-control-sm hotel-city-input" value="Madinah" placeholder="Type city..." required>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1 d-flex justify-content-between align-items-center">
                        <span>Hotel Name <span class="text-danger">*</span></span>
                        <a href="javascript:void(0);" class="text-primary text-xxs fw-bold btn-quick-add-trigger"><i class="fa-solid fa-plus me-1"></i>New Hotel</a>
                    </label>
                    <div class="input-group input-group-sm">
                        <select name="hotels[${nextIdx}][hotel_name]" class="form-select form-select-sm hotel-name-select" required>
                            <option value="">Select Hotel...</option>
                            <option value="__CUSTOM__" class="fw-bold text-primary">+ Add New Hotel...</option>
                        </select>
                        <button type="button" class="btn btn-outline-primary btn-sm mb-0 btn-quick-add-trigger" title="Quick Add Hotel">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Res / Voucher Ref</label>
                    <input type="text" name="hotels[${nextIdx}][voucher_no]" class="form-control form-control-sm" placeholder="Res Ref No">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Room Type <span class="text-danger">*</span></label>
                    <select name="hotels[${nextIdx}][room_type]" class="form-select form-select-sm" required>
                        <option value="Double" selected>Double (2 Beds)</option>
                        <option value="Triple">Triple (3 Beds)</option>
                        <option value="Quad">Quad (4 Beds)</option>
                        <option value="Quint">Quint (5 Beds)</option>
                        <option value="Hexa">Hexa (6 Beds)</option>
                        <option value="Penta">Penta</option>
                        <option value="Suite">Suite Room</option>
                        <option value="Family Suite">Family Suite</option>
                        <option value="Single">Single Room</option>
                        <option value="Sharing">Sharing</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Qty</label>
                    <input type="number" name="hotels[${nextIdx}][room_qty]" class="form-control form-control-sm text-center" value="1" min="1" required>
                </div>

                <div class="col-md-1">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Nights</label>
                    <input type="text" class="form-control form-control-sm text-center bg-light hotel-nights-display" value="—" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-In <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="hotels[${nextIdx}][check_in]" class="form-control form-control-sm hotel-checkin-input" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Check-Out <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="hotels[${nextIdx}][check_out]" class="form-control form-control-sm hotel-checkout-input" required>
                </div>

                <div class="col-md-4 d-flex align-items-center mt-3">
                    <label class="custom-toggle-switch">
                        <input type="checkbox" name="hotels[${nextIdx}][schedule_ziyaraat]" value="1" checked>
                        <span class="toggle-slider"></span>
                        <span class="toggle-label">Auto-Schedule Ziyaraat (+48h)</span>
                    </label>
                </div>
            </div>
        `;
        hotelsContainer.appendChild(row);
        renumberHotels();
        attachDateChangeListeners(row);
        loadHotelsForCity(row);
    });

    // --- ADD ADDITIONAL GROUND SERVICES ---
    document.getElementById('btnAddCustomLeg').addEventListener('click', function () {
        const nextIdx = document.querySelectorAll('.custom-leg-row').length;
        const row = document.createElement('div');
        row.className = 'custom-leg-row card border border-radius-md p-3 mb-3 bg-light shadow-none position-relative';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase border custom-leg-badge">
                    <i class="fa-solid fa-train text-primary me-1"></i> Extra Service / Transfer #${nextIdx + 1}
                </span>
                <button type="button" class="btn btn-outline-danger btn-xs mb-0 py-1 px-2 btn-remove-row shadow-none" title="Delete Service">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </div>
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Service Title <span class="text-danger">*</span></label>
                    <input type="text" name="custom_legs[${nextIdx}][title]" class="form-control form-control-sm" placeholder="e.g. Haramain Bullet Train, Private GMC" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Route / Sector</label>
                    <input type="text" name="custom_legs[${nextIdx}][city_or_route]" class="form-control form-control-sm" placeholder="Makkah → Madinah">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Date & Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="custom_legs[${nextIdx}][event_datetime]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xxs font-weight-bold text-secondary mb-1">Vehicle / Service Type</label>
                    <select name="custom_legs[${nextIdx}][leg_type]" class="form-select form-select-sm">
                        <option value="custom_transfer">Transfer (Private Car / GMC / Coaster)</option>
                        <option value="custom_activity">Excursion / Ziyaraat Tour</option>
                        <option value="custom_train">Haramain Bullet Train</option>
                        <option value="custom_visa">Visa & Ground Handling</option>
                    </select>
                </div>
            </div>
        `;
        customLegsContainer.appendChild(row);
        renumberCustomLegs();
    });

    // --- REMOVE ROW (WITH DYNAMIC SEQUENTIAL RE-NUMBERING) ---
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-remove-row');
        if (btn) {
            const card = btn.closest('.pilgrim-row, .flight-row, .hotel-row, .custom-leg-row');
            if (card) {
                const isPilgrim = card.classList.contains('pilgrim-row');
                const isFlight = card.classList.contains('flight-row');
                const isHotel = card.classList.contains('hotel-row');
                const isCustom = card.classList.contains('custom-leg-row');

                card.remove();

                if (isPilgrim) renumberPilgrims();
                if (isFlight) renumberFlights();
                if (isHotel) renumberHotels();
                if (isCustom) renumberCustomLegs();
            }
        }
    });

    // --- DYNAMIC HOTEL FETCHING BY CITY ---
    function loadHotelsForCity(hotelRow) {
        const cityInput = hotelRow.querySelector('.hotel-city-input');
        const hotelSelect = hotelRow.querySelector('.hotel-name-select');
        if (!cityInput || !hotelSelect) return;

        const city = cityInput.value.trim();
        if (!city) return;

        fetch(`/api/hotels-by-city?city=${encodeURIComponent(city)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.hotels) {
                    const currentVal = hotelSelect.value;
                    let html = '<option value="">Select Hotel...</option>';
                    data.hotels.forEach(h => {
                        const label = `${h.name} (${h.star_rating || 'Standard'}${h.distance ? ' - ' + h.distance : ''})`;
                        const isSelected = (currentVal === h.name) ? 'selected' : '';
                        html += `<option value="${h.name}" ${isSelected}>${label}</option>`;
                    });
                    html += '<option value="__CUSTOM__" class="fw-bold text-primary">+ Add New Hotel...</option>';
                    hotelSelect.innerHTML = html;
                }
            })
            .catch(err => console.error('Error fetching hotels for city:', err));
    }

    // Attach city change listener to hotel rows
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('hotel-city-input')) {
            const row = e.target.closest('.hotel-row');
            if (row) loadHotelsForCity(row);
        }
    });

    // When Hotel select changes to "+ Add New Hotel...", open modal
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('hotel-name-select') && e.target.value === '__CUSTOM__') {
            const row = e.target.closest('.hotel-row');
            activeHotelSelectForQuickAdd = e.target;
            openQuickAddHotelModal(row);
        }
    });

    // When Quick Add button is clicked
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.btn-quick-add-trigger');
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

    // Quick Add Hotel form submit
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
                // Close modal
                const modalEl = document.getElementById('quickAddHotelModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                // If we have an active select, append and select
                if (activeHotelSelectForQuickAdd) {
                    const label = `${data.hotel.name} (${data.hotel.star_rating || 'Standard'}${data.hotel.distance ? ' - ' + data.hotel.distance : ''})`;
                    const newOpt = new Option(label, data.hotel.name, true, true);
                    
                    // Insert before the last option (+ Add New Hotel...)
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

    updatePaxCount();
    renumberFlights();
    renumberHotels();
    renumberCustomLegs();
});
</script>
@endsection
