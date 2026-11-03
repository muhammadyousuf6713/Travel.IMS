@extends('layouts.user_type.auth')

@section('content')
<style>
    .rp-title h5 { font-weight: 800; color: #1e293b; margin: 0; }
    .rp-title p { color: #8392ab; font-size: .85rem; margin: 0; }
    .rp-card { background: #fff; border: 1px solid #eef0f3; border-radius: 14px; box-shadow: 0 1px 3px rgba(16,24,40,.05); }
    .rp-card-h { display: flex; justify-content: space-between; align-items: center; gap: .75rem; flex-wrap: wrap; padding: 1rem 1.25rem; border-bottom: 1px solid #eef0f3; }
    .rp-card-h h6 { margin: 0; font-weight: 700; color: #1e293b; }
    .rp-card-h .sub { font-size: .75rem; color: #8392ab; }
    .rp-card-b { padding: 1.1rem 1.25rem; }
    .rp-label { font-size: .75rem; font-weight: 700; color: #344767; margin-bottom: .3rem; display: block; }
    .rp-actions { display: flex; flex-wrap: wrap; gap: .5rem; justify-content: flex-end; align-items: center; }
    .rp-summary { display: flex; flex-wrap: wrap; gap: .5rem; }
    .rp-chip { border: 1px solid #eef0f3; border-radius: 10px; padding: .45rem .8rem; background: #fafbfc; }
    .rp-chip .n { font-weight: 800; color: #1e293b; font-size: 1rem; line-height: 1.1; }
    .rp-chip .t { font-size: .68rem; color: #8392ab; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
    .rp-st { display: inline-block; font-size: .7rem; font-weight: 700; padding: .22rem .6rem; border-radius: 999px; white-space: nowrap; background: #f1f3f5; color: #475569; }
    .rp-st.Open { background: #eef1f4; color: #475569; }
    .rp-st.In-Progress { background: #e8f0fe; color: #1d4ed8; }
    .rp-st.Quotation { background: #fff4e5; color: #b45309; }
    .rp-st.Hold { background: #f3e8ff; color: #7e22ce; }
    .rp-st.Confirmed, .rp-st.Completed { background: #e6f7ee; color: #15803d; }
    .rp-st.Cancelled { background: #fdecec; color: #b91c1c; }
    #reportTable td { white-space: nowrap; }
    #reportTable td.cust { font-weight: 600; color: #1e293b; }
    .rp-footer { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .5rem; padding: .85rem 1.25rem; }
    .rp-empty { padding: 2rem 1rem; text-align: center; color: #8392ab; }
    .rp-empty i { display: block; font-size: 1.5rem; color: #cbd3da; margin-bottom: .4rem; }
    body.dark-mode .rp-card { background: #1e293b; border-color: #334155; }
    body.dark-mode .rp-title h5, body.dark-mode .rp-card-h h6, body.dark-mode .rp-chip .n { color: #f1f5f9; }
    body.dark-mode .rp-chip { background: transparent; border-color: #334155; }
</style>

<div class="container-fluid py-3">
    <div class="rp-title mb-3">
        <h5>Reports</h5>
        <p>Filter inquiries, preview the results below, then download them as Excel or PDF.</p>
    </div>

    {{-- Filters --}}
    <div class="rp-card mb-3">
        <div class="rp-card-h">
            <div><h6><i class="fa-solid fa-sliders me-2 text-secondary"></i>Filters</h6><div class="sub">The preview updates automatically when you change a filter</div></div>
        </div>
        <div class="rp-card-b">
            <form id="reportFilterForm" onsubmit="return false;">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="rp-label">Inquiry type</label>
                        <select name="inquiry_type[]" class="form-control select2" multiple data-placeholder="All types">
                            @foreach($inquiry_types as $type)
                                <option value="{{ $type->type_id }}">{{ $type->type_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="rp-label">Lead source</label>
                        <select name="sales_reference[]" class="form-control select2" multiple data-placeholder="All sources">
                            @foreach($sales_reference as $ref)
                                <option value="{{ $ref->type_id }}">{{ $ref->type_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="rp-label">Salesperson</label>
                        <select name="sales_person[]" class="form-control select2" multiple data-placeholder="All salespeople">
                            @foreach($sales_person as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="rp-label">City</label>
                        <select name="city[]" class="form-control ajax-city-select2" multiple data-placeholder="Type 2+ letters to search"></select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="rp-label">Status</label>
                        <select name="status[]" class="form-control select2" multiple data-placeholder="All statuses">
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="rp-label">Created from</label>
                        <input type="date" name="date_from" class="form-control form-control-sm">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="rp-label">Created to</label>
                        <input type="date" name="date_to" class="form-control form-control-sm">
                    </div>
                    <div class="col-lg-3 col-md-6 d-flex align-items-end">
                        <div class="d-flex flex-wrap gap-1 w-100">
                            <button type="button" class="btn btn-sm btn-light mb-0 quick-range" data-range="7">Last 7 days</button>
                            <button type="button" class="btn btn-sm btn-light mb-0 quick-range" data-range="30">Last 30 days</button>
                            <button type="button" class="btn btn-sm btn-light mb-0 quick-range" data-range="month">This month</button>
                        </div>
                    </div>
                </div>

                <div class="rp-actions mt-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-0" id="resetReportFilters"><i class="fa-solid fa-rotate-left me-1"></i> Clear all</button>
                    <button type="button" class="btn btn-sm btn-primary mb-0" id="previewBtn"><i class="fa-regular fa-eye me-1"></i> Preview</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-0" onclick="downloadReport('excel')"><i class="fa-regular fa-file-excel me-1 text-success"></i> Excel</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-0" onclick="downloadReport('pdf')"><i class="fa-regular fa-file-pdf me-1 text-danger"></i> PDF</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Live preview --}}
    <div class="rp-card">
        <div class="rp-card-h">
            <div><h6><i class="fa-solid fa-table-list me-2 text-secondary"></i>Preview</h6><div class="sub" id="previewSub">All inquiries</div></div>
            <div class="rp-summary" id="rpSummary">
                <div class="rp-chip"><div class="n" id="sumTotal">–</div><div class="t">Records</div></div>
                <div class="rp-chip"><div class="n" id="sumWon">–</div><div class="t">Won on this page</div></div>
                <div class="rp-chip"><div class="n" id="sumActive">–</div><div class="t">Active on this page</div></div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="reportTable" class="table align-middle mb-0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Cell</th>
                        <th>City</th>
                        <th>Inquiry type</th>
                        <th>Salesperson</th>
                        <th>Lead source</th>
                        <th>Status</th>
                        <th>Travel date</th>
                        <th>Created</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const esc = s => $('<div>').text(s == null ? '' : s).html();
    const filterParams = () => {
        const out = {};
        $('#reportFilterForm').serializeArray().forEach(({ name, value }) => {
            if (value === '') return;
            if (name.endsWith('[]')) { (out[name.slice(0, -2)] = out[name.slice(0, -2)] || []).push(value); }
            else out[name] = value;
        });
        return out;
    };

    const table = $('#reportTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 250], [25, 50, 100, 250]],
        order: [[9, 'desc']],
        dom: 't<"rp-footer"lip>',
        ajax: {
            url: '{{ route('reports.data') }}',
            data: d => Object.assign(d, filterParams())
        },
        columns: [
            { data: 'id_inquiry', name: 'id_inquiry', render: (v) => '<a href="{{ url('edit_inquiry') }}/' + v + '" target="_blank" class="fw-bold text-primary">#' + v + '</a>' },
            { data: 'customer_name', name: 'customer_name', className: 'cust', render: v => esc(v) },
            { data: 'customer_cell', name: 'customer_cell', render: v => esc(v) },
            { data: 'customer_city', name: 'customer_city', render: v => esc(v) },
            { data: 'inquiry_type_name', name: 'inquiry_type_name', render: v => esc(v) },
            { data: 'salesperson_name', name: 'salesperson_name', render: v => esc(v) },
            { data: 'sales_ref_name', name: 'sales_ref_name', render: v => esc(v) },
            { data: 'status', name: 'status', render: v => v ? '<span class="rp-st ' + esc(v).replace(/\s+/g, '-') + '">' + esc(v) + '</span>' : '-' },
            { data: 'travel_date', name: 'travel_date', render: v => esc(v) },
            { data: 'created_at', name: 'created_at', render: v => esc(v) }
        ],
        language: {
            emptyTable: '<div class="rp-empty"><i class="fa-regular fa-folder-open"></i>No inquiries match these filters</div>',
            zeroRecords: '<div class="rp-empty"><i class="fa-regular fa-folder-open"></i>No inquiries match these filters</div>',
            info: 'Showing _START_–_END_ of _TOTAL_',
            infoEmpty: '',
            lengthMenu: 'Rows _MENU_',
            processing: 'Loading…',
            paginate: { previous: '<i class="fa-solid fa-chevron-left"></i>', next: '<i class="fa-solid fa-chevron-right"></i>' }
        },
        drawCallback: function () {
            const api = this.api();
            const info = api.page.info();
            $('#sumTotal').text((info.recordsDisplay || 0).toLocaleString());
            const rows = api.rows({ page: 'current' }).data().toArray();
            $('#sumWon').text(rows.filter(r => ['Confirmed', 'Completed'].includes(r.status)).length);
            $('#sumActive').text(rows.filter(r => ['Open', 'In-Progress', 'Quotation', 'Hold'].includes(r.status)).length);
            updateSubtitle();
        }
    });

    function updateSubtitle() {
        const parts = [];
        $('#reportFilterForm select').each(function () {
            const sel = $(this).find('option:selected').map((_, o) => $(o).text()).get();
            if (sel.length) parts.push($(this).closest('div').find('.rp-label').text() + ': ' + (sel.length > 2 ? sel.length + ' selected' : sel.join(', ')));
        });
        const f = $('[name=date_from]').val(), t = $('[name=date_to]').val();
        if (f || t) parts.push('Created ' + (f || '…') + ' to ' + (t || 'today'));
        $('#previewSub').text(parts.length ? parts.join(' · ') : 'All inquiries');
    }

    const REP_SESSION_KEY = 'ims_filters_reports_state';
    function saveReportFilters() {
        try {
            sessionStorage.setItem(REP_SESSION_KEY, $('#reportFilterForm').serialize());
        } catch(e) {}
    }
    function restoreReportFilters() {
        try {
            const raw = sessionStorage.getItem(REP_SESSION_KEY);
            if (!raw) return;
            const params = new URLSearchParams(raw);
            for (const [key, val] of params.entries()) {
                const cleanKey = key.replace('[]', '');
                const $el = $('[name="' + key + '"], [name="' + cleanKey + '[]"], [name="' + cleanKey + '"]');
                if ($el.length) {
                    if ($el.is('select[multiple]')) {
                        const cur = $el.val() || [];
                        if (!cur.includes(val)) {
                            cur.push(val);
                            $el.val(cur).trigger('change.select2');
                        }
                    } else if ($el.is('select')) {
                        $el.val(val).trigger('change.select2');
                    } else {
                        $el.val(val);
                    }
                }
            }
        } catch(e) {}
    }
    restoreReportFilters();

    let timer;
    const refresh = () => { 
        clearTimeout(timer); 
        timer = setTimeout(() => {
            saveReportFilters();
            table.ajax.reload();
        }, 300); 
    };
    $('#reportFilterForm').on('change', 'select, input', refresh);
    $('#previewBtn').on('click', () => { saveReportFilters(); table.ajax.reload(); });

    $('.quick-range').on('click', function () {
        const r = $(this).data('range');
        const to = new Date();
        const from = new Date();
        if (r === 'month') from.setDate(1); else from.setDate(to.getDate() - (parseInt(r, 10) - 1));
        const fmt = d => d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        $('[name=date_from]').val(fmt(from));
        $('[name=date_to]').val(fmt(to));
        table.ajax.reload();
    });

    $('#resetReportFilters').on('click', function () {
        try { sessionStorage.removeItem(REP_SESSION_KEY); } catch(e) {}
        $('#reportFilterForm')[0].reset();
        $('.select2, .ajax-city-select2').val(null).trigger('change.select2');
        table.ajax.reload();
    });

    window.downloadReport = function (format) {
        const params = $('#reportFilterForm').serialize() + '&format=' + format;
        window.open('{{ route('reports.export') }}?' + params, '_blank');
    };
});
</script>
@endpush
