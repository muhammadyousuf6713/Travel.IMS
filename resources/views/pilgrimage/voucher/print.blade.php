<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Voucher & Bill - {{ $voucherNo }} - {{ $booking->leader_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --primary-blue: #1e3a8a;
            --accent-blue: #2563eb;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --border-color: #e2e8f0;
            --bg-subtle: #f8fafc;
            --currency-sym: "SAR";
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: var(--text-dark);
            line-height: 1.45;
            padding: 24px 16px;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Action Toolbar */
        .no-print-bar {
            max-width: 880px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            background: #ffffff;
            padding: 12px 18px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .toolbar-left, .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-tool {
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #334155;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .btn-tool:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .btn-primary-tool {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        .btn-primary-tool:hover {
            background: #1d4ed8;
            color: #ffffff;
            border-color: #1d4ed8;
        }

        .btn-secondary-tool {
            background: #f8fafc;
            color: #475569;
        }

        .edit-badge {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .currency-select-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .currency-select-wrap select {
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            cursor: pointer;
        }

        .toggle-switch-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            user-select: none;
        }

        .toggle-switch-wrap input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
        }

        /* Document Sheet */
        .voucher-card {
            max-width: 880px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            padding: 40px 45px;
            border: 1px solid #e2e8f0;
            position: relative;
        }

        /* Top Sovereign Accent Line */
        .voucher-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        /* Live Editable Hover & Focus Styles */
        [contenteditable="true"] {
            transition: background 0.15s, outline 0.15s;
            border-radius: 3px;
            padding: 1px 3px;
        }

        [contenteditable="true"]:hover {
            background-color: #f0f7ff;
            outline: 1px dashed #93c5fd;
            cursor: text;
        }

        [contenteditable="true"]:focus {
            background-color: #eff6ff;
            outline: 2px solid #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Voucher Header */
        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 24px;
            gap: 20px;
        }

        .agency-info {
            flex: 1;
        }

        .agency-info h2 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .agency-info .sub-tagline {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #2563eb;
            margin-bottom: 6px;
        }

        .agency-info p {
            font-size: 12px;
            color: #475569;
            line-height: 1.45;
        }

        .voucher-title-box {
            text-align: right;
            min-width: 280px;
        }

        .voucher-badge {
            background-color: #f1f5f9;
            color: #1e293b;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 6px;
            border: 1px solid #cbd5e1;
        }

        .voucher-title-box h1 {
            font-size: 19px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }

        .voucher-no-line {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-top: 2px;
        }

        .voucher-no-line strong {
            color: #2563eb;
            font-size: 14px;
        }

        .voucher-issue-date {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
        }

        /* Meta Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 22px;
        }

        .meta-item .label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
            letter-spacing: 0.6px;
        }

        .meta-item .value {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
        }

        /* Accompanying Pilgrims */
        .travelers-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 22px;
        }

        .travelers-heading {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .travelers-list {
            font-size: 12px;
            color: #334155;
            line-height: 1.6;
        }

        /* Section Headings */
        .section-heading-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-heading {
            font-size: 12.5px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-heading i {
            color: #2563eb;
        }

        /* Sober Executive Tables */
        .table-doc {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .table-doc th {
            background-color: #0f172a;
            color: #ffffff;
            text-align: left;
            padding: 10px 12px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10.5px;
            letter-spacing: 0.6px;
        }

        .table-doc td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }

        .table-doc tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .table-doc .hotel-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 12.5px;
        }

        .table-doc .city-tag {
            font-size: 10px;
            font-weight: 700;
            background-color: #eff6ff;
            color: #1e40af;
            padding: 2px 7px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 3px;
            border: 1px solid #bfdbfe;
        }

        .table-doc .nights-pill {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            border: 1px solid #cbd5e1;
        }

        .table-doc .num-col {
            text-align: right;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
        }

        .btn-row-action {
            background: transparent;
            border: none;
            color: #dc2626;
            cursor: pointer;
            padding: 4px 6px;
            font-size: 12px;
            border-radius: 4px;
            transition: background 0.15s;
        }

        .btn-row-action:hover {
            background: #fee2e2;
        }

        .btn-add-row {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #2563eb;
            font-size: 11.5px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 20px;
            transition: all 0.15s;
        }

        .btn-add-row:hover {
            background: #eff6ff;
            border-color: #93c5fd;
        }

        /* Billing Breakdown Box */
        .billing-container {
            margin-bottom: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            overflow: hidden;
        }

        .billing-header {
            background: #f8fafc;
            padding: 12px 18px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .billing-header-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .billing-body {
            padding: 16px 18px;
        }

        .billing-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 24px;
        }

        .bill-items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .bill-items-table th {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            padding: 6px 8px;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }

        .bill-items-table td {
            padding: 8px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .bill-totals-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #475569;
        }

        .total-row.grand-total {
            border-top: 2px solid #0f172a;
            padding-top: 10px;
            margin-top: 4px;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        .total-row.balance-row {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13.5px;
            font-weight: 800;
            color: #1e3a8a;
            margin-top: 6px;
        }

        .total-row.balance-row.paid-full {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        /* 24/7 Operations Box */
        .ops-contact-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .ops-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #1e3a8a;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .ops-contacts {
            display: flex;
            gap: 20px;
            font-size: 11.5px;
            color: #334155;
            font-weight: 600;
            flex-wrap: wrap;
        }

        .ops-contacts span strong {
            color: #0f172a;
        }

        /* Terms & Notes Box */
        .terms-notes-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            margin-bottom: 22px;
        }

        .policy-box-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #334155;
            margin-bottom: 8px;
            letter-spacing: 0.6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .terms-list {
            font-size: 10px;
            color: #64748b;
            padding-left: 14px;
            line-height: 1.5;
        }

        .terms-list li {
            margin-bottom: 3px;
        }

        .instructions-box {
            background: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 10.5px;
            color: #4b5563;
            line-height: 1.5;
        }

        /* Signature & Seal Block */
        .signatures-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
        }

        .sig-block {
            text-align: center;
        }

        .sig-line {
            height: 50px;
            border-bottom: 1px solid #94a3b8;
            margin-bottom: 6px;
        }

        .sig-caption {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
        }

        .sig-sub {
            font-size: 9.5px;
            color: #94a3b8;
        }

        /* Footer Audit Line */
        .voucher-footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 24px;
            padding-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10.5px;
            color: #64748b;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                color: #000000;
            }

            .no-print-bar, .btn-add-row, .btn-row-action, .hide-on-print {
                display: none !important;
            }

            .voucher-card {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
                border-radius: 0;
            }

            .voucher-card::before {
                display: none;
            }

            [contenteditable="true"] {
                outline: none !important;
                background: transparent !important;
            }

            .table-doc th {
                background-color: #0f172a !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .meta-grid, .ops-contact-box, .bill-totals-box, .instructions-box {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 12mm 12mm 12mm;
            }
        }
    </style>
