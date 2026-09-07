<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $settings->public_form_title ?? 'Travel & Tour Inquiry' }} - Travel IMS</title>
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 10px;
        }
        .inquiry-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 31, 63, 0.1);
            overflow: hidden;
            max-width: 680px;
            width: 100%;
            border: 1px solid #e2e8f0;
        }
        .form-header {
            background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%);
            color: #ffffff;
            padding: 28px 24px;
            text-align: center;
        }
        .form-label {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4a5568;
            margin-bottom: 4px;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.88rem;
            padding: 8px 12px;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }
        .submit-btn {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            border: none;
            border-radius: 8px;
            font-weight: 700;
            letter-spacing: 0.3px;
            padding: 12px 24px;
            font-size: 0.95rem;
            transition: transform 0.2s;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
        }
        .badge-step {
            background: rgba(255, 255, 255, 0.2);
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="inquiry-card">
        <div class="form-header">
            <span class="badge-step"><i class="fa-solid fa-plane-departure me-1"></i> Fast-Track Online Inquiry</span>
            <h4 class="fw-bold mb-1">{{ $settings->public_form_title ?? 'Book Your Journey' }}</h4>
            <p class="text-white-50 mb-0 small">{{ $settings->public_form_subtitle ?? 'Fill out your requirements below and our dedicated travel consultant will contact you.' }}</p>
        </div>

        <div class="p-4">
            @if(session('success_inquiry'))
                <div class="text-center py-4">
                    <div class="mb-3 text-success">
                        <i class="fa-solid fa-circle-check fa-4x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Thank You, {{ session('success_inquiry')['name'] }}!</h5>
                    <p class="text-muted small mb-3">
                        Your inquiry has been registered successfully under reference 
                        <strong class="text-primary">#INQ-{{ session('success_inquiry')['inquiry_id'] }}</strong>.
                    </p>
                    <p class="text-secondary small">One of our specialist travel agents has been assigned and will call / WhatsApp you shortly.</p>
                    <a href="{{ url('/inquire-now') }}" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fa-solid fa-plus me-1"></i> Submit Another Request
                    </a>
                </div>
            @else
                <form action="{{ route('public.inquiry.submit') }}" method="POST">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert-danger text-xs py-2 px-3 mb-3">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                                <input type="text" class="form-control" name="customer_name" value="{{ old('customer_name') }}" placeholder="e.g. Muhammad Usman" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">WhatsApp / Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-brands fa-whatsapp text-success"></i></span>
                                <input type="tel" class="form-control" name="customer_cell" value="{{ old('customer_cell') }}" placeholder="+92 300 1234567" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control" name="customer_email" value="{{ old('customer_email') }}" placeholder="name@domain.com">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Departure / Current City</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-location-dot text-muted"></i></span>
                                <input type="text" class="form-control" name="city" value="{{ old('city') }}" placeholder="e.g. Karachi, Lahore, Islamabad">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Service / Package Interest <span class="text-danger">*</span></label>
                            <select class="form-select" name="service_type" required>
                                <option value="" disabled selected>Select Travel Service...</option>
                                @foreach($inquiryTypes as $type)
                                    <option value="{{ $type->type_name }}" {{ old('service_type') == $type->type_name ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Target Travel Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-calendar text-muted"></i></span>
                                <input type="date" class="form-control" name="travel_date" value="{{ old('travel_date') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-4">
                            <label class="form-label">Adults (12+)</label>
                            <input type="number" class="form-control" name="adults" min="1" max="50" value="{{ old('adults', 1) }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Children (2-11)</label>
                            <input type="number" class="form-control" name="children" min="0" max="50" value="{{ old('children', 0) }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Infants (<2)</label>
                            <input type="number" class="form-control" name="infants" min="0" max="20" value="{{ old('infants', 0) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Special Requirements / Hotel Preferences</label>
                        <textarea class="form-control" name="requirements" rows="3" placeholder="Specify hotel star rating, room types (Kaaba view, Quad/Triple), flight preferences, or budget range...">{{ old('requirements') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 text-white submit-btn shadow-sm">
                        <i class="fa-solid fa-paper-plane me-2"></i> Submit Inquiry Now
                    </button>
                    <p class="text-center text-muted text-xxs mt-3 mb-0" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-lock text-success me-1"></i> Your personal details are confidential and will only be used to process your travel itinerary.
                    </p>
                </form>
            @endif
        </div>
    </div>
</body>
</html>
