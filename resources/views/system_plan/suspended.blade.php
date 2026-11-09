<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status['suspended_title'] ?? 'System Subscription Suspended' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="{{ asset('assets/css/soft-ui-dashboard.css') }}" rel="stylesheet" />
    <style>
        :root {
            --primary: #cb0c9f;
            --danger: #ea0606;
            --warning: #f53939;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-600: #475569;
            --slate-100: #f1f5f9;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0b1120 0%, #111827 50%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
            color: #e2e8f0;
        }
        .suspended-wrapper {
            max-width: 680px;
            width: 100%;
            margin: 0 auto;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05);
            padding: 2.75rem;
            position: relative;
            overflow: hidden;
        }
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #ef4444, #dc2626);
        }
        .icon-badge {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: radial-gradient(circle at top left, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 35px rgba(239, 68, 68, 0.25);
            color: #ef4444;
            font-size: 2.25rem;
            animation: pulse-glow 3s infinite ease-in-out;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 25px rgba(239, 68, 68, 0.2); }
            50% { box-shadow: 0 0 45px rgba(239, 68, 68, 0.45); }
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1.25rem;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            box-shadow: 0 0 10px #ef4444;
        }
        .title {
            color: #ffffff;
            font-weight: 800;
            font-size: 1.75rem;
            line-height: 1.25;
            margin-bottom: 0.85rem;
        }
        .lead-text {
            color: #cbd5e1;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .info-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.75rem;
            text-align: left;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.875rem;
        }
        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .info-label {
            color: #94a3b8;
            font-weight: 500;
        }
        .info-val {
            color: #f8fafc;
            font-weight: 600;
        }
        .btn-contact {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff !important;
            border: none;
            border-radius: 12px;
            padding: 0.85rem 1.75rem;
            font-size: 0.95rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
        }
        .btn-contact:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(37, 99, 235, 0.6);
        }
        .btn-whatsapp {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        }
        .btn-whatsapp:hover {
            box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.6);
        }
        .footer-note {
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.8125rem;
            color: #64748b;
        }
        .admin-link {
            color: #94a3b8;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: color 0.15s ease;
        }
        .admin-link:hover {
            color: #f8fafc;
        }
    </style>
</head>
<body>

<div class="suspended-wrapper text-center">

    @if(!empty($isPreview))
    <div class="alert alert-warning text-dark text-start mb-3 border-0 shadow-sm" style="background: #fef3c7; border-radius: 12px;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fa-solid fa-eye me-2 text-warning"></i>
                <strong>Super-Admin Live Preview:</strong> You are previewing the client suspension screen.
            </div>
            <a href="{{ route('integration_settings.edit') }}#system-plan" class="btn btn-xs btn-dark mb-0">
                Back to Settings
            </a>
        </div>
    </div>
    @endif

    <div class="glass-card">
        {{-- Icon --}}
        <div class="icon-badge">
            <i class="fa-solid fa-lock"></i>
        </div>

        {{-- Status badge --}}
        <div>
            <div class="status-pill">
                <span class="status-dot"></span>
                Service Hold &bull; Renewal Required
            </div>
        </div>

        {{-- Title --}}
        <h1 class="title">{{ $status['suspended_title'] ?? 'System Access Temporarily Suspended' }}</h1>

        {{-- Explanatory notice --}}
        <p class="lead-text">
            {{ $status['suspended_message'] }}
        </p>

        {{-- Info Box --}}
        <div class="info-box">
            @if(!empty($status['owner_name']))
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-building me-2"></i>Service Administrator</span>
                <span class="info-val">{{ $status['owner_name'] }}</span>
            </div>
            @endif

            @if(!empty($status['formatted_deadline']))
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-calendar-xmark me-2"></i>Renewal Due Date</span>
                <span class="info-val text-danger">{{ $status['formatted_deadline'] }}</span>
            </div>
            @endif

            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-clock-rotate-left me-2"></i>Status</span>
                <span class="info-val text-warning"><i class="fa-solid fa-hourglass-half me-1"></i>Pending Renewal Confirmation</span>
            </div>

            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-database me-2"></i>Database & Storage</span>
                <span class="info-val text-success"><i class="fa-solid fa-circle-check me-1"></i>Protected & Preserved</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex flex-wrap justify-content-center gap-3">
            @if(!empty($status['owner_whatsapp']))
            @php
                $cleanWa = preg_replace('/[^0-9]/', '', $status['owner_whatsapp']);
                $waText = urlencode("Hello, I am contacting you regarding System Subscription Renewal for " . request()->getHost() . " to restore access.");
            @endphp
            <a href="https://wa.me/{{ $cleanWa }}?text={{ $waText }}" target="_blank" class="btn-contact btn-whatsapp">
                <i class="fa-brands fa-whatsapp fa-lg"></i>
                <span>Contact via WhatsApp</span>
            </a>
            @endif

            @if(!empty($status['owner_phone']))
            <a href="tel:{{ $status['owner_phone'] }}" class="btn-contact">
                <i class="fa-solid fa-phone"></i>
                <span>Call Administrator</span>
            </a>
            @endif

            @if(!empty($status['owner_email']))
            <a href="mailto:{{ $status['owner_email'] }}?subject={{ urlencode('System Renewal Request - ' . request()->getHost()) }}" class="btn-contact" style="background: linear-gradient(135deg, #475569, #334155);">
                <i class="fa-solid fa-envelope"></i>
                <span>Send Email</span>
            </a>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer-note d-flex justify-content-between align-items-center">
            <span>&copy; {{ date('Y') }} All Rights Reserved.</span>
            <a href="{{ url('login') }}" class="admin-link" title="Authorized Administrator Login">
                <i class="fa-solid fa-user-shield"></i>
                <span>Owner Login</span>
            </a>
        </div>
    </div>
</div>

</body>
</html>
