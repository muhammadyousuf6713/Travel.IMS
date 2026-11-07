@extends('layouts.user_type.auth')

@section('content')
@php
    $statusOrder = ['Open', 'In-Progress', 'Quotation', 'Hold', 'Confirmed', 'Completed', 'Cancelled'];
    foreach ($inquiryStatusCounts as $st => $c) { if (!in_array($st, $statusOrder) && $st) $statusOrder[] = $st; }
    $statusMax = max(1, collect($inquiryStatusCounts)->max() ?? 1);
    $leadMax = max(1, $leadSources->max('total') ?? 1);
    $statusIcon = [
        'Open' => 'fa-regular fa-folder-open', 'In-Progress' => 'fa-solid fa-spinner', 'Quotation' => 'fa-regular fa-file-lines',
        'Hold' => 'fa-regular fa-circle-pause', 'Confirmed' => 'fa-regular fa-circle-check', 'Completed' => 'fa-solid fa-flag-checkered',
        'Cancelled' => 'fa-regular fa-circle-xmark',
    ];
@endphp

<style>
    .db { --db-text:#1e293b; --db-text-2:#475569; --db-muted:#8392ab; --db-border:#eef0f3; --db-soft:#f8f9fb;
          --s1:#2a78d6; --s3:#1baf7a; }
    .db .db-head h4 { font-weight: 800; color: var(--db-text); margin: 0; }
    .db .db-head p { color: var(--db-muted); margin: 0; font-size: .85rem; }
    .db .db-card { background:#fff; border:1px solid var(--db-border); border-radius:14px; box-shadow:0 1px 3px rgba(16,24,40,.05); height:100%; }
    .db .db-card-h { display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:1rem 1.25rem .5rem; }
    .db .db-card-h h6 { margin:0; font-weight:700; color:var(--db-text); font-size:.92rem; }
    .db .db-card-h .sub { font-size:.75rem; color:var(--db-muted); }
    .db .db-card-b { padding:.5rem 1.25rem 1.1rem; }
    .db .db-link { font-size:.75rem; font-weight:600; color:var(--bs-primary); text-decoration:none; white-space:nowrap; }
    .db .db-link:hover { text-decoration:underline; }

    /* KPI tiles */
    .db .kpi { padding:1rem 1.15rem; display:flex; flex-direction:column; gap:.35rem; text-decoration:none; transition:border-color .15s, box-shadow .15s; }
    .db a.kpi:hover { border-color:#d6dde6; box-shadow:0 4px 14px rgba(16,24,40,.07); }
    .db .kpi .label { font-size:.72rem; font-weight:700; color:var(--db-muted); text-transform:uppercase; letter-spacing:.04em; display:flex; align-items:center; gap:.45rem; }
    .db .kpi .label i { width:26px; height:26px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:.75rem;
                        background:rgba(var(--bs-primary-rgb),.1); color:var(--bs-primary); }
    .db .kpi .value { font-size:1.65rem; font-weight:800; color:var(--db-text); line-height:1.1; }
    .db .kpi .meta { font-size:.75rem; color:var(--db-muted); }
    .db .delta { font-weight:700; font-size:.72rem; padding:1px 7px; border-radius:999px; }
    .db .delta.up { background:#e6f7ee; color:#15803d; } .db .delta.down { background:#fdecec; color:#b91c1c; } .db .delta.flat { background:#f1f3f5; color:#64748b; }

    /* follow-up tiles */
    .db .fu-tiles { display:grid; grid-template-columns:repeat(3,1fr); gap:.6rem; }
    .db .fu-tile { border:1px solid var(--db-border); border-radius:12px; padding:.75rem .85rem; text-decoration:none; display:block; transition:border-color .15s; }
    .db .fu-tile:hover { border-color:#d6dde6; }
    .db .fu-tile .n { font-size:1.35rem; font-weight:800; line-height:1.1; }
    .db .fu-tile .t { font-size:.72rem; font-weight:700; display:flex; gap:.35rem; align-items:center; }
    .db .fu-tile.today .n, .db .fu-tile.today .t { color:#b45309; } .db .fu-tile.today { background:#fffaf0; }
    .db .fu-tile.overdue .n, .db .fu-tile.overdue .t { color:#b91c1c; } .db .fu-tile.overdue { background:#fff7f7; }
    .db .fu-tile.upcoming .n, .db .fu-tile.upcoming .t { color:#1d4ed8; } .db .fu-tile.upcoming { background:#f5f8ff; }

    /* horizontal bars */
    .db .hbar { display:grid; grid-template-columns:130px 1fr 54px; align-items:center; gap:.75rem; padding:.42rem 0; text-decoration:none; }
    .db a.hbar:hover .name { color:var(--bs-primary); }
    .db .hbar .name { font-size:.8rem; font-weight:600; color:var(--db-text-2); display:flex; align-items:center; gap:.45rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .db .hbar .name i { color:var(--db-muted); width:14px; text-align:center; }
    .db .hbar .track { height:8px; background:#f1f3f6; border-radius:999px; overflow:hidden; }
    .db .hbar .fill { height:100%; background:var(--s1); border-radius:999px; }
    .db .hbar .num { font-size:.8rem; font-weight:700; color:var(--db-text); text-align:right; }

    /* team table */
    .db .team-row { display:grid; grid-template-columns:minmax(180px,1.4fr) 90px 90px 90px minmax(160px,1.6fr); gap:.75rem; align-items:center; padding:.55rem 0; border-bottom:1px solid #f3f4f6; }
    .db .team-row:last-child { border-bottom:0; }
    .db .team-row.head { font-size:.68rem; font-weight:700; color:var(--db-muted); text-transform:uppercase; letter-spacing:.04em; padding-top:0; }
    .db .team-row .who { display:flex; align-items:center; gap:.6rem; min-width:0; }
    .db .avatar { width:32px; height:32px; border-radius:50%; color:#fff; font-size:.7rem; font-weight:700; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
    .db .team-row .nm { font-size:.82rem; font-weight:700; color:var(--db-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .db .team-row .c { font-size:.82rem; font-weight:600; color:var(--db-text-2); text-align:right; }
    .db .conv { display:flex; align-items:center; gap:.5rem; }
    .db .conv .track { flex:1; height:6px; background:#f1f3f6; border-radius:999px; overflow:hidden; }
    .db .conv .fill { height:100%; background:var(--s3); border-radius:999px; }
    .db .conv span { font-size:.75rem; font-weight:700; color:var(--db-text); width:38px; text-align:right; }

    /* agenda */
    .db .agenda-item { display:flex; gap:.75rem; align-items:flex-start; padding:.6rem 0; border-bottom:1px solid #f3f4f6; text-decoration:none; }
    .db .agenda-item:last-child { border-bottom:0; }
    .db .agenda-item .when { min-width:64px; font-size:.7rem; font-weight:700; border-radius:8px; padding:.3rem .45rem; text-align:center; }
    .db .agenda-item .when.today { background:#fff4e5; color:#b45309; } .db .agenda-item .when.overdue { background:#fdecec; color:#b91c1c; }
    .db .agenda-item .nm { font-size:.82rem; font-weight:700; color:var(--db-text); }
    .db .agenda-item .sub { font-size:.72rem; color:var(--db-muted); }
    .db .agenda-item .rm { font-size:.75rem; color:var(--db-text-2); display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden; }
    .db .empty { text-align:center; color:var(--db-muted); font-size:.82rem; padding:1.5rem 0; }
    .db .empty i { display:block; font-size:1.4rem; color:#cbd3da; margin-bottom:.4rem; }

    /* reviews */
    .db .rv-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.6rem; }
    .db .rv-grid .box { background:var(--db-soft); border-radius:12px; padding:.7rem .85rem; }
    .db .rv-grid .n { font-size:1.25rem; font-weight:800; color:var(--db-text); }
    .db .rv-grid .t { font-size:.72rem; color:var(--db-muted); font-weight:600; }
    .db .stars { color:#f5b301; font-size:.8rem; }

    /* status table */
    .db .st-table th { font-size:.66rem !important; }
    .db .st-table td { font-size:.8rem; }
    .db .st-table a.cnt { font-weight:700; color:var(--bs-primary); text-decoration:none; }
    .db .st-table a.cnt:hover { text-decoration:underline; }
    .db .st-table .zero { color:#c3cad2; }

    /* chart */
    .db .chart-wrap { position:relative; height:260px; }
    .db .legend { display:flex; gap:1rem; font-size:.75rem; color:var(--db-text-2); font-weight:600; }
    .db .legend i { display:inline-block; width:10px; height:10px; border-radius:3px; margin-right:.35rem; vertical-align:-1px; }

    body.dark-mode .db .db-card { background:#1e293b; border-color:#334155; }
    body.dark-mode .db .kpi .value, body.dark-mode .db .db-card-h h6, body.dark-mode .db .team-row .nm,
    body.dark-mode .db .hbar .num, body.dark-mode .db .agenda-item .nm, body.dark-mode .db .db-head h4, body.dark-mode .db .rv-grid .n { color:#f1f5f9; }
    body.dark-mode .db .rv-grid .box, body.dark-mode .db .hbar .track, body.dark-mode .db .conv .track { background:#0f172a; }
    body.dark-mode .db .fu-tile { background:transparent !important; border-color:#334155; }
    @media (max-width: 767px) { .db .team-row { grid-template-columns:1fr 50px 50px 110px; } .db .team-row .c.active-col { display:none; } .db .hbar { grid-template-columns:100px 1fr 44px; } }
</style>

<div class="container-fluid py-3 db">

    {{-- Greeting + quick actions --}}
    <div class="db-head d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h4>{{ $greeting }}, {{ \Illuminate\Support\Str::of($userName)->explode(' ')->first() }}</h4>
            <p>{{ now()->format('l, d F Y') }} · {{ !empty($isSalesDashboard) ? 'Here is your personal sales pipeline, follow-ups and performance.' : 'Here is how the business is doing.' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @can('Inquiry add')
            <a href="{{ url('inquiry/create') }}" class="btn btn-sm btn-primary mb-0"><i class="fa-solid fa-plus me-1"></i> New inquiry</a>
            @endcan
            @can('Inquiry list')
            <a href="{{ route('inquiry.index', ['fud_filter' => 'today']) }}" class="btn btn-sm btn-outline-secondary mb-0"><i class="fa-regular fa-calendar-check me-1"></i> Today's follow-ups</a>
            @endcan
            @can('view reports')
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary mb-0"><i class="fa-regular fa-file-lines me-1"></i> Reports</a>
            @endcan
        </div>
    </div>

    {{-- KPI tiles --}}
    <div class="row g-3 mb-3">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('inquiry.index') }}" class="db-card kpi">
                <span class="label"><i class="fa-regular fa-envelope-open"></i> {{ !empty($isSalesDashboard) ? 'My Inquiries · last 30 days' : 'Inquiries · last 30 days' }}</span>
                <span class="value">{{ number_format($inquiriesThisMonth) }}</span>
                <span class="meta">
                    @if(!is_null($monthChange))
                        <span class="delta {{ $monthChange > 0 ? 'up' : ($monthChange < 0 ? 'down' : 'flat') }}">{{ $monthChange > 0 ? '+' : '' }}{{ $monthChange }}%</span>
                    @endif
                    vs {{ number_format($inquiriesLastMonth) }} in the previous 30 days · {{ $inquiriesToday }} today
                </span>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('inquiry.index') }}" class="db-card kpi">
                <span class="label"><i class="fa-solid fa-layer-group"></i> Active pipeline</span>
                <span class="value">{{ number_format($activePipeline) }}</span>
                <span class="meta">Open, in progress, quotation and hold</span>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('inquiry.index', ['status' => 'Confirmed']) }}" class="db-card kpi">
                <span class="label"><i class="fa-regular fa-circle-check"></i> Won (confirmed + completed)</span>
                <span class="value">{{ number_format($wonTotal) }}</span>
                <span class="meta">{{ $conversionRate }}% conversion · {{ $wonThisMonth }} won in the last 30 days</span>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ auth()->user()->can('Customers list') ? url('customers') : 'javascript:void(0);' }}" class="db-card kpi">
                <span class="label"><i class="fa-regular fa-user"></i> {{ !empty($isSalesDashboard) ? 'My Customers' : 'Customers' }}</span>
                <span class="value">{{ number_format($totalCustomers) }}</span>
                <span class="meta">+{{ number_format($newCustomersThisMonth) }} new in the last 30 days</span>
            </a>
        </div>
    </div>

    {{-- Trend + follow-ups / reviews --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="db-card">
                <div class="db-card-h">
                    <div><h6>Inquiries over the last 12 months</h6><div class="sub">New inquiries per month, and how many of them were won</div></div>
                    <div class="legend"><span><i style="background:var(--s1)"></i>New inquiries</span><span><i style="background:var(--s3)"></i>Won</span></div>
                </div>
                <div class="db-card-b"><div class="chart-wrap"><canvas id="trendChart" aria-label="Inquiries per month" role="img"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-4 d-flex flex-column gap-3">
            <div class="db-card">
                <div class="db-card-h">
                    <div><h6>Follow-ups</h6><div class="sub">Based on each inquiry's latest follow-up</div></div>
                    <a class="db-link" href="{{ route('followups.index') }}">Follow-up list</a>
                </div>
                <div class="db-card-b">
                    <div class="fu-tiles">
                        <a class="fu-tile today" href="{{ route('inquiry.index', ['fud_filter' => 'today']) }}">
                            <div class="n">{{ number_format($todaysFollowupsCount) }}</div><div class="t"><i class="fa-regular fa-clock"></i> Today</div>
                        </a>
                        <a class="fu-tile overdue" href="{{ route('inquiry.index', ['fud_filter' => 'overdue']) }}">
                            <div class="n">{{ number_format($overdueFollowupsCount) }}</div><div class="t"><i class="fa-solid fa-triangle-exclamation"></i> Overdue</div>
                        </a>
                        <a class="fu-tile upcoming" href="{{ route('inquiry.index', ['fud_filter' => 'upcoming']) }}">
                            <div class="n">{{ number_format($upcomingFollowupsCount) }}</div><div class="t"><i class="fa-regular fa-calendar"></i> Upcoming</div>
                        </a>
                    </div>
                </div>
            </div>

            @if($reviews)
                <div class="db-card">
                    <div class="db-card-h">
                        <div><h6>Customer reviews</h6><div class="sub">From completed inquiries</div></div>
                        @can('Reviews list')
                        <a class="db-link" href="{{ route('reviews.index') }}">Open reviews</a>
                        @endcan
                    </div>
                    <div class="db-card-b">
                        <div class="rv-grid">
                            <div class="box"><div class="n">{{ $reviews['open'] }}</div><div class="t">To follow up</div></div>
                            <div class="box"><div class="n" style="{{ $reviews['due'] ? 'color:#b45309' : '' }}">{{ $reviews['due'] }}</div><div class="t">Due or overdue</div></div>
                            <div class="box"><div class="n">{{ $reviews['received_month'] }}</div><div class="t">Received this month</div></div>
                            <div class="box">
                                <div class="n">{{ $reviews['avg_rating'] ?: '–' }}</div>
                                <div class="t">
                                    @if($reviews['avg_rating'])
                                        <span class="stars">@for($i = 1; $i <= 5; $i++)<i class="fa-{{ $i <= round($reviews['avg_rating']) ? 'solid' : 'regular' }} fa-star"></i>@endfor</span>
                                    @else Average rating @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Pipeline + attention + sources --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-4">
            <div class="db-card">
                <div class="db-card-h"><div><h6>Pipeline by status</h6><div class="sub">All inquiries · click a row to open the list</div></div></div>
                <div class="db-card-b">
                    @foreach($statusOrder as $st)
                        @php $c = (int) ($inquiryStatusCounts[$st] ?? 0); @endphp
                        <a class="hbar" href="{{ route('inquiry.index', ['status' => $st]) }}" title="{{ $st }}: {{ $c }}">
                            <span class="name"><i class="{{ $statusIcon[$st] ?? 'fa-regular fa-circle' }}"></i>{{ $st }}</span>
                            <span class="track"><span class="fill d-block" style="width: {{ $c ? max(2, $c / $statusMax * 100) : 0 }}%"></span></span>
                            <span class="num">{{ number_format($c) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="db-card">
                <div class="db-card-h">
                    <div><h6>Needs attention</h6><div class="sub">Active inquiries with a follow-up due today or overdue</div></div>
                    <a class="db-link" href="{{ route('inquiry.index', ['fud_filter' => 'overdue']) }}">View all</a>
                </div>
                <div class="db-card-b">
                    @forelse($agenda as $a)
                        @php $d = \Carbon\Carbon::parse($a->followup_date); $isToday = $d->isToday(); @endphp
                        <a class="agenda-item" href="{{ auth()->user()->can('Inquiry edit') ? url('edit_inquiry/' . $a->id_inquiry) : route('inquiry.index', ['id_inquiry' => $a->id_inquiry]) }}">
                            <span class="when {{ $isToday ? 'today' : 'overdue' }}">{{ $isToday ? 'Today' : $d->format('d M') }}</span>
                            <span class="flex-grow-1" style="min-width:0">
                                <span class="nm d-block">{{ $a->customer_name ?? 'Unknown' }} <span class="sub">· #{{ $a->id_inquiry }}</span></span>
                                <span class="sub d-block">{{ $a->salesperson ?? 'Unassigned' }} · {{ $a->status }}</span>
                                @if($a->remarks)<span class="rm">{{ \Illuminate\Support\Str::limit(strip_tags($a->remarks), 90) }}</span>@endif
                            </span>
                        </a>
                    @empty
                        <div class="empty"><i class="fa-regular fa-circle-check"></i>Nothing due. Well done!</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="db-card">
                <div class="db-card-h"><div><h6>Top lead sources</h6><div class="sub">Where inquiries come from</div></div></div>
                <div class="db-card-b">
                    @forelse($leadSources as $src)
                        <div class="hbar" style="grid-template-columns: 120px 1fr 50px;" title="{{ $src->name }}: {{ $src->total }}">
                            <span class="name">{{ $src->name }}</span>
                            <span class="track"><span class="fill d-block" style="width: {{ max(3, $src->total / $leadMax * 100) }}%"></span></span>
                            <span class="num">{{ number_format($src->total) }}</span>
                        </div>
                    @empty
                        <div class="empty">No data</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Team --}}
    <div class="db-card mb-3">
        <div class="db-card-h">
            <div>
                <h6>{{ !empty($isSalesDashboard) ? 'My Performance Summary' : 'Team performance' }}</h6>
                <div class="sub">{{ !empty($isSalesDashboard) ? 'Your personal active pipeline, won deals and conversion rate' : 'Top 8 salespeople by assigned inquiries · click a name to see their list' }}</div>
            </div>
        </div>
        <div class="db-card-b">
            <div class="team-row head"><span>Salesperson</span><span class="c">Active</span><span class="c">Won</span><span class="c">Total</span><span>Conversion</span></div>
            @php $pal = ['#4f6bed','#0f9d8a','#c2410c','#7c3aed','#be185d','#0369a1','#4d7c0f','#b45309']; @endphp
            @forelse($team as $u)
                @php
                    $conv = $u->total ? round($u->won / $u->total * 100) : 0;
                    $parts = preg_split('/\s+/', trim($u->name));
                    $ini = strtoupper(mb_substr($parts[0] ?? '?', 0, 1) . mb_substr($parts[1] ?? '', 0, 1));
                @endphp
                <div class="team-row">
                    <a class="who text-decoration-none" href="{{ route('inquiry.index', ['sales_person' => $u->id]) }}">
                        <span class="avatar" style="background: {{ $pal[abs(crc32($u->name)) % count($pal)] }}">{{ $ini }}</span>
                        <span class="nm">{{ $u->name }}</span>
                    </a>
                    <span class="c active-col">{{ number_format($u->active) }}</span>
                    <span class="c">{{ number_format($u->won) }}</span>
                    <span class="c">{{ number_format($u->total) }}</span>
                    <span class="conv" title="{{ $u->won }} won of {{ $u->total }}"><span class="track"><span class="fill d-block" style="width: {{ $conv }}%"></span></span><span>{{ $conv }}%</span></span>
                </div>
            @empty
                <div class="empty"><i class="fa-regular fa-user"></i>No assigned inquiries yet</div>
            @endforelse
        </div>
    </div>

    {{-- Status by salesperson --}}
    <div class="db-card mb-2">
        <div class="db-card-h">
            <div>
                <h6>{{ !empty($isSalesDashboard) ? 'My Inquiries by Status' : 'Inquiry status by salesperson' }}</h6>
                <div class="sub">Click a number to open those inquiries</div>
            </div>
        </div>
        <div class="db-card-b pt-0">
            <div class="table-responsive">
                <table class="table st-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Salesperson</th>
                            @foreach($allStatuses as $status)<th class="text-center">{{ $status }}</th>@endforeach
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salespersonStatusCounts as $spName => $data)
                            <tr>
                                <td class="fw-bold">{{ $spName ?? 'Unassigned' }}</td>
                                @foreach($allStatuses as $status)
                                    @php $count = $data['counts'][$status] ?? 0; @endphp
                                    <td class="text-center">
                                        @if($count > 0 && isset($data['id']))
                                            <a class="cnt" href="{{ route('inquiry.index', ['sales_person' => $data['id'], 'status' => $status]) }}">{{ $count }}</a>
                                        @else
                                            <span class="zero">0</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center fw-bold">
                                    <a class="cnt" href="{{ route('inquiry.index', ['sales_person' => $data['id']]) }}">{{ array_sum($data['counts']) }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($allStatuses) + 2 }}" class="text-center text-sm">No data available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const el = document.getElementById('trendChart');
    if (!el || !window.Chart) return;
    const trend = @json($trend);
    const dark = document.body.classList.contains('dark-mode');
    const ink = dark ? '#cbd5e1' : '#64748b';
    const grid = dark ? 'rgba(148,163,184,.15)' : '#eef0f3';
    new Chart(el, {
        data: {
            labels: trend.labels,
            datasets: [
                { type: 'bar', label: 'New inquiries', data: trend.total, backgroundColor: '#2a78d6', borderRadius: 4, borderSkipped: 'bottom', maxBarThickness: 28, order: 2 },
                { type: 'line', label: 'Won', data: trend.won, borderColor: '#1baf7a', backgroundColor: '#1baf7a', borderWidth: 2, tension: .3,
                  pointRadius: 3, pointHoverRadius: 5, pointBackgroundColor: '#fff', pointBorderWidth: 2, order: 1 }
            ]
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8, titleFont: { weight: '700' }, boxPadding: 4 }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: ink, font: { size: 11 } }, border: { display: false } },
                y: { beginAtZero: true, grid: { color: grid }, ticks: { color: ink, font: { size: 11 }, precision: 0 }, border: { display: false } }
            }
        }
    });
})();
</script>
@endpush
