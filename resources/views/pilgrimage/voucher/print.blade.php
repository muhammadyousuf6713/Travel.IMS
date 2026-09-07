<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Voucher - {{ $voucherNo }} - {{ $booking->leader_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.4;
            padding: 20px;
        }

        .no-print-bar {
            max-width: 860px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-print {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-print:hover {
            background-color: #1d4ed8;
        }

        .btn-back {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .voucher-card {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 35px 40px;
            border: 1px solid #e5e7eb;
        }

        /* Voucher Header */
        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .agency-info h2 {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .agency-info p {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.4;
        }

        .voucher-title-box {
            text-align: right;
        }

        .voucher-badge {
            background-color: #eff6ff;
            color: #2563eb;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 6px;
            border: 1px solid #bfdbfe;
        }

        .voucher-title-box h1 {
            font-size: 20px;
            font-weight: 800;
            color: #1e3a8a;
            margin-bottom: 4px;
        }

        .voucher-no {
            font-size: 13px;
            font-weight: 700;
            color: #4b5563;
        }

        /* Guest & Booking Details Meta Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 25px;
        }

        .meta-item .label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .meta-item .value {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Table of Stays */
        .section-heading {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e293b;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-stays {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .table-stays th {
            background-color: #1e293b;
            color: #ffffff;
            text-align: left;
            padding: 9px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .table-stays td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }

        .table-stays tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .table-stays .hotel-name {
            font-weight: 700;
            color: #0f172a;
        }

        .table-stays .city-tag {
            font-size: 10px;
            font-weight: 700;
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 3px;
        }

        .table-stays .nights-pill {
            background-color: #ecfdf5;
            color: #047857;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            border: 1px solid #a7f3d0;
        }

        /* Co-Travelers Box */
        @if($booking->pilgrims->isNotEmpty())
        .travelers-box {
            background-color: #fdfdfd;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .travelers-list {
            font-size: 12px;
            color: #374151;
            line-height: 1.6;
        }
        @endif

        /* Emergency Operations Support Box */
        .ops-contact-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ops-contact-box .ops-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .ops-contact-box .ops-contacts {
            display: flex;
            gap: 20px;
            font-size: 12px;
            color: #1e3a8a;
            font-weight: 600;
        }

        /* Standard Terms & Conditions */
        .terms-box {
            border-top: 1px dashed #cbd5e1;
            padding-top: 15px;
            margin-bottom: 20px;
        }

        .terms-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .terms-list {
            font-size: 10px;
            color: #64748b;
            padding-left: 16px;
            line-height: 1.5;
        }

        .terms-list li {
            margin-bottom: 3px;
        }

        /* Footer */
        .voucher-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: #64748b;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .voucher-card {
                box-shadow: none;
                border: none;
                padding: 10px 15px;
                max-width: 100%;
            }

            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Toolbar (Hidden during print) -->
    <div class="no-print-bar">
        <a href="{{ route('pilgrimage.index') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Operations
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> Print Voucher / Save as PDF
        </button>
    </div>

    <!-- Official Voucher Document Card -->
    <div class="voucher-card">

        <!-- Voucher Header -->
        <div class="voucher-header">
            <div class="agency-info">
                <h2>{{ $orgSetting->name ?? 'FLUGZEIT AVIATION & TRAVEL' }}</h2>
                <p>{{ $orgSetting->address ?? 'Main Office, Executive Travel Operations' }}</p>
                <p>Phone: {{ $orgSetting->phone ?? '+92 300 0000000' }} | Email: {{ $orgSetting->email ?? 'info@travelims.com' }}</p>
                @if(!empty($orgSetting->website))
                    <p>Web: {{ $orgSetting->website }}</p>
                @endif
            </div>

            <div class="voucher-title-box">
                <span class="voucher-badge">Official Document</span>
                <h1>HOTEL ACCOMMODATION VOUCHER</h1>
                <p class="voucher-no">Voucher No: <strong style="color: #2563eb;">{{ $voucherNo }}</strong></p>
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div class="meta-item">
                <div class="label">Issue Date</div>
                <div class="value">{{ $booking->submitted_at ? $booking->submitted_at->format('d M Y') : $printDate->format('d M Y') }}</div>
            </div>

            <div class="meta-item">
                <div class="label">Guest / Lead Traveler</div>
                <div class="value">{{ $booking->leader_name }}</div>
            </div>

            <div class="meta-item">
                <div class="label">Total Persons</div>
                <div class="value">{{ $booking->total_persons }} Pax ({{ $booking->total_persons == 1 ? '1 Leader' : '1 Leader + ' . ($booking->total_persons - 1) . ' Travelers' }})</div>
            </div>

            <div class="meta-item">
                <div class="label">Service Category</div>
                <div class="value text-uppercase" style="color: #1e3a8a;">{{ $booking->service_type }}</div>
            </div>
        </div>

        <!-- Co-Travelers Summary if exists -->
        @if($booking->pilgrims->isNotEmpty())
            <div class="travelers-box">
                <div class="section-heading" style="margin-bottom: 5px;">
                    <i class="fa-solid fa-users" style="color: #2563eb;"></i> Accompanying Travelers:
                </div>
                <div class="travelers-list">
                    @foreach($booking->pilgrims as $pIndex => $p)
                        <strong>{{ $pIndex + 1 }}.</strong> {{ $p->name }} @if($p->passport_no) <span style="color:#64748b;">(Pass: {{ $p->passport_no }})</span>@endif @if(!$loop->last) &bull; @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Hotel Accommodation Table -->
        <div class="section-heading">
            <i class="fa-solid fa-hotel" style="color: #2563eb;"></i> Confirmed Hotel Accommodation Schedule
        </div>

        <table class="table-stays">
            <thead>
                <tr>
                    <th style="width: 28%;">Hotel & Destination</th>
                    <th style="width: 12%;">Nights</th>
                    <th style="width: 18%;">Check-In</th>
                    <th style="width: 18%;">Check-Out</th>
                    <th style="width: 14%;">Room Type</th>
                    <th style="width: 10%;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($booking->hotels as $hotel)
                    <tr>
                        <td>
                            <div class="hotel-name">{{ $hotel->hotel_name }}</div>
                            <span class="city-tag">{{ $hotel->city }}</span>
                            @if($hotel->voucher_no)
                                <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Ref: {{ $hotel->voucher_no }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="nights-pill">{{ $hotel->nights }} {{ $hotel->nights == 1 ? 'Night' : 'Nights' }}</span>
                        </td>
                        <td>
                            <strong>{{ $hotel->check_in ? $hotel->check_in->format('d M Y') : '—' }}</strong>
                            <div style="font-size: 10px; color: #64748b;">{{ $hotel->check_in ? $hotel->check_in->format('H:i') : '' }}</div>
                        </td>
                        <td>
                            <strong>{{ $hotel->check_out ? $hotel->check_out->format('d M Y') : '—' }}</strong>
                            <div style="font-size: 10px; color: #64748b;">{{ $hotel->check_out ? $hotel->check_out->format('H:i') : '' }}</div>
                        </td>
                        <td>
                            <strong>{{ $hotel->room_type }}</strong>
                        </td>
                        <td>
                            <strong>{{ $hotel->room_qty }}</strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 20px; color: #6b7280;">No hotel accommodation legs recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Operations Emergency Hotlines (Mapped dynamically by city) -->
        <div class="ops-contact-box">
            <div>
                <div class="ops-title"><i class="fa-solid fa-headset me-1"></i> 24/7 Ground Operations & Emergency Helplines</div>
                <div class="ops-contacts">
                    <span>Makkah Ops: <strong>{{ $settings->makkah_ops_contact ?? '+966 50 123 4567' }}</strong></span>
                    <span>Madinah Ops: <strong>{{ $settings->madinah_ops_contact ?? '+966 50 765 4321' }}</strong></span>
                    <span>Jeddah Airport: <strong>{{ $settings->jeddah_ops_contact ?? '+966 50 999 8888' }}</strong></span>
                </div>
            </div>
            <div style="text-align: right; font-size: 11px; color: #1e40af; font-weight: 600;">
                Hotline: {{ $settings->general_hotline ?? '+92 300 1234567' }}
            </div>
        </div>

        <!-- Standard Terms & Conditions (Verbatim from PRD Appendix B) -->
        <div class="terms-box">
            <div class="terms-title">Standard Terms & Conditions:</div>
            <ol class="terms-list">
                <li>Check-in time starts after 04:00 PM, whereas Check-out time ends at 12:00 Noon as per Hotel Policy, regardless of arrival/departure time of any airline.</li>
                <li>Late arrival must be informed otherwise reservations may be cancelled automatically after 6:00 PM.</li>
                <li>Early check-in is subject to availability.</li>
                <li>All Hotels & Rates Offered/Quoted are subject to availability/confirmation.</li>
                <li>Hotel rooms vary in size & view; "Double" may mean two single beds or one king bed for 2 persons, subject to availability — no objections entertained.</li>
                <li>Triple & Quad rooms may include a 3rd/4th bed as extra or sofa-cum-bed — no objections entertained.</li>
                <li>Star categories are assigned by Saudi authorities according to their own rules and regulations.</li>
                <li>Refund of package amount (Hotels & Airline) in case of changes/restrictions is subject to Hotel/Airline policy. These are generally Non-Refundable.</li>
                <li>Extension is subject to Hotel approval and payment.</li>
                <li>No refund of partially used portion once confirmed.</li>
                <li>Terms & Conditions may change without prior notice and apply to all pilgrims.</li>
            </ol>
        </div>

        <!-- Footer Audit Line -->
        <div class="voucher-footer">
            <span>This is a system generated voucher. Printed by <strong>{{ $booking->submittingAgent->name ?? $user->name }}</strong> on {{ $printDate->format('d M Y, H:i') }}.</span>
            <span>Ref: {{ $voucherNo }}</span>
        </div>

    </div>

</body>
</html>
