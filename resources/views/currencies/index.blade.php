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
            <div class="d-flex flex-row justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-coins text-warning me-2"></i> Currencies & Exchange Rates
                    </h5>
                    <p class="text-secondary text-xxs mb-0">Manage billing currencies, active exchange rates, and quotation defaults for vouchers and billing.</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm mb-0 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addCurrencyModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Currency
                </button>
            </div>
        </div>

        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle w-100" id="currenciesTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Code</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Currency Name</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Symbol</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end">Exchange Rate</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($currencies as $c)
                            <tr>
                                <td>
                                    <span class="badge bg-gradient-dark text-white text-xs font-weight-bold px-3 py-1">
                                        {{ $c->code }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-sm font-weight-bold text-dark">{{ $c->name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border text-xs px-2 py-1">
                                        {{ $c->symbol ?: $c->code }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="text-sm font-weight-bolder text-dark font-monospace">
                                        {{ number_format($c->exchange_rate, 4) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($c->is_default)
                                        <span class="badge bg-success-subtle text-success text-xxs font-weight-bold border border-success px-2 py-1">
                                            <i class="fa-solid fa-star me-1"></i> Base Currency
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary text-xxs font-weight-bold border px-2 py-1">
                                            {{ $c->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-xs mb-0 me-1 edit-currency-btn"
                                        data-id="{{ $c->id }}"
                                        data-code="{{ $c->code }}"
                                        data-name="{{ $c->name }}"
                                        data-symbol="{{ $c->symbol }}"
                                        data-rate="{{ $c->exchange_rate }}"
                                        data-default="{{ $c->is_default ? 1 : 0 }}"
                                        data-status="{{ $c->status ? 1 : 0 }}"
                                        title="Edit Currency">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                    </button>

                                    @if(!$c->is_default)
                                        <button type="button" class="btn btn-outline-danger btn-xs mb-0 delete-currency-btn"
                                            data-id="{{ $c->id }}"
                                            data-code="{{ $c->code }}"
                                            title="Delete Currency">
                                            <i class="fa-solid fa-trash-can me-1"></i> Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-secondary text-xs">
                                    No currencies configured. Click "Add Currency" above to define your base and client billing currencies.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Add Currency Modal -->
<div class="modal fade" id="addCurrencyModal" tabindex="-1" aria-labelledby="addCurrencyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg border-radius-lg">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <h6 class="modal-title font-weight-bold text-dark" id="addCurrencyModalLabel">
                    <i class="fa-solid fa-circle-plus text-primary me-2"></i> Add New Currency
                </h6>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('currencies.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Currency Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control form-control-sm text-uppercase font-weight-bold" placeholder="e.g. SAR, USD, PKR" maxlength="10" required>
                            <span class="text-xxs text-muted">ISO 3-letter currency code.</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Symbol / Abbr</label>
                            <input type="text" name="symbol" class="form-control form-control-sm" placeholder="e.g. SAR, $, Rs" maxlength="20">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Currency Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Saudi Riyal, US Dollar" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Exchange Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" name="exchange_rate" class="form-control form-control-sm font-monospace" placeholder="1.0000" value="1.0000" required>
                            <span class="text-xxs text-muted">Conversion rate relative to base system currency.</span>
                        </div>
                        <div class="col-12 pt-2">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_default" id="add_is_default" value="1">
                                <label class="form-check-label text-xs font-weight-bold" for="add_is_default">Set as System Base Currency</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="add_status" value="1" checked>
                                <label class="form-check-label text-xs font-weight-bold" for="add_status">Active & Available for Quotations / Vouchers</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm mb-0">Save Currency</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Currency Modal -->
<div class="modal fade" id="editCurrencyModal" tabindex="-1" aria-labelledby="editCurrencyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg border-radius-lg">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <h6 class="modal-title font-weight-bold text-dark" id="editCurrencyModalLabel">
                    <i class="fa-solid fa-pen-to-square text-info me-2"></i> Edit Currency
                </h6>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCurrencyForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Currency Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit_code" class="form-control form-control-sm text-uppercase font-weight-bold" maxlength="10" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Symbol / Abbr</label>
                            <input type="text" name="symbol" id="edit_symbol" class="form-control form-control-sm" maxlength="20">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Currency Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Exchange Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" name="exchange_rate" id="edit_exchange_rate" class="form-control form-control-sm font-monospace" required>
                        </div>
                        <div class="col-12 pt-2">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_default" id="edit_is_default" value="1">
                                <label class="form-check-label text-xs font-weight-bold" for="edit_is_default">Set as System Base Currency</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="edit_status" value="1">
                                <label class="form-check-label text-xs font-weight-bold" for="edit_status">Active & Available for Quotations / Vouchers</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm mb-0">Update Currency</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Currency Modal -->
<div class="modal fade" id="deleteCurrencyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg border-radius-lg">
            <div class="modal-body p-4 text-center">
                <div class="avatar avatar-md bg-danger-subtle text-danger rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-trash-can fa-lg"></i>
                </div>
                <h6 class="font-weight-bold text-dark mb-1">Delete Currency</h6>
                <p class="text-secondary text-xs mb-4">Are you sure you want to delete <span class="font-weight-bold text-dark" id="deleteCurrencyCode"></span>? This action cannot be undone.</p>
                <form id="deleteCurrencyForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-0 px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm mb-0 px-3">Confirm Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit Modal Binding
    document.querySelectorAll('.edit-currency-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const code = this.getAttribute('data-code');
            const name = this.getAttribute('data-name');
            const symbol = this.getAttribute('data-symbol');
            const rate = this.getAttribute('data-rate');
            const isDefault = this.getAttribute('data-default') == '1';
            const status = this.getAttribute('data-status') == '1';

            const form = document.getElementById('editCurrencyForm');
            form.action = "{{ url('currencies') }}/" + id;

            document.getElementById('edit_code').value = code;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_symbol').value = symbol;
            document.getElementById('edit_exchange_rate').value = parseFloat(rate).toFixed(4);
            document.getElementById('edit_is_default').checked = isDefault;
            document.getElementById('edit_status').checked = status;

            const modal = new bootstrap.Modal(document.getElementById('editCurrencyModal'));
            modal.show();
        });
    });

    // Delete Modal Binding
    document.querySelectorAll('.delete-currency-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const code = this.getAttribute('data-code');

            const form = document.getElementById('deleteCurrencyForm');
            form.action = "{{ url('currencies') }}/" + id;
            document.getElementById('deleteCurrencyCode').textContent = code;

            const modal = new bootstrap.Modal(document.getElementById('deleteCurrencyModal'));
            modal.show();
        });
    });
});
</script>
@endsection
