@extends('layouts.master')
@section('content')
    <style>
        .cell-1 {
            border-collapse: separate;
            border-spacing: 0 4em;

            border-bottom: 5px solid transparent;
            background-clip: padding-box;
            cursor: pointer
        }



        .table-elipse {
            cursor: pointer
        }

        #demo {
            -webkit-transition: all 0.3s ease-in-out;
            -moz-transition: all 0.3s ease-in-out;
            -o-transition: all 0.3s 0.1s ease-in-out;
            transition: all 0.3s ease-in-out
        }


        .table td.collapse.in {
            display: table-cell;
        }
    </style>
    <div class="az-content-breadcrumb">
        <span>Inquiry</span>
        {{-- <span>Add Airline</span> --}}
        {{-- <span>Forms</span> --}}
        {{-- <span>Form Layouts</span> --}}
    </div>
    <h2 class="az-content-title" style="display: inline"> Edit Inquiry# <span
            class="badge badge-success fs-2">{{ $dec_inq_id }}</span><span>
            <a href="{{ url('inquiry/create') }}" class="btn btn-az-primary" style="float: right">Add Inquiry</a></span>
    </h2>

    <div class="card mt-4">
        <img class="card-img-top" src="holder.js/100px180/" alt="">
        <div class="card-body">
            <h4 class="card-title">Edit Inquiry</h4>
            <div class="row">
                <form action="{{ url('inquiry_edit_update') }}" method="POST">
                    <section>
                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <span style="color: red">{{ $error }}</span>
                            @endforeach
                        @endif
                        <div class="row row-sm">
                            <div class="col-lg-12 mg-t-20 mg-lg-t-0">
                                <div class="form-group">

                                    <label class="form-control-label">Campaign Reference</label>
                                    <select id="campaign" name="campaign" class="form-control" required="required">
                                        <option>Select Customer Campaign</option>
                                        @forelse ($campaigns as $campaign)
                                            <option @if ($get_inquiry->campaign_id == $campaign->id_campaigns) selected @endif
                                                value="{{ $campaign->id_campaigns }}">
                                                {{ $campaign->campaign_name }}
                                            </option>
                                        @empty
                                            No Results Found
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="inq_id" value="{{ $dec_inq_id }}" id="">


                            <div class="row mt-2">
                                <div class="row" id="append_services">

                                </div>
                                <div class="col-lg-5 mg-t-20 mg-lg-t-0">
                                    <div class="form-group">
                                        <label class="form-control-label">Services: <span
                                                style="color:red;">*</span></label>
                                        <select name="services[]" id="services" class="form-control"
                                            required="required">
                                            <option>Select Services </option>
                                            @forelse ($services as $service)
                                                <option value="{{ $service->id_other_services }}">
                                                    {{ $service->service_name }}
                                                </option>
                                            @empty
                                                No Results Found
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="col-lg-6 mg-t-20 mg-lg-t-0">
                                    <div class="form-group">

                                        <label class="form-control-label" style="display:none !important;">Sub Services:</label>
                                        <select style="width: 100%" name="sub_services[]" id="sub_services"
                                            class="js-example-basic-multiple" multiple="multiple">
                                            <option>Select Sub Service</option>

                                        </select>
                                    </div>
                                </div> -->
                                <div class="col-lg-1 mg-t-20 mg-md-t-0">
                                    {{-- <label class="form-control-label">Add More</label> --}}
                                    <button onclick="add_more()" class="btn btn-az-primary mt-4" type="button">Add
                                        More</button>
                                </div>
                            </div>

                            <div class="col-lg-6 mg-t-20 mg-md-t-0 mt-2">
                                <label class="form-control-label">Inquiry Category:</label>
                                <select name="inquiry_category" class="form-control" required="required">
                                    <option value="">Select Inquiry Category</option>
                                    <option @if ($get_inquiry->inquiry_category == 'Economy') selected @endif value="Economy">Economy
                                    </option>
                                    <option @if ($get_inquiry->inquiry_category == 'Standard') selected @endif value="Standard">Standard
                                    </option>
                                    <option @if ($get_inquiry->inquiry_category == '2 - Star') selected @endif value="2 - Star">2 - Star
                                    </option>
                                    <option @if ($get_inquiry->inquiry_category == '3 - Star') selected @endif value="3 - Star">3 - Star
                                    </option>
                                    <option @if ($get_inquiry->inquiry_category == '4 - Star') selected @endif value="4 - Star">4 - Star
                                    </option>
                                    <option @if ($get_inquiry->inquiry_category == '5 - Star') selected @endif value="5 - Star">5 - Star
                                    </option>

                                </select>
                            </div>
                            <div class="col-lg-6 mg-t-20 mg-lg-t-0 mt-2">
                                <div class="form-group ml-2 mt-2">
                                    <label class="az-content-label tx-11 tx-medium tx-gray-600">Sales Person <span
                                            style="color:red;">*</span></label>
                                    <select name="sale_person" class="form-control" id="sale_person">
                                        <option>Select</option>
                                        @forelse ($sale_persons as $sp)
                                            <option @if ($get_inquiry->saleperson == $sp['id']) selected @endif
                                                value="{{ $sp['id'] }}">{{ $sp['name'] }}</option>
                                        @empty
                                            No Results Found
                                        @endforelse
                                    </select>

                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0 mt-2">
                                <div class="form-group">
                                    @csrf
                                    <label class="form-control-label">No Of Adults</label>
                                    <input type="number" value="{{ $get_inquiry->no_of_adults }}" class="form-control"
                                        name="no_of_adults">
                                </div>
                            </div>
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0 mt-2">
                                <div class="form-group">

                                    <label class="form-control-label">No Of Children</label>
                                    <input type="number" value="{{ $get_inquiry->no_of_children }}"
                                        class="form-control" name="no_of_children">
                                </div>
                            </div>
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0 mt-2">
                                <div class="form-group">

                                    <label class="form-control-label">No Of Infants</label>
                                    <input type="number" class="form-control" value="{{ $get_inquiry->no_of_infants }}"
                                        name="no_of_infants">
                                </div>
                            </div>
                        </div>

                        <div class="row row-sm">
                            <div class="col-lg-12 mg-t-20 mg-md-t-0 mt-2">
                                <label class="form-control-label">Travel Date: <span style="color:red;">*</span></label>
                                <input type="text" readonly value="{{ $get_inquiry->travel_date }}"
                                    name="travel_date" class="form-control fc-datepicker" placeholder="MM/DD/YYYY" />
                            </div>
                        </div>
                        <div class="row row-sm float-end">
                            <div class="col-lg-12 mg-t-20 mg-md-t-0 mt-2">
                                <button class="btn btn-az-primary">Update</button>
                            </div>
                        </div>

                    </section>

                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('.select2').select2({});
        $(document).ready(function() {
            $("#reasons").hide();
            $("#status").on("change", function() {
                var val = $(this).val();
                if (val == 5) {
                    $("#reasons").show();
                } else {
                    $("#reasons").hide();
                }
            });
            $('#campaign').on('change', function() {
                var val = $(this).val();
                $.ajax({
                    url: "{{ url('get_campaign_data') }}/" + val,
                    type: "GET",
                    success: function(data) {
                        // alert(data.);
                        // $('#sub_services').html(data);
                        $('#inquiry_type').val(data.inquiry_id);

                    }
                });
            });

            $('#services').on('change', function() {
                var val = $(this).val();
                $.ajax({
                    url: "{{ url('get_sub_services') }}/" + val,
                    type: "GET",
                    success: function(data) {
                        $('#sub_services').html(data);
                    }
                });
            });


        });
        var counti = 1;

        function add_more() {

            counti = counti + 1;
            $.ajax({
                url: "{{ url('add_more_services') }}/" + counti,
                type: 'GET',
                success: function(data) {
                    console.log(data.script)
                    $('#append_services').append(data.data);
                    // $('#append_js').append(data.script);
                    $('#count_id').val(counti);
                    $('.js-example-basic-multiple').select2()
                    $('#services' + counti).on('change', function() {
                        var val = $(this).val();
                        $.ajax({
                            url: "{{ url('get_sub_services') }}/" + val,
                            type: "GET",
                            success: function(data) {
                                console.log(data)
                                $('#sub_services' + counti).html(data);
                            }
                        });
                    });
                }
            });

        }

        $(document).ready(function() {
            var inq_id = "{{ $dec_inq_id }}";

            $.ajax({
                type: "GET",
                url: "{{ url('/append_services_edit') }}/" + inq_id,
                success: function(response) {
                    $("#append_services").html(response.services);
                    $('.js-example-basic-multiple').select2();
                }
            });

        });

        function remove(count_rmv) {

            counti = counti - 1;
            $('.rmv' + count_rmv).remove();
        }

        function remove_echo(count_rmv) {

            counti = counti - 1;
            $('.rmv' + count_rmv).remove();
        }
    </script>
@endpush
