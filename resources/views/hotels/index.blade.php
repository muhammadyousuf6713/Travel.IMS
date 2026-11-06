@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white text-sm mb-4" role="alert">
            <span class="alert-icon me-2"><i class="fa-solid fa-circle-check"></i></span>
            <span class="alert-text">{{ session('success') }}</span>
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" style="top: 10px;"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-white text-sm mb-4" role="alert">
            <span class="alert-icon me-2"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <span class="alert-text">{{ session('error') }}</span>
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" style="top: 10px;"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show text-white text-sm mb-4" role="alert">
            <span class="alert-icon me-2"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <span class="alert-text">{{ $errors->first() }}</span>
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" style="top: 10px;"></button>
        </div>
    @endif

    <div class="card mb-4 shadow-xs border-0 border-radius-lg">
        <div class="card-header pb-0 bg-white border-bottom py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-hotel text-primary me-2"></i> Hotels & Accommodations
                    </h5>
                    <p class="text-secondary text-xxs mb-0">Manage registered hotels, star ratings, Haram distances, and contact details for Pilgrimage & Itinerary bookings.</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm mb-0 d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#addHotelModal">
                        <i class="fa-solid fa-plus me-1"></i> Add New Hotel
                    </button>
                </div>
            </div>

            <!-- Filters Bar -->
            <form method="GET" action="{{ route('hotels.index') }}" class="row g-2 mt-2 pt-2 border-top">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search hotel name, distance, address..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="city" class="form-select form-select-sm">
                        <option value="">All Cities</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="star_rating" class="form-select form-select-sm">
                        <option value="">All Ratings</option>
                        @foreach($ratings as $r)
                            <option value="{{ $r }}" {{ request('star_rating') == $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-info w-100 mb-0">Filter</button>
                    @if(request()->hasAny(['search', 'city', 'star_rating']))
                        <a href="{{ route('hotels.index') }}" class="btn btn-sm btn-outline-secondary mb-0" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle w-100" id="hotelsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Hotel Name</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">City / Country</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Category / Rating</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Distance / Location</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Contact</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hotels as $h)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-2 text-white font-weight-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 13px;">
                                            <i class="fa-solid fa-hotel"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-weight-bold text-dark d-block">{{ $h->name }}</span>
                                            @if($h->address)
                                                <span class="text-xxs text-secondary text-truncate d-inline-block" style="max-width: 250px;">{{ $h->address }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border text-xs font-weight-semibold">
                                        <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $h->city }}
                                    </span>
                                    <span class="text-xxs text-secondary d-block mt-1">{{ $h->country ?? 'Saudi Arabia' }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = match($h->star_rating) {
                                            '5 Star' => 'bg-warning text-dark',
                                            '4 Star' => 'bg-info text-white',
                                            '3 Star' => 'bg-primary text-white',
                                            default => 'bg-secondary text-white'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-xxs px-2 py-1">
                                        @if(str_contains($h->star_rating ?? '', 'Star'))
                                            <i class="fa-solid fa-star me-1"></i>
                                        @endif
                                        {{ $h->star_rating ?? 'Standard' }}
                                    </span>
                                </td>
                                <td>
                                    @if($h->distance)
                                        <span class="text-xs text-dark font-weight-semibold"><i class="fa-solid fa-person-walking text-primary me-1"></i> {{ $h->distance }}</span>
                                    @else
                                        <span class="text-xs text-secondary">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($h->contact_number)
                                        <span class="text-xs text-dark d-block"><i class="fa-solid fa-phone me-1 text-success"></i> {{ $h->contact_number }}</span>
                                    @endif
                                    @if($h->email)
                                        <span class="text-xxs text-secondary d-block"><i class="fa-solid fa-envelope me-1"></i> {{ $h->email }}</span>
                                    @endif
                                    @if(!$h->contact_number && !$h->email)
                                        <span class="text-xs text-secondary">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($h->status)
                                        <span class="badge bg-success text-xxs">Active</span>
                                    @else
                                        <span class="badge bg-secondary text-xxs">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-xs btn-outline-info mb-0 btn-edit-hotel"
                                            data-id="{{ $h->id }}"
                                            data-name="{{ $h->name }}"
                                            data-city="{{ $h->city }}"
                                            data-country="{{ $h->country }}"
                                            data-star_rating="{{ $h->star_rating }}"
                                            data-distance="{{ $h->distance }}"
                                            data-address="{{ $h->address }}"
                                            data-contact_number="{{ $h->contact_number }}"
                                            data-email="{{ $h->email }}"
                                            data-notes="{{ $h->notes }}"
                                            data-status="{{ $h->status ? '1' : '0' }}"
                                            title="Edit Hotel">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form method="POST" action="{{ route('hotels.destroy', $h->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($h->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger mb-0" title="Delete Hotel">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-secondary text-sm">
                                    <i class="fa-solid fa-hotel fa-2x mb-2 d-block text-secondary opacity-5"></i>
                                    No hotels registered yet. Click "Add New Hotel" to register hotels for your itineraries.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($hotels->hasPages())
                <div class="px-4 py-2 border-top d-flex justify-content-between align-items-center">
                    <span class="text-xxs text-secondary">Showing {{ $hotels->firstItem() }} to {{ $hotels->lastItem() }} of {{ $hotels->total() }} hotels</span>
                    {{ $hotels->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Add New Hotel -->
<div class="modal fade" id="addHotelModal" tabindex="-1" aria-labelledby="addHotelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" action="{{ route('hotels.store') }}" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-light border-bottom py-3">
                <h6 class="modal-title font-weight-bold" id="addHotelModalLabel">
                    <i class="fa-solid fa-plus-circle text-primary me-2"></i> Register New Hotel
                </h6>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Hotel Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Pullman Zamzam Makkah" required>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-0">City <span class="text-danger">*</span></label>
                            <a href="javascript:void(0);" class="text-primary text-xxs fw-bold btn-quick-add-city" data-target="#add_hotel_city">
                                <i class="fa-solid fa-plus-circle me-1"></i>New City
                            </a>
                        </div>
                        <select name="city" id="add_hotel_city" class="form-control form-control-sm ajax-city-select2" data-placeholder="Search City in Database..." required>
                            <option value="Makkah" selected>Makkah</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Country</label>
                        <select name="country" id="add_hotel_country" class="form-control form-control-sm ajax-country-select2" data-placeholder="Select Country...">
                            <option value="Saudi Arabia" selected>Saudi Arabia</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Category / Star Rating</label>
                        <select name="star_rating" class="form-select form-select-sm">
                            <option value="5 Star">5 Star (Luxury / Clock Tower)</option>
                            <option value="4 Star" selected>4 Star</option>
                            <option value="3 Star">3 Star</option>
                            <option value="2 Star">2 Star</option>
                            <option value="Standard / Economy">Standard / Economy</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Distance / Proximity to Haram</label>
                        <input type="text" name="distance" class="form-control form-control-sm" placeholder="e.g. 50m from Haram, Clock Tower">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Contact Phone / WhatsApp</label>
                        <input type="text" name="contact_number" class="form-control form-control-sm" placeholder="+966 12 ...">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Reservation Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="reservations@hotel.com">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Address / Landmark</label>
                        <input type="text" name="address" class="form-control form-control-sm" placeholder="Street, Area, Gate proximity">
                    </div>

                    <div class="col-12">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Amenities & Notes</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="e.g. Free Shuttle Bus, Breakfast included, Quad rooms available..."></textarea>
                    </div>

                    <div class="col-12">
                        <div class="slide-switch-container">
                            <label class="slide-switch mb-0">
                                <input type="checkbox" name="status" value="1" id="addHotelStatus" checked>
                                <span class="slider"></span>
                            </label>
                            <label for="addHotelStatus" class="text-xs font-weight-bold text-dark mb-0 cursor-pointer">
                                Active & Available in Itinerary Bookings
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm mb-0 shadow-sm">
                    <i class="fa-solid fa-save me-1"></i> Save Hotel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Hotel -->
<div class="modal fade" id="editHotelModal" tabindex="-1" aria-labelledby="editHotelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" id="editHotelForm" action="" class="modal-content border-0 shadow">
            @csrf
            @method('PUT')
            <div class="modal-header bg-light border-bottom py-3">
                <h6 class="modal-title font-weight-bold" id="editHotelModalLabel">
                    <i class="fa-solid fa-pen-to-square text-info me-2"></i> Edit Hotel Details
                </h6>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Hotel Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-0">City <span class="text-danger">*</span></label>
                            <a href="javascript:void(0);" class="text-primary text-xxs fw-bold btn-quick-add-city" data-target="#edit_city">
                                <i class="fa-solid fa-plus-circle me-1"></i>New City
                            </a>
                        </div>
                        <select name="city" id="edit_city" class="form-control form-control-sm ajax-city-select2" data-placeholder="Search City..." required></select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Country</label>
                        <select name="country" id="edit_country" class="form-control form-control-sm ajax-country-select2" data-placeholder="Select Country..."></select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Category / Star Rating</label>
                        <select name="star_rating" id="edit_star_rating" class="form-select form-select-sm">
                            <option value="5 Star">5 Star (Luxury / Clock Tower)</option>
                            <option value="4 Star">4 Star</option>
                            <option value="3 Star">3 Star</option>
                            <option value="2 Star">2 Star</option>
                            <option value="Standard / Economy">Standard / Economy</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Distance / Proximity to Haram</label>
                        <input type="text" name="distance" id="edit_distance" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Contact Phone / WhatsApp</label>
                        <input type="text" name="contact_number" id="edit_contact_number" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Reservation Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Address / Landmark</label>
                        <input type="text" name="address" id="edit_address" class="form-control form-control-sm">
                    </div>

                    <div class="col-12">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Amenities & Notes</label>
                        <textarea name="notes" id="edit_notes" class="form-control form-control-sm" rows="2"></textarea>
                    </div>

                    <div class="col-12">
                        <div class="slide-switch-container">
                            <label class="slide-switch mb-0">
                                <input type="checkbox" name="status" value="1" id="edit_status">
                                <span class="slider"></span>
                            </label>
                            <label for="edit_status" class="text-xs font-weight-bold text-dark mb-0 cursor-pointer">
                                Active & Available in Itinerary Bookings
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info btn-sm mb-0 shadow-sm">
                    <i class="fa-solid fa-save me-1"></i> Update Hotel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit-hotel');
    const editModal = new bootstrap.Modal(document.getElementById('editHotelModal'));
    const editForm = document.getElementById('editHotelForm');

    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editForm.action = `/hotels/${id}`;

            document.getElementById('edit_name').value = this.getAttribute('data-name') || '';
            document.getElementById('edit_city').value = this.getAttribute('data-city') || '';
            document.getElementById('edit_country').value = this.getAttribute('data-country') || 'Saudi Arabia';
            document.getElementById('edit_star_rating').value = this.getAttribute('data-star_rating') || '4 Star';
            document.getElementById('edit_distance').value = this.getAttribute('data-distance') || '';
            document.getElementById('edit_contact_number').value = this.getAttribute('data-contact_number') || '';
            document.getElementById('edit_email').value = this.getAttribute('data-email') || '';
            document.getElementById('edit_address').value = this.getAttribute('data-address') || '';
            document.getElementById('edit_notes').value = this.getAttribute('data-notes') || '';
            document.getElementById('edit_status').checked = this.getAttribute('data-status') === '1';

            editModal.show();
        });
    });
});
</script>
<!-- Quick Add City Modal -->
<div class="modal fade" id="quickAddCityModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-light border-bottom py-2 px-3">
                <h6 class="modal-title font-weight-bold text-dark mb-0" style="font-size: 0.95rem;">
                    <i class="fa-solid fa-city text-primary me-1"></i> Quick Add New City
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddCityForm">
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label text-xs font-weight-bold mb-1">City Name <span class="text-danger">*</span></label>
                        <input type="text" id="quick_city_name_input" class="form-control form-control-sm" placeholder="e.g. Taif / Yanbu" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-xs font-weight-bold mb-1">Country</label>
                        <input type="text" id="quick_country_name_input" class="form-control form-control-sm" value="Saudi Arabia" placeholder="e.g. Saudi Arabia">
                    </div>
                    <div class="text-xxs text-muted">
                        <i class="fa-solid fa-circle-info me-1"></i> This city will be permanently saved to the database table for future selections.
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-xs mb-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-xs mb-0 shadow-sm" id="btnSaveQuickCity">
                        <i class="fa-solid fa-save me-1"></i> Save City
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
