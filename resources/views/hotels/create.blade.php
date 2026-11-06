@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-plus-circle text-primary me-2"></i> Register New Hotel</h5>
            <span class="text-xs text-secondary">Add hotel details for Pilgrimage and Itinerary accommodation selections</span>
        </div>
        <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Hotels
        </a>
    </div>

    <div class="card shadow-xs border-0 border-radius-lg">
        <form method="POST" action="{{ route('hotels.store') }}" class="card-body p-4">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-xs font-weight-bold text-secondary mb-1">Hotel Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Pullman Zamzam Makkah" value="{{ old('name') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-xs font-weight-bold text-secondary mb-1">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" list="popularCitiesList" class="form-control form-control-sm" placeholder="e.g. Makkah" value="{{ old('city') }}" required>
                    <datalist id="popularCitiesList">
                        @foreach($popularCities as $pc)
                            <option value="{{ $pc }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-xs font-weight-bold text-secondary mb-1">Country</label>
                    <input type="text" name="country" class="form-control form-control-sm" value="{{ old('country', 'Saudi Arabia') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xs font-weight-bold text-secondary mb-1">Category / Star Rating</label>
                    <select name="star_rating" class="form-select form-select-sm">
                        @foreach($ratings as $r)
                            <option value="{{ $r }}" {{ old('star_rating', '4 Star') == $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xs font-weight-bold text-secondary mb-1">Distance / Proximity to Haram</label>
                    <input type="text" name="distance" class="form-control form-control-sm" placeholder="e.g. 50m from Haram, Clock Tower" value="{{ old('distance') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-xs font-weight-bold text-secondary mb-1">Contact Phone / WhatsApp</label>
                    <input type="text" name="contact_number" class="form-control form-control-sm" placeholder="+966 12 ..." value="{{ old('contact_number') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label text-xs font-weight-bold text-secondary mb-1">Reservation Email</label>
                    <input type="email" name="email" class="form-control form-control-sm" placeholder="reservations@hotel.com" value="{{ old('email') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label text-xs font-weight-bold text-secondary mb-1">Address / Landmark</label>
                    <input type="text" name="address" class="form-control form-control-sm" placeholder="Street, Area, Gate proximity" value="{{ old('address') }}">
                </div>

                <div class="col-12">
                    <label class="form-label text-xs font-weight-bold text-secondary mb-1">Amenities & Notes</label>
                    <textarea name="notes" class="form-control form-control-sm" rows="3" placeholder="e.g. Free Shuttle Bus, Breakfast included...">{{ old('notes') }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="hotelStatus" checked>
                        <label class="form-check-label text-xs font-weight-bold text-secondary" for="hotelStatus">Active & Available in Itinerary Bookings</label>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary btn-sm mb-0">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm mb-0 shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Save Hotel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
