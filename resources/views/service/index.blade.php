@extends('layouts.user_type.auth')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">All Services</h5>
                            </div>
                            <a href="{{ url('services/create') }}" class="btn bg-gradient-primary  mb-2" type="button"><i
                                    class="fa fa-plus"></i>&nbsp; New
                                Services</a>
                        </div>
                    </div>

                    <div class="az-content-body d-flex flex-column">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xl-12">
                                <div class="card card-body pd-20">
                                    <hr>
                                    <div class="table-responsive">
                                        <table id="example23" class="table table-striped table-bordered table-hover nowrap"
                                            cellspacing="1" width="100%">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="wd-10p">S.No</th>
                                                    <th class="wd-20p">Service Name</th>
                                                    <th class="wd-10p">Status</th>
                                                    <th class="wd-10p">Created</th>
                                                    <th class="wd-10p">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($service as $key => $service)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $service->service_name }}</td>
                                                        <td>
                                                            @if ($service->status == 1)
                                                                <span
                                                                    class="badge bg-success">Active</span>
                                                            @else
                                                                <span class="badge bg-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d-m-Y', strtotime($service->created_at)) }}</td>
                                                        <td>

                                                            <a class="btn bg-gradient-info btn-sm mb-0 me-1"
                                                                href="{{ url('services/edit/' . $service->id_other_services) }}">
                                                                <i class="fa-regular fa-pen-to-square me-1"></i>Edit
                                                            </a>



                                                            <a class="btn bg-gradient-danger btn-sm mb-0" data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal"
                                                                data-id="{{ $service->id_other_services }}">
                                                                <i class="fa-solid fa-trash-can me-1"></i>Delete
                                                            </a>


                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="wd-10p">S.No</th>
                                                    <th class="wd-20p">Service Name</th>
                                                    <th class="wd-10p">Status</th>
                                                    <th class="wd-10p">Created</th>
                                                    <th class="wd-10p">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this Sales Reference ?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        document.addEventListener('keydown', function(event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 'a') {
                event.preventDefault(); // Prevent default CTRL + A behavior (Select All)

                // Trigger the click on the "Add Customers" button
                document.getElementById('addbtn').click();
            }
        });
        $(function() {
            oTable = $('#example23').DataTable({
                dom: '<"d-flex justify-content-between align-items-center mb-3"lB>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                language: {
                    paginate: {
                        previous: "<i class='fa fa-chevron-left'></i>",
                        next: "<i class='fa fa-chevron-right'></i>"
                    }
                },
                buttons: [{
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv"></i> CSV',
                        title: 'Services List',
                        className: 'btn btn-primary text-white',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel"></i> Excel',
                        title: 'Services List',
                        className: 'btn btn-primary text-white',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf"></i> PDF',
                        title: 'Services List',
                        className: 'btn btn-primary text-white',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        },
                        orientation: 'landscape'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        title: 'Services List',
                        className: 'btn btn-primary text-white',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        }
                    }
                ],
                responsive: !0,
                lengthMenu: [10, 25, 50, 100]
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var actionUrl = '{{ url('services/delete') }}/' + id;
                var deleteForm = document.getElementById('deleteForm');
                deleteForm.setAttribute('action', actionUrl);
            });
        });
    </script>
@endpush
