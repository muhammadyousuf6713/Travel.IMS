@extends('layouts.user_type.auth')

@section('content')
<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header pb-0 bg-white">
                    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0 fw-bold">Users Management</h5>
                            <p class="text-xs text-secondary mb-0">Manage system users, login as specific users to test allotted permissions, or inspect detailed access profiles.</p>
                        </div>
                        <a href="{{ url('users/create') }}" class="btn btn-primary btn-sm mb-0 text-uppercase" type="button">
                            <i class="fa fa-plus me-1"></i> Add User
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show text-white text-sm mx-3 mb-3" role="alert">
                        <span class="alert-icon me-2"><i class="fa-solid fa-circle-check"></i></span>
                        <span class="alert-text">{{ session('success') }}</span>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" style="top: 10px;"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show text-white text-sm mx-3 mb-3" role="alert">
                        <span class="alert-icon me-2"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <span class="alert-text">{{ session('error') }}</span>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" style="top: 10px;"></button>
                    </div>
                @endif

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-3">
                        <table id="example23" class="table table-striped table-bordered align-middle w-100">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 38px;" class="text-center no-sort"></th>
                                    <th class="wd-5p">ID</th>
                                    <th class="wd-25p">User & Profile</th>
                                    <th class="wd-15p">Role</th>
                                    <th class="wd-10p">Status</th>
                                    <th class="wd-10p text-center">Inquiries</th>
                                    <th class="wd-10p">Created</th>
                                    <th class="wd-25p text-center no-sort">Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $my_user)
                                    @php
                                        $hasPic = !empty($my_user->profile_picture) && file_exists(public_path($my_user->profile_picture));
                                        $allPerms = method_exists($my_user, 'getAllPermissions') ? $my_user->getAllPermissions()->pluck('name')->toArray() : [];
                                    @endphp
                                    <tr data-user-id="{{ $my_user->id }}">
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-xs btn-outline-primary mb-0 p-1 px-2 toggle-user-details" title="Expand / Collapse Details">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </td>
                                        <td class="fw-bold text-secondary text-xs text-center">#{{ $my_user->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center px-1 py-1">
                                                <div class="me-2 flex-shrink-0">
                                                    @if($hasPic)
                                                        <img src="{{ asset($my_user->profile_picture) }}" class="avatar avatar-sm rounded-circle shadow-xs" style="width: 34px; height: 34px; object-fit: cover;" alt="{{ $my_user->name }}">
                                                    @else
                                                        <div class="avatar avatar-sm rounded-circle bg-gradient-primary text-white font-weight-bold d-flex align-items-center justify-content-center shadow-xs" style="width: 34px; height: 34px; font-size: 13px;">
                                                            {{ strtoupper(substr($my_user->name ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column justify-content-center text-truncate">
                                                    <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $my_user->name }}</h6>
                                                    <span class="text-xxs text-secondary">{{ $my_user->email }}</span>
                                                </div>
                                            </div>

                                            <!-- Hidden container with full user details for DataTables Child Row -->
                                            <div class="user-detail-payload d-none">
                                                <div class="p-3 bg-white rounded-3 border shadow-xs my-2">
                                                    <div class="row g-3">
                                                        <div class="col-md-3 text-center border-end">
                                                            <div class="mb-2">
                                                                @if($hasPic)
                                                                    <img src="{{ asset($my_user->profile_picture) }}" class="avatar avatar-xl rounded-circle shadow-sm" style="width: 70px; height: 70px; object-fit: cover; border: 3px solid #e2e8f0;" alt="{{ $my_user->name }}">
                                                                @else
                                                                    <div class="avatar avatar-xl rounded-circle bg-gradient-primary text-white font-weight-bold d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px; font-size: 24px;">
                                                                        {{ strtoupper(substr($my_user->name ?? 'U', 0, 1)) }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <h6 class="mb-0 text-sm font-weight-bolder text-dark">{{ $my_user->name }}</h6>
                                                            <span class="text-xxs text-secondary d-block">{{ $my_user->email }}</span>
                                                            <div class="mt-2">
                                                                <a href="{{ route('users.impersonate', $my_user->id) }}" class="btn btn-xs bg-gradient-warning mb-0 text-white shadow-xs">
                                                                    <i class="fa-solid fa-right-to-bracket me-1"></i> Login as User
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5 border-end">
                                                            <h6 class="text-xs font-weight-bold text-uppercase text-secondary mb-2">
                                                                <i class="fa-solid fa-address-card text-primary me-1"></i> Contact & Organization
                                                            </h6>
                                                            <div class="row g-2 text-xs">
                                                                <div class="col-6">
                                                                    <span class="text-secondary d-block text-xxs font-weight-bold">Phone:</span>
                                                                    <span class="font-weight-bold text-dark">{{ $my_user->phone ?: 'Not provided' }}</span>
                                                                </div>
                                                                <div class="col-6">
                                                                    <span class="text-secondary d-block text-xxs font-weight-bold">Office / Location:</span>
                                                                    <span class="font-weight-bold text-dark">{{ $my_user->location ?: 'Main Branch' }}</span>
                                                                </div>
                                                                <div class="col-6">
                                                                    <span class="text-secondary d-block text-xxs font-weight-bold">Assigned Inquiries:</span>
                                                                    <span class="badge bg-light text-dark border">{{ $my_user->total_inquiries ?? 0 }} Total ({{ $my_user->won_inquiries ?? 0 }} Won)</span>
                                                                </div>
                                                                <div class="col-6">
                                                                    <span class="text-secondary d-block text-xxs font-weight-bold">Account Created:</span>
                                                                    <span class="text-dark">{{ $my_user->created_at ? $my_user->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                                                </div>
                                                                <div class="col-12 mt-2">
                                                                    <span class="text-secondary d-block text-xxs font-weight-bold">About / Bio:</span>
                                                                    <p class="text-xxs text-secondary mb-0">{{ $my_user->about_me ?: 'No additional notes or bio recorded.' }}</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <h6 class="text-xs font-weight-bold text-uppercase text-secondary mb-2">
                                                                <i class="fa-solid fa-shield-halved text-success me-1"></i> Role & Granted Permissions ({{ count($allPerms) }})
                                                            </h6>
                                                            <div class="d-flex flex-wrap gap-1" style="max-height: 125px; overflow-y: auto;">
                                                                @forelse($allPerms as $perm)
                                                                    <span class="badge bg-light text-dark text-xxs border px-2 py-1">{{ $perm }}</span>
                                                                @empty
                                                                    <span class="text-xxs text-secondary">No direct or role permissions allotted yet.</span>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @foreach($my_user->roles as $role)
                                                <span class="badge rounded-pill bg-gradient-info">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if ($my_user->status == 1)
                                                <span class="badge rounded-pill bg-gradient-success">Active</span>
                                            @else
                                                <span class="badge rounded-pill bg-gradient-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-weight-bold text-xs">
                                            <span class="badge bg-light text-dark border">{{ $my_user->total_inquiries ?? 0 }}</span>
                                        </td>
                                        <td class="text-xs text-secondary">{{ date('d M Y', strtotime($my_user->created_at)) }}</td>
                                        <td class="text-center text-nowrap">
                                            <!-- Login As Button -->
                                            <a class="btn bg-gradient-warning btn-sm mb-0 me-1 text-white shadow-xs"
                                                href="{{ route('users.impersonate', $my_user->id) }}"
                                                title="Login as {{ $my_user->name }} with allotted permissions">
                                                <i class="fa-solid fa-right-to-bracket me-1"></i> Login As
                                            </a>

                                            <!-- Edit Button -->
                                            <a class="btn bg-gradient-info btn-sm mb-0 me-1"
                                                href="{{ url('users/edit/' . $my_user->id) }}"
                                                title="Edit user details">
                                                <i class="fa fa-pen-to-square me-1"></i> Edit
                                            </a>

                                            <!-- Delete Button -->
                                            <a class="btn bg-gradient-danger btn-sm mb-0" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="{{ $my_user->id }}"
                                                title="Delete user">
                                                <i class="fa fa-trash-can me-1"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this User? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" title="Delete"><i class="fa fa-trash me-1"></i> Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var actionUrl = '{{ url('users/delete') }}/' + id;
                var deleteForm = document.getElementById('deleteForm');
                deleteForm.setAttribute('action', actionUrl);
            });
        }
    });

    $(document).ready(function() {
        var table = $('#example23').DataTable({
            "ordering": true,
            "order": [[1, "desc"]],
            dom: "<'row mb-3'<'col-md-8 d-flex align-items-center gap-2'B l><'col-md-4'f>>t<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
            "buttons": [
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    className: 'btn btn-sm btn-primaryHtml5',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-sm btn-primaryHtml5',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6]
                    }
                }
            ],
            "columnDefs": [
                { "orderable": false, "targets": [0, 7] }
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "language": {
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                "paginate": {
                    "next": '>',
                    "previous": '<'
                }
            },
            responsive: false
        });

        // Expand / Collapse Details Row on clicking the Plus (+) button
        $('#example23 tbody').on('click', '.toggle-user-details', function(e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var icon = $(this).find('i');

            if (row.child.isShown()) {
                // Collapse row
                row.child.hide();
                tr.removeClass('shown details-open');
                icon.removeClass('fa-minus').addClass('fa-plus');
                $(this).removeClass('btn-primary text-white').addClass('btn-outline-primary');
            } else {
                // Expand row
                var payloadHtml = tr.find('.user-detail-payload').html();
                row.child(payloadHtml).show();
                tr.addClass('shown details-open');
                icon.removeClass('fa-plus').addClass('fa-minus');
                $(this).removeClass('btn-outline-primary').addClass('btn-primary text-white');
            }
        });
    });
</script>
@endpush