</head>
<body>

    <!-- Non-Print Floating Toolbar -->
    <div class="no-print-bar">
        <div class="toolbar-left">
            <a href="{{ route('pilgrimage.index') }}" class="btn-tool">
                <i class="fa-solid fa-arrow-left"></i> Back to Operations
            </a>
            <span class="edit-badge">
                <i class="fa-solid fa-pen-nib"></i> Live Editable Mode
            </span>
            <span style="font-size: 11.5px; color: #64748b;">
                Click any text or number to edit in-place
            </span>
        </div>

        <div class="toolbar-right">
            <!-- Currency Selector -->
            <div class="currency-select-wrap">
                <span>Currency:</span>
                <select id="currencyPicker">
                    @if(isset($currencies) && count($currencies) > 0)
                        @foreach($currencies as $cur)
                            <option value="{{ $cur->code }}" data-symbol="{{ $cur->symbol }}" data-rate="{{ $cur->exchange_rate }}" {{ $cur->code == 'SAR' ? 'selected' : '' }}>
                                {{ $cur->code }} ({{ $cur->symbol ?: $cur->code }})
                            </option>
                        @endforeach
                    @else
                        <option value="SAR" data-symbol="SAR" data-rate="75.50" selected>SAR (SR)</option>
                        <option value="PKR" data-symbol="Rs" data-rate="1.00">PKR (Rs)</option>
                        <option value="USD" data-symbol="$" data-rate="280.00">USD ($)</option>
                        <option value="AED" data-symbol="AED" data-rate="76.20">AED</option>
                    @endif
                </select>
            </div>

            <!-- Include Rates / Billing Toggle -->
            <label class="toggle-switch-wrap" title="Show or hide rates & billing table on voucher">
                <input type="checkbox" id="toggleBillingCheck" checked>
                <span>Show Rates & Bill</span>
            </label>

            <!-- Reset to Defaults -->
            <button type="button" class="btn-tool btn-secondary-tool" id="btnResetDraft" title="Revert edits back to original system data">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </button>

            <!-- Print / PDF -->
            <button type="button" onclick="window.print()" class="btn-tool btn-primary-tool">
                <i class="fa-solid fa-print"></i> Print Voucher / PDF
            </button>
        </div>
    </div>

    <!-- Official Voucher Document Card -->
    <div class="voucher-card" id="voucherDocument">

        <!-- Voucher Header -->
        <div class="voucher-header">
            <div class="agency-info">
                <h2 contenteditable="true" id="agencyName">{{ $orgSetting->name ?? 'FLUGZEIT AVIATION & TRAVEL' }}</h2>
                <div class="sub-tagline" contenteditable="true">Licensed International Tour Operator &bull; Umrah & Hajj Services</div>
                <p contenteditable="true" id="agencyAddress">{{ $orgSetting->address ?? 'Main Executive Office, Blue Area, Islamabad' }}</p>
                <p contenteditable="true" id="agencyContact">
                    Phone: {{ $orgSetting->phone ?? '+92 300 0000000' }} &bull; Email: {{ $orgSetting->email ?? 'operations@travelims.com' }}
                    @if(!empty($orgSetting->website)) &bull; Web: {{ $orgSetting->website }} @endif
                </p>
            </div>

            <div class="voucher-title-box">
                <span class="voucher-badge" contenteditable="true">CONFIRMED RESERVATION VOUCHER</span>
                <h1 contenteditable="true">ACCOMMODATION & SERVICE VOUCHER</h1>
                <div class="voucher-no-line">
                    Voucher No: <strong contenteditable="true" id="voucherNoDisplay">{{ $voucherNo }}</strong>
                </div>
                <div class="voucher-issue-date">
                    Issue Date: <span contenteditable="true" id="issueDateDisplay">{{ $booking->submitted_at ? $booking->submitted_at->format('d M Y') : $printDate->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div class="meta-item">
                <div class="label">Guest / Lead Traveler</div>
                <div class="value" contenteditable="true" id="leadTraveler">{{ $booking->leader_name }}</div>
                @if($booking->leader_passport_no)
                    <div style="font-size: 10px; color: #64748b; margin-top: 2px;" contenteditable="true">Pass: {{ $booking->leader_passport_no }}</div>
                @endif
            </div>

            <div class="meta-item">
                <div class="label">Total Passengers</div>
                <div class="value" contenteditable="true" id="totalPax">
                    {{ $booking->total_persons }} Pax ({{ $booking->total_persons == 1 ? '1 Adult' : '1 Leader + ' . ($booking->total_persons - 1) . ' Travelers' }})
                </div>
            </div>

            <div class="meta-item">
                <div class="label">Service Category</div>
                <div class="value text-uppercase" style="color: #1e3a8a;" contenteditable="true" id="serviceCategory">
                    {{ $booking->service_type }}
                </div>
            </div>

            <div class="meta-item">
                <div class="label">Booking Reference</div>
                <div class="value" style="color: #2563eb;" contenteditable="true" id="bookingRef">
                    CRM-{{ $booking->crm_id }}
                </div>
            </div>
        </div>

        <!-- Co-Travelers Box -->
        <div class="travelers-box" id="travelersBox">
            <div class="travelers-heading">
                <span><i class="fa-solid fa-users text-primary me-1"></i> Accompanying Travelers (Pax List):</span>
                <button type="button" class="btn-tool btn-secondary-tool hide-on-print" id="btnAddTraveler" style="padding: 2px 8px; font-size: 11px;">
                    <i class="fa-solid fa-plus"></i> Add Traveler
                </button>
            </div>
            <div class="travelers-list" contenteditable="true" id="travelersList">
                @if($booking->pilgrims->isNotEmpty())
                    @foreach($booking->pilgrims as $pIndex => $p)
                        <strong>{{ $pIndex + 1 }}.</strong> {{ $p->name }} @if($p->passport_no) <span style="color:#64748b;">(Passport: {{ $p->passport_no }})</span>@endif @if(!$loop->last) &bull; @endif
                    @endforeach
                @else
                    <span class="text-muted">No accompanying pilgrims listed. Click to add passenger names or passport details.</span>
                @endif
            </div>
        </div>

        <!-- Hotel Accommodation Table -->
        <div class="section-heading-bar">
            <div class="section-heading">
                <i class="fa-solid fa-hotel"></i> Confirmed Hotel Accommodation Schedule
            </div>
        </div>

        <table class="table-doc" id="hotelStaysTable">
            <thead>
                <tr>
                    <th style="width: 25%;">Hotel & City</th>
                    <th style="width: 13%;">Check-In</th>
                    <th style="width: 13%;">Check-Out</th>
                    <th style="width: 9%; text-align: center;">Nights</th>
                    <th style="width: 13%;">Room Type</th>
                    <th style="width: 7%; text-align: center;">Qty</th>
                    <th class="bill-col" style="width: 10%; text-align: right;">Rate / Nt</th>
                    <th class="bill-col" style="width: 10%; text-align: right;">Total (<span class="currency-label">SAR</span>)</th>
                    <th class="hide-on-print" style="width: 5%; text-align: center;"></th>
                </tr>
            </thead>
            <tbody id="hotelStaysBody">
                @forelse($booking->hotels as $hIndex => $hotel)
                    @php
                        $nights = $hotel->nights ?: 1;
                        $qty = $hotel->room_qty ?: 1;
                        $rate = 0; // Default rate to be set by user
                        $total = $nights * $qty * $rate;
                    @endphp
                    <tr class="hotel-stay-row">
                        <td>
                            <div class="hotel-name" contenteditable="true">{{ $hotel->hotel_name }}</div>
                            <span class="city-tag" contenteditable="true">{{ $hotel->city }}</span>
                            @if($hotel->voucher_no)
                                <div style="font-size: 10px; color: #64748b; margin-top: 2px;" contenteditable="true">Conf Ref: {{ $hotel->voucher_no }}</div>
                            @endif
                        </td>
                        <td>
                            <strong contenteditable="true">{{ $hotel->check_in ? $hotel->check_in->format('d M Y') : '—' }}</strong>
                            <div style="font-size: 10px; color: #64748b;" contenteditable="true">Check-in: 16:00</div>
                        </td>
                        <td>
                            <strong contenteditable="true">{{ $hotel->check_out ? $hotel->check_out->format('d M Y') : '—' }}</strong>
                            <div style="font-size: 10px; color: #64748b;" contenteditable="true">Check-out: 12:00</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="nights-pill" contenteditable="true" data-role="nights">{{ $nights }}</span>
                        </td>
                        <td>
                            <strong contenteditable="true">{{ $hotel->room_type }}</strong>
                        </td>
                        <td style="text-align: center;">
                            <strong contenteditable="true" data-role="qty">{{ $qty }}</strong>
                        </td>
                        <td class="bill-col num-col" contenteditable="true" data-role="rate">{{ number_format($rate, 2) }}</td>
                        <td class="bill-col num-col" style="color: #0f172a;" data-role="total">{{ number_format($total, 2) }}</td>
                        <td class="hide-on-print" style="text-align: center;">
                            <button type="button" class="btn-row-action btn-del-hotel" title="Remove Stay"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr class="hotel-stay-row">
                        <td>
                            <div class="hotel-name" contenteditable="true">Pullman Zamzam Makkah</div>
                            <span class="city-tag" contenteditable="true">Makkah</span>
                        </td>
                        <td><strong contenteditable="true">10 Nov 2026</strong></td>
                        <td><strong contenteditable="true">15 Nov 2026</strong></td>
                        <td style="text-align: center;"><span class="nights-pill" contenteditable="true" data-role="nights">5</span></td>
                        <td><strong contenteditable="true">Double Room</strong></td>
                        <td style="text-align: center;"><strong contenteditable="true" data-role="qty">1</strong></td>
                        <td class="bill-col num-col" contenteditable="true" data-role="rate">650.00</td>
                        <td class="bill-col num-col" data-role="total">3,250.00</td>
                        <td class="hide-on-print" style="text-align: center;">
                            <button type="button" class="btn-row-action btn-del-hotel" title="Remove Stay"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <button type="button" class="btn-add-row hide-on-print" id="btnAddHotelRow">
            <i class="fa-solid fa-plus-circle"></i> Add Hotel Accommodation Leg
        </button>

        <!-- Rates & Billing Statement Section -->
        <div class="billing-container" id="billingSection">
            <div class="billing-header">
                <div class="billing-header-title">
                    <i class="fa-solid fa-file-invoice-dollar text-primary"></i> Rates & Financial Billing Statement
                </div>
                <div style="font-size: 11px; color: #64748b;">
                    Currency: <strong class="currency-label text-dark">SAR</strong> &bull; All rates customizable
                </div>
            </div>

            <div class="billing-body">
                <div class="billing-grid">
                    <!-- Billing Line Items -->
                    <div>
                        <table class="bill-items-table" id="billItemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 48%;">Service / Item Description</th>
                                    <th style="width: 25%;">Remarks</th>
                                    <th style="width: 20%; text-align: right;">Amount (<span class="currency-label">SAR</span>)</th>
                                    <th class="hide-on-print" style="width: 7%;"></th>
                                </tr>
                            </thead>
                            <tbody id="billItemsBody">
                                <tr class="bill-item-row" data-locked="hotel">
                                    <td>
                                        <strong contenteditable="true">Hotel Accommodation Total</strong>
                                    </td>
                                    <td>
                                        <span style="font-size: 11px; color: #64748b;" contenteditable="true">Confirmed Hotel Stays</span>
                                    </td>
                                    <td class="num-col" contenteditable="true" data-role="item-amount" id="hotelSubtotalAmount">0.00</td>
                                    <td class="hide-on-print"></td>
                                </tr>
                                <tr class="bill-item-row">
                                    <td>
                                        <strong contenteditable="true">Transport & Ground Transfers</strong>
                                    </td>
                                    <td>
                                        <span style="font-size: 11px; color: #64748b;" contenteditable="true">Private GMC / Coaster Transfers</span>
                                    </td>
                                    <td class="num-col" contenteditable="true" data-role="item-amount">0.00</td>
                                    <td class="hide-on-print" style="text-align: center;">
                                        <button type="button" class="btn-row-action btn-del-bill-item"><i class="fa-solid fa-xmark"></i></button>
                                    </td>
                                </tr>
                                <tr class="bill-item-row">
                                    <td>
                                        <strong contenteditable="true">Visa Processing & Insurance</strong>
                                    </td>
                                    <td>
                                        <span style="font-size: 11px; color: #64748b;" contenteditable="true">Saudi Tourist / Umrah eVisa</span>
                                    </td>
                                    <td class="num-col" contenteditable="true" data-role="item-amount">0.00</td>
                                    <td class="hide-on-print" style="text-align: center;">
                                        <button type="button" class="btn-row-action btn-del-bill-item"><i class="fa-solid fa-xmark"></i></button>
                                    </td>
                                </tr>
                                <tr class="bill-item-row">
                                    <td>
                                        <strong contenteditable="true">Ziyarat & Historical Site Tours</strong>
                                    </td>
                                    <td>
                                        <span style="font-size: 11px; color: #64748b;" contenteditable="true">Makkah & Madinah Guided Tours</span>
                                    </td>
                                    <td class="num-col" contenteditable="true" data-role="item-amount">0.00</td>
                                    <td class="hide-on-print" style="text-align: center;">
                                        <button type="button" class="btn-row-action btn-del-bill-item"><i class="fa-solid fa-xmark"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="button" class="btn-add-row hide-on-print" id="btnAddBillItem" style="margin-top: 10px; margin-bottom: 0;">
                            <i class="fa-solid fa-plus"></i> Add Line Item
                        </button>
                    </div>

                    <!-- Totals & Balances Summary Card -->
                    <div>
                        <div class="bill-totals-box">
                            <div class="total-row">
                                <span>Gross Bill Subtotal:</span>
                                <strong><span class="currency-label">SAR</span> <span id="billGrossSubtotal">0.00</span></strong>
                            </div>

                            <div class="total-row">
                                <span contenteditable="true">Tax / Service Surcharge:</span>
                                <div>
                                    <span class="currency-label">SAR</span>
                                    <span contenteditable="true" id="billTax" class="num-col">0.00</span>
                                </div>
                            </div>

                            <div class="total-row">
                                <span contenteditable="true">Discount / Courtesy Allowance:</span>
                                <div>
                                    -<span class="currency-label">SAR</span>
                                    <span contenteditable="true" id="billDiscount" class="num-col" style="color: #059669;">0.00</span>
                                </div>
                            </div>

                            <div class="total-row grand-total">
                                <span>NET TOTAL BILL:</span>
                                <span style="color: #2563eb;"><span class="currency-label">SAR</span> <span id="billGrandTotal">0.00</span></span>
                            </div>

                            <div class="total-row" style="margin-top: 4px;">
                                <span contenteditable="true">Advance / Amount Received:</span>
                                <div>
                                    <span class="currency-label">SAR</span>
                                    <span contenteditable="true" id="billAdvance" class="num-col" style="font-weight: 700;">0.00</span>
                                </div>
                            </div>

                            <div class="total-row balance-row" id="balanceRow">
                                <span>BALANCE DUE:</span>
                                <span><span class="currency-label">SAR</span> <span id="billBalanceDue">0.00</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operations Emergency Hotlines -->
        <div class="ops-contact-box">
            <div>
                <div class="ops-title"><i class="fa-solid fa-headset me-1"></i> 24/7 Operations Desk & Emergency Ground Hotlines</div>
                <div class="ops-contacts">
                    <span>Makkah: <strong contenteditable="true">{{ $settings->makkah_ops_contact ?? '+966 50 123 4567' }}</strong></span>
                    <span>Madinah: <strong contenteditable="true">{{ $settings->madinah_ops_contact ?? '+966 50 765 4321' }}</strong></span>
                    <span>Jeddah Airport: <strong contenteditable="true">{{ $settings->jeddah_ops_contact ?? '+966 50 999 8888' }}</strong></span>
                </div>
            </div>
            <div style="text-align: right; font-size: 11px; color: #1e3a8a; font-weight: 700;">
                Executive Hotline: <span contenteditable="true">{{ $settings->general_hotline ?? '+92 300 1234567' }}</span>
            </div>
        </div>

        <!-- Terms & Instructions Grid -->
        <div class="terms-notes-grid">
            <div>
                <div class="policy-box-title"><i class="fa-solid fa-shield-halved text-secondary"></i> Standard Reservation Terms:</div>
                <ol class="terms-list" contenteditable="true" id="termsList">
                    <li>Standard check-in time is after 04:00 PM; check-out is before 12:00 Noon as per Saudi hotel policy.</li>
                    <li>Original passports and valid visas must be presented upon hotel check-in.</li>
                    <li>Early check-in and late check-out are subject to availability and room charges.</li>
                    <li>Hotel star categories are certified by Saudi tourism regulatory standards.</li>
                    <li>Room categories (Double, Triple, Quad) follow hotel specifications; requests are fulfilled subject to availability.</li>
                    <li>Confirmed bookings are strictly non-refundable and non-transferable once vouchers are issued.</li>
                </ol>
            </div>

            <div>
                <div class="policy-box-title"><i class="fa-solid fa-circle-info text-secondary"></i> Special Guest Instructions:</div>
                <div class="instructions-box" contenteditable="true" id="specialNotes">
                    &bull; Private airport pickup will meet passenger outside the arrival terminal holding a placard with passenger's name.<br>
                    &bull; Daily breakfast buffet is included at the main hotel restaurant.<br>
                    &bull; For any ground assistance or room extensions, contact the local Makkah/Madinah hotlines listed above.<br>
                    &bull; Non-smoking rooms requested as priority.
                </div>
            </div>
        </div>

        <!-- Signature & Official Seal Box -->
        <div class="signatures-grid">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-caption" contenteditable="true">Passenger / Guest Acceptance</div>
                <div class="sig-sub" contenteditable="true">Signature & Date</div>
            </div>

            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-caption" contenteditable="true">Authorized Operations Signature</div>
                <div class="sig-sub" contenteditable="true">{{ $booking->submittingAgent->name ?? $user->name }} &bull; Official Seal</div>
            </div>
        </div>

        <!-- Footer Audit Line -->
        <div class="voucher-footer">
            <span contenteditable="true">
                This is a verified official system document. Prepared by <strong>{{ $booking->submittingAgent->name ?? $user->name }}</strong> on {{ $printDate->format('d M Y, H:i') }}.
            </span>
            <span contenteditable="true">Verification ID: {{ $voucherNo }}</span>
        </div>

    </div>

    <!-- Live Interactivity & Formatting Script -->
    <script>
        (function() {
            var bookingId = {{ $booking->id }};
            var DRAFT_KEY = 'ims_voucher_draft_' + bookingId;

            var currencyPicker = document.getElementById('currencyPicker');
            var toggleBilling = document.getElementById('toggleBillingCheck');
            var billingSection = document.getElementById('billingSection');
            var btnReset = document.getElementById('btnResetDraft');

            function fmt(num) {
                var val = parseFloat(num);
                if (isNaN(val)) val = 0;
                return val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function parseNum(str) {
                if (!str) return 0;
                var cleaned = String(str).replace(/[^0-9.-]/g, '');
                var val = parseFloat(cleaned);
                return isNaN(val) ? 0 : val;
            }

            // Recalculate all hotel rows, bill items, totals, and balance due
            window.recalculateTotals = function() {
                var hotelSubtotal = 0;

                // 1. Calculate each hotel stay row
                var hotelRows = document.querySelectorAll('.hotel-stay-row');
                hotelRows.forEach(function(row) {
                    var nightsEl = row.querySelector('[data-role="nights"]');
                    var qtyEl = row.querySelector('[data-role="qty"]');
                    var rateEl = row.querySelector('[data-role="rate"]');
                    var totalEl = row.querySelector('[data-role="total"]');

                    var nights = parseNum(nightsEl ? nightsEl.innerText : 1);
                    var qty = parseNum(qtyEl ? qtyEl.innerText : 1);
                    var rate = parseNum(rateEl ? rateEl.innerText : 0);

                    if (nights < 1) nights = 1;
                    if (qty < 1) qty = 1;

                    var rowTotal = nights * qty * rate;
                    if (totalEl) totalEl.innerText = fmt(rowTotal);
                    hotelSubtotal += rowTotal;
                });

                // Update hotel subtotal row in billing table
                var hotelSubEl = document.getElementById('hotelSubtotalAmount');
                if (hotelSubEl) {
                    hotelSubEl.innerText = fmt(hotelSubtotal);
                }

                // 2. Sum all billing items
                var grossBill = 0;
                var billRows = document.querySelectorAll('.bill-item-row');
                billRows.forEach(function(r) {
                    var amtEl = r.querySelector('[data-role="item-amount"]');
                    if (amtEl) {
                        grossBill += parseNum(amtEl.innerText);
                    }
                });

                var billGrossEl = document.getElementById('billGrossSubtotal');
                if (billGrossEl) billGrossEl.innerText = fmt(grossBill);

                // 3. Tax and Discount
                var taxEl = document.getElementById('billTax');
                var discountEl = document.getElementById('billDiscount');
                var tax = parseNum(taxEl ? taxEl.innerText : 0);
                var discount = parseNum(discountEl ? discountEl.innerText : 0);

                var netTotal = (grossBill + tax) - discount;
                if (netTotal < 0) netTotal = 0;

                var grandTotalEl = document.getElementById('billGrandTotal');
                if (grandTotalEl) grandTotalEl.innerText = fmt(netTotal);

                // 4. Advance and Balance Due
                var advanceEl = document.getElementById('billAdvance');
                var advance = parseNum(advanceEl ? advanceEl.innerText : 0);
                var balance = netTotal - advance;

                var balanceEl = document.getElementById('billBalanceDue');
                var balanceRow = document.getElementById('balanceRow');
                if (balanceEl) {
                    balanceEl.innerText = fmt(balance);
                }

                if (balanceRow) {
                    if (balance <= 0 && netTotal > 0) {
                        balanceRow.classList.add('paid-full');
                        var labelSpan = balanceRow.querySelector('span:first-child');
                        if (labelSpan) labelSpan.innerText = 'STATUS: FULLY PAID';
                    } else {
                        balanceRow.classList.remove('paid-full');
                        var labelSpan = balanceRow.querySelector('span:first-child');
                        if (labelSpan) labelSpan.innerText = 'BALANCE DUE:';
                    }
                }

                saveDraft();
            };

            // Currency Labels
            function updateCurrencyLabels(curr) {
                document.querySelectorAll('.currency-label').forEach(function(el) {
                    el.innerText = curr;
                });
                saveDraft();
            }

            if (currencyPicker) {
                currencyPicker.addEventListener('change', function() {
                    updateCurrencyLabels(this.value);
                });
            }

            // Toggle Billing visibility
            function setBillingVisible(show) {
                if (billingSection) {
                    billingSection.style.display = show ? 'block' : 'none';
                }
                document.querySelectorAll('.bill-col').forEach(function(col) {
                    col.style.display = show ? '' : 'none';
                });
                saveDraft();
            }

            if (toggleBilling) {
                toggleBilling.addEventListener('change', function() {
                    setBillingVisible(this.checked);
                });
            }

            // Real-time calculation on typing (Event Delegation on Document)
            document.addEventListener('input', function(e) {
                var t = e.target;
                if (!t) return;
                if (t.dataset && (
                    t.dataset.role === 'rate' ||
                    t.dataset.role === 'nights' ||
                    t.dataset.role === 'qty' ||
                    t.dataset.role === 'item-amount' ||
                    t.id === 'billTax' ||
                    t.id === 'billDiscount' ||
                    t.id === 'billAdvance'
                )) {
                    window.recalculateTotals();
                } else if (t.closest && t.closest('#voucherDocument')) {
                    saveDraft();
                }
            });

            // Global Event Delegation for Row Removal
            document.addEventListener('click', function(e) {
                // Delete Hotel Row
                var btnHotel = e.target.closest('.btn-del-hotel');
                if (btnHotel) {
                    var row = btnHotel.closest('tr');
                    if (row) {
                        row.remove();
                        window.recalculateTotals();
                    }
                    return;
                }

                // Delete Bill Item
                var btnBill = e.target.closest('.btn-del-bill-item');
                if (btnBill) {
                    var bRow = btnBill.closest('tr');
                    if (bRow && bRow.dataset.locked !== 'hotel') {
                        bRow.remove();
                        window.recalculateTotals();
                    }
                    return;
                }
            });

            // Add Hotel Row
            var btnAddHotel = document.getElementById('btnAddHotelRow');
            if (btnAddHotel) {
                btnAddHotel.addEventListener('click', function() {
                    var tbody = document.getElementById('hotelStaysBody');
                    if (!tbody) return;
                    var tr = document.createElement('tr');
                    tr.className = 'hotel-stay-row';
                    tr.innerHTML = '<td>' +
                        '<div class="hotel-name" contenteditable="true">Hotel Name</div>' +
                        '<span class="city-tag" contenteditable="true">City</span>' +
                        '</td>' +
                        '<td><strong contenteditable="true">DD MMM YYYY</strong><div style="font-size: 10px; color: #64748b;" contenteditable="true">Check-in: 16:00</div></td>' +
                        '<td><strong contenteditable="true">DD MMM YYYY</strong><div style="font-size: 10px; color: #64748b;" contenteditable="true">Check-out: 12:00</div></td>' +
                        '<td style="text-align: center;"><span class="nights-pill" contenteditable="true" data-role="nights">3</span></td>' +
                        '<td><strong contenteditable="true">Double Room</strong></td>' +
                        '<td style="text-align: center;"><strong contenteditable="true" data-role="qty">1</strong></td>' +
                        '<td class="bill-col num-col" contenteditable="true" data-role="rate">0.00</td>' +
                        '<td class="bill-col num-col" data-role="total">0.00</td>' +
                        '<td class="hide-on-print" style="text-align: center;">' +
                        '<button type="button" class="btn-row-action btn-del-hotel" title="Remove Stay"><i class="fa-solid fa-trash-can"></i></button>' +
                        '</td>';
                    tbody.appendChild(tr);
                    setBillingVisible(toggleBilling ? toggleBilling.checked : true);
                    window.recalculateTotals();
                });
            }

            // Add Billing Item
            var btnAddBill = document.getElementById('btnAddBillItem');
            if (btnAddBill) {
                btnAddBill.addEventListener('click', function() {
                    var tbody = document.getElementById('billItemsBody');
                    if (!tbody) return;
                    var tr = document.createElement('tr');
                    tr.className = 'bill-item-row';
                    tr.innerHTML = '<td><strong contenteditable="true">Additional Service</strong></td>' +
                        '<td><span style="font-size: 11px; color: #64748b;" contenteditable="true">Description / Remarks</span></td>' +
                        '<td class="num-col" contenteditable="true" data-role="item-amount">0.00</td>' +
                        '<td class="hide-on-print" style="text-align: center;">' +
                        '<button type="button" class="btn-row-action btn-del-bill-item"><i class="fa-solid fa-xmark"></i></button>' +
                        '</td>';
                    tbody.appendChild(tr);
                    window.recalculateTotals();
                });
            }

            // Add Traveler Quick Button
            var btnAddTraveler = document.getElementById('btnAddTraveler');
            if (btnAddTraveler) {
                btnAddTraveler.addEventListener('click', function() {
                    var list = document.getElementById('travelersList');
                    if (!list) return;
                    var current = list.innerHTML.trim();
                    var newPassenger = ' &bull; <strong>Passenger Name</strong> <span style="color:#64748b;">(Pass: Passport#)</span>';
                    if (current.includes('No accompanying pilgrims')) {
                        list.innerHTML = '<strong>1.</strong> Passenger Name <span style="color:#64748b;">(Pass: Passport#)</span>';
                    } else {
                        list.innerHTML = current + newPassenger;
                    }
                    saveDraft();
                });
            }

            // Save Draft
            function saveDraft() {
                try {
                    var doc = document.getElementById('voucherDocument');
                    if (!doc) return;
                    var draft = {
                        html: doc.innerHTML,
                        currency: currencyPicker ? currencyPicker.value : 'SAR',
                        showBilling: toggleBilling ? toggleBilling.checked : true
                    };
                    localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
                } catch(e) {}
            }

            // Restore Draft
            function restoreDraft() {
                try {
                    var raw = localStorage.getItem(DRAFT_KEY);
                    if (raw) {
                        var draft = JSON.parse(raw);
                        if (draft.html) {
                            var doc = document.getElementById('voucherDocument');
                            if (doc) doc.innerHTML = draft.html;
                        }
                        if (draft.currency && currencyPicker) {
                            currencyPicker.value = draft.currency;
                            updateCurrencyLabels(draft.currency);
                        }
                        if (typeof draft.showBilling !== 'undefined' && toggleBilling) {
                            toggleBilling.checked = draft.showBilling;
                            setBillingVisible(draft.showBilling);
                        }
                    }
                } catch(e) {}
            }

            // Reset
            if (btnReset) {
                btnReset.addEventListener('click', function() {
                    if (confirm('Revert all voucher edits and rates back to original system defaults?')) {
                        try {
                            localStorage.removeItem(DRAFT_KEY);
                        } catch(e) {}
                        window.location.reload();
                    }
                });
            }

            restoreDraft();
            window.recalculateTotals();
        })();
    </script>
</body>
</html>
