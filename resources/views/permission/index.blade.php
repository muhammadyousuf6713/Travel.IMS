@extends('layouts.user_type.auth')


<style>
    .dot {
        height: 10px;
        width: 10px;
        background-color: #ef8e8e;
        border-radius: 50%;
        display: inline-block;
    }

    .dot2 {
        height: 10px;
        width: 10px;
        background-color: #b69595;
        border-radius: 50%;
        display: inline-block;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 34px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        /* Default color */
        transition: .4s;
        border-radius: 34px;
    }

    .switch .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    .switch input:checked+.slider.module-slider {
        background-color: #3bafda;
        /* Primary Modules Permission */
    }

    .switch input:checked+.slider.submodule-slider {
        background-color: #4caf50;
        /* Sub Modules Permission */
    }

    .switch input:checked+.slider.subpermissions-slider {
        background-color: #ffc107;
        /* Access Controls Permission */
    }

    .switch input:checked+.slider:before {
        transform: translateX(14px);
    }

    .switch label {
        margin: 0;
    }

    .description-container {
        margin-bottom: 1rem;
    }

    .description-container h5 {
        font-weight: bold;
        color: #333;
    }

    .description-text {
        font-size: 0.9rem;
        margin-left: 10px;
    }

    .btn {
        border-radius: 4px;
    }

    .btn-primary {
        background-color: #3bafda;
        border-color: #3bafda;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .table thead th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .table tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }

    .table tbody tr:hover {
        background-color: #e9ecef;
    }

    .card {
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    .card-body {
        padding: 1.25rem;
    }

    .alert-card {
        border-radius: 6px;
        padding: 0.75rem 1.25rem;
    }

    .alert-card .close {
        margin-top: -0.75rem;
        margin-right: -0.75rem;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-12">
            @if (Session('alert'))
                <div class="alert alert-{{ Session('alert-class') }} alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                    <span class="alert-text"><strong>{{ Session('alert') }}</strong></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header pb-0 bg-white">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold">Roles Permission Management</h5>
                        </div>
                        <a href="{{ url('roles') }}" class="btn bg-gradient-secondary btn-sm mb-0 text-uppercase">
                            <i class="fa fa-arrow-left me-1"></i> Back to Roles
                        </a>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role" class="form-control-label">Select Role</label>
                                <select onchange="location.href='{{ url('roles/permission') }}/' + this.value;" class="form-control">
                                    <option value="0">--Select Role--</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ $role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <form id="permission" action="{{ url('roles/permission', [$role_id]) }}" method="post">
                        @csrf
                        <div class="p-4">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card shadow-none border">
                                        <div class="card-body">
                                            <!-- Colorful Switches Description -->
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <div class="description-container">
                                                        <h6 class="fw-bold mb-3">Access Control Legend:</h6>
                                                        <div class="d-flex flex-wrap gap-3">
                                                            <div class="d-flex align-items-center mb-2 me-3">
                                                                <label class="switch switch-primary me-2 mb-0">
                                                                    <input type="checkbox" checked disabled />
                                                                    <span class="slider module-slider"></span>
                                                                </label>
                                                                <span class="text-xs text-muted"> <b class="text-info">Primary:</b> Core Module</span>
                                                            </div>
                                                            <div class="d-flex align-items-center mb-2 me-3">
                                                                <label class="switch switch-success me-2 mb-0">
                                                                    <input type="checkbox" checked disabled />
                                                                    <span class="slider submodule-slider"></span>
                                                                </label>
                                                                <span class="text-xs text-muted"> <b class="text-success">Sub-Module:</b> Feature</span>
                                                            </div>
                                                            <div class="d-flex align-items-center mb-2">
                                                                <label class="switch switch-warning me-2 mb-0">
                                                                    <input type="checkbox" checked disabled />
                                                                    <span class="slider subpermissions-slider"></span>
                                                                </label>
                                                                <span class="text-xs text-muted"> <b class="text-warning">Action:</b> Control</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <button type="submit" class="btn bg-gradient-primary mb-0">
                                                        <i class="fa fa-save me-1"></i> Update Permissions
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="horizontal dark my-4">
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold">Module Permissions</h6>
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-auto check-module" type="checkbox" id="check-all">
                                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="check-all">Check All</label>
                                </div>
                            </div>
                                {{-- @csrf --}}
                            <div class="table-responsive">
                                <table id="permissionsTable" class="table table-striped table-bordered w-100">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Pages</th>
                                            <th>Permission</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $count = 1; @endphp
                                        @foreach ($permissions->groupBy('parent_id') as $parent_id => $group)
                                            @if ($parent_id == 0)
                                                @foreach ($group as $permission)
                                                    <tr class="odd gradeX">
                                                        <td>{{ $count++ }}</td>
                                                        <td class="fw-bold text-dark">
                                                            {{ $permission->name }}
                                                        </td>
                                                        <td>
                                                            <label class="switch">
                                                                <input type="checkbox" class="check-module"
                                                                    name="permissions[]" value="{{ $permission->id }}"
                                                                    {{ in_array($permission->id, $assigned_permissions) ? 'checked' : '' }}>
                                                                <span class="slider module-slider"></span>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    @foreach ($permissions->where('parent_id', $permission->id) as $subPermission)
                                                        <tr class="odd gradeX sub-module"
                                                            data-parent="{{ $permission->id }}">
                                                            <td>{{ $count++ }}</td>
                                                            <td style="padding-left: 3rem;">
                                                                <span class="dot me-2"></span>
                                                                <span class="text-secondary">{{ $subPermission->name }}</span>
                                                            </td>
                                                            <td>
                                                                <label class="switch">
                                                                    <input type="checkbox" class="check-module"
                                                                        name="permissions[]"
                                                                        value="{{ $subPermission->id }}"
                                                                        {{ in_array($subPermission->id, $assigned_permissions) ? 'checked' : '' }}>
                                                                    <span class="slider submodule-slider"></span>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        @foreach ($permissions->where('parent_id', $subPermission->id) as $subSubPermission)
                                                            <tr class="odd gradeX sub-permissions"
                                                                data-parent="{{ $subPermission->id }}">
                                                                <td>{{ $count++ }}</td>
                                                                <td style="padding-left: 5rem;">
                                                                    <span class="dot2 me-2"></span>
                                                                    <small class="text-muted">{{ $subSubPermission->name }}</small>
                                                                </td>
                                                                <td>
                                                                    <label class="switch">
                                                                        <input type="checkbox" class="check-module"
                                                                            name="permissions[]"
                                                                            value="{{ $subSubPermission->id }}"
                                                                            {{ in_array($subSubPermission->id, $assigned_permissions) ? 'checked' : '' }}>
                                                                        <span
                                                                            class="slider subpermissions-slider"></span>
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault(); // Prevent the default save dialog in the browser
                document.getElementById('permission').submit(); // Submit the form
            }
        });
        $(document).ready(function() {
            $('#permissionsTable').DataTable({
                "paging": false,
                "info": false,
                "dom": 'Bfrtip',
                "buttons": [
                    'excel', 'pdf', 'print'
                ],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search Permissions...",
                    "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                }
            });


            $('#check-all').change(function() {
                var isChecked = $(this).is(':checked');
                $('.check-module').prop('checked', isChecked);
            });

            // $('.check-module').change(function() {
            //     var isChecked = $(this).is(':checked');
            //     var parentId = $(this).closest('tr').data('parent');
            //     $(this).closest('tr').nextUntil('tr:not([data-parent="' + parentId + '"])').find('input')
            //         .prop('checked', isChecked);
            // });

            $('.check-submodule').change(function() {
                var isChecked = $(this).is(':checked');
                var parentId = $(this).closest('tr').data('parent');
                $(this).closest('tr').nextUntil('tr:not([data-parent="' + parentId + '"])').find('input')
                    .prop('checked', isChecked);
            });
        });
        var checkAllAccountsBtn = document.getElementById('checkAllAccounts');
        if (checkAllAccountsBtn) {
            checkAllAccountsBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.account-slider');
                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                checkboxes.forEach(checkbox => { checkbox.checked = !allChecked; });
            });
        }
        });
    </script>
@endpush
