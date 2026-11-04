@extends('layouts.user_type.auth')
@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h2 class="az-content-title" style="display: inline"> Other Services</h2>
                            </div>
                            @can('Services add')
                                <a href="{{ url('other_services/create') }}" class="btn btn-az-primary" style="float: right">
                                    <i class="fa-solid fa-plus"></i> Add Other Services
                                </a>
                            @endcan
                        </div>
                    </div>
                    @if (Session('alert'))
                        <div class="alert alert-card alert-{{ Session('alert-class') }}" role="alert">
                            {{ Session('alert') }}
                            <button class="close" type="button" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="az-content-body d-flex flex-column">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xl-12">
                                <div class="card card-body pd-20">
                                    <hr>
                                    <div class="table-responsive">
                                        <table id="example23" class="table table-striped table-bordered table-hover nowrap"
                                            cellspacing="1" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="wd-5p">View</th>
                                                    <th class="wd-5p">S.No</th>
                                                    <th class="wd-20p">Service</th>
                                                    <th class="wd-20p">Description</th>
                                                    <th class="wd-10p">Status</th>
                                                    <th class="wd-10p">Created At</th>
                                                    <th class="wd-10p">Operations</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($other_services as $key => $type)
                                                    <tr class="cell-1" data-toggle="collapse"
                                                        data-target="#demo{{ $type->id_other_services }}">
                                                        <td class="table-elipse" data-toggle="collapse" data-target="#demo">
                                                            <i class="fa fa-ellipsis-h text-black-50"></i>
                                                        </td>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $type->service_name }}</td>
                                                        <td>{{ $type->description }}</td>
                                                        <td>{{ $type->status }}</td>
                                                        <td>{{ date('d-m-Y', strtotime($type['created_at'])) }}</td>
                                                        <td>
                                                            <a class="btn bg-gradient-info btn-sm mb-0 me-1" title="Edit"
                                                                href="{{ url('/other_services/edit/' . \Crypt::encrypt($type->id_other_services)) }}">
                                                                <i class="fa-regular fa-pen-to-square"></i></a>
                                                            <button class="btn bg-gradient-danger btn-sm mb-0" title="Delete"
                                                                data-id="{{ $type->id_other_services }}"
                                                                data-bs-toggle="modal" data-bs-target="#deleteModal"> <i class="fa-solid fa-trash-can"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr id="demo{{ $type->id_other_services }}"
                                                        class="collapse cell-1 row-child">
                                                        <td class="text-center"><i class="fa fa-angle-up"></i></td>
                                                        <th colspan="2">Service Name</th>
                                                        <th>Sub Service</th>
                                                    </tr>
                                                    @php
                                                        $decode = App\other_service::where(
                                                            'parent_id',
                                                            $type->id_other_services,
                                                        )
                                                            ->whereNotNull('parent_id')
                                                            ->get();
                                                    @endphp
                                                    @foreach ($decode as $key => $value)
                                                        <tr id="demo{{ $type->id_other_services }}"
                                                            class="collapse cell-1 row-child">
                                                            <td class="text-center"></td>
                                                            @php
                                                                $service_name = App\other_service::where(
                                                                    'id_other_services',
                                                                    $value->parent_id,
                                                                )->first();
                                                            @endphp
                                                            <td colspan="2">{{ $service_name->service_name }}</td>
                                                            <td colspan="2">{{ $value->service_name }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="wd-5p">View</th>
                                                    <th class="wd-5p">S.No</th>
                                                    <th class="wd-20p">Service</th>
                                                    <th class="wd-20p">Description</th>
                                                    <th class="wd-10p">Status</th>
                                                    <th class="wd-10p">Created At</th>
                                                    <th class="wd-10p">Operations</th>
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
                    Are you sure you want to delete this service?
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
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example2 tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="' + title + '" />');
            });

            $('#example2').DataTable({
                "ordering": true,
                "dom": 'Blfrtip',
                "buttons": ['excel', 'pdf', 'print'],
                responsive: true,
                columnDefs: [{
                    className: 'control',
                    orderable: false,
                    targets: 7
                }],
                initComplete: function() {
                    this.api().columns().every(function() {
                        var that = this;
                        $('input', this.footer()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                }
            });

            var deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var id = button ? button.getAttribute('data-id') : null;
                    if (id) {
                        document.getElementById('deleteForm').setAttribute('action', '{{ url("other_services/delete") }}/' + id);
                    }
                });
            }
        });
    </script>
@endpush
