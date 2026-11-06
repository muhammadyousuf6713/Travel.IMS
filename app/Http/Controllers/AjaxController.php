<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\addon;
use App\airline_inventory;
use App\airline_inventory_temp;
use App\airline_rate;
use App\airlines;
use App\approval_group;
use App\assign_department_user;
use App\campaign;
use App\cities;
use App\countries;
use App\currency_exchange_rate;
use App\customer;
use App\department_team;
use App\escallation;
use App\followup_remark;
use App\hotel_details;
use App\hotel_inventory;
use App\hotel_inventory_temp;
use App\hotel_rate;
use App\hotels;
use App\inquiry;
use App\land_services_type;
use App\Landservicestypes;
use App\main_menu;
use App\Notification;
use App\other_service;
use App\packages;
use App\payment;
use App\payments_account;
use App\quotation;
use App\quotation_approval;
use App\quotation_issuance;
use App\quotations_detail;
use App\remarks;
use App\role_permission;
use App\room_type;
use App\Route;
use App\service_vendor;
use App\Visa_rates;
use Carbon\Carbon;
use Facade\Ignition\Support\Packagist\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use DB;

class AjaxController extends Controller
{
    //  Inventory Controller
    public function save_inventory(Request $request)
    {
        $airline_id = \Crypt::decrypt($request->h_id);
        $store = new airline_inventory_temp();
        $store->airline_id = $airline_id;
        $store->flight_class = $request->flight_class;
        $store->qty = $request->qty;
        $store->cost_price = $request->c_price;
        $store->selling_price = $request->s_price;
        $store->save();
        return response()->json([
            'success' => true,
            'message' => 'Hotel inventory saved',
        ]);
    }

    public function inventory($id, Request $request)
    {
        $airline_id = \Crypt::decrypt($id);

        $airline = airlines::select()->where('id_airlines', $airline_id)->first();
        $airline_inventory = airline_inventory::select()->where('airline_id', $airline_id)->get();
        $all_data = '';
        if ($request->ajax()) {
            $airline_inventory = airline_inventory_temp::select()->where('airline_id', $airline_id)->get();
            foreach ($airline_inventory as $key => $airline_inv) {
                $all_data .= '<tr id="rmv' . $airline_inv->id_airline_inventory_temp . '">

                <td>' . $key = $key + 1 . '</td>
                <td>
                <input type="hidden" name="flight_class[]" value="' . $airline_inv->flight_class . '">
                <input type="hidden" name="qty[]" value="' . $airline_inv->qty . '">
                <input type="hidden" name="cost_price[]" value="' . $airline_inv->cost_price . '">
                <input type="hidden" name="selling_price[]" value="' . $airline_inv->selling_price . '">


                ' . $airline_inv->flight_class . '
                </td>
                <td>
                    ' . $airline_inv->qty . '
                </td>
                <td>
                    ' . $airline_inv->cost_price . '
                </td>
                <td>
                    ' . $airline_inv->selling_price . '
                </td>

                <td>
                    Super Admin
                </td>
                <td>' . date('d-m-Y', strtotime($airline_inv->created_at)) . '</td>

                <td><button type="button"
                        onClick="delete_btn(' . $airline_inv->id_airline_inventory_temp . ',' . $airline_inv->airline_id . ')"
                        class="btn btn-rounded btn-danger" href="#">
                        Delete
                    </button>
            </tr>';
            };

            return response()->json([
                'data' => $all_data,
            ]);
        }
        return view('airlines.airline_inventory', compact('airline', 'airline_inventory'));
    }

    public function get_room_type($id)
    {
        // dd("sds");
        if (request()->edit == 'edit') {
            $hotel_id = \Crypt::decrypt(request()->h_id);
            // dd($hotel_id);
            $hotel_inventory = hotel_inventory::where(['hotel_id' => $hotel_id, 'inventory_type' => 'room', 'inventory_type_id' => $id])->first();
            $room_types = room_type::where('id_room_types', $id)->first();
            echo '<div class="col-md-3">

            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">' . $room_types->name . ' Quantity</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Qty</span>
                    </div>
                    <input type="number" name="qty" class="form-control" value="' . $hotel_inventory->qty . '" required="required">

                </div><!-- input-group -->

            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">' . $room_types->name . ' Cost</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">AMOUNT</span>
                    </div>
                    <input type="text" name="cost" class="form-control" value="' . $hotel_inventory->cost_price . '" required="required">
                    <div class="input-group-append">
                        <span class="input-group-text">.00</span>
                    </div>
                </div><!-- input-group -->

            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">' . $room_types->name . ' Price</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">AMOUNT</span>
                    </div>
                    <input type="text" name="s_price" value="' . $hotel_inventory->selling_price . '" class="form-control">
                    <div class="input-group-append">
                        <span class="input-group-text">.00</span>
                    </div>
                </div><!-- input-group -->

            </div>
        </div>
        <hr>';
        } else {
            $data = '<h3 class="mt-4"> Select Inventory Of ' . $id . '</h3><div class="col-md-3">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">No Of Tickets</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Qty</span>
                    </div>
                    <input type="number" name="qty" class="form-control"  required="required">

                </div><!-- input-group -->

            </div>
        </div>

        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">' . $id . ' Cost Price</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">AMOUNT</span>
                    </div>
                    <input type="text" name="cost" class="form-control"  required="required">
                    <div class="input-group-append">
                        <span class="input-group-text">.00</span>
                    </div>
                </div><!-- input-group -->

            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">' . $id . ' Selling Price</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">AMOUNT</span>
                    </div>
                    <input type="text" name="s_price"  class="form-control">
                    <div class="input-group-append">
                        <span class="input-group-text">.00</span>
                    </div>
                </div><!-- input-group -->

            </div>
        </div>

        </div>
        <div class="row">
        <div class="col-md-12"  style="text-align:right;">
        <button id="save_btn_temp" onclick="save_btn()" type="button" class="btn btn-az-primary">Save</button>
        </div>
        </div>
                <hr>
                ';
            return response()->json([
                'data' => $data,
                'name' => $id
            ]);
        }

        // dd($hotel_inventory);
    }

    public function inventory_delete($id, $id_airline)
    {
        // dd($id .'/'. $id_airline);
        $destroy_airline = airline_inventory_temp::where(['airline_id' => $id_airline, 'id_airline_inventory_temp' => $id])->first();
        //    dd($destroy_hotel);
        $destroy_airline->delete();
        // session()->flash('success', 'Inventory Removed!');
        return response()->json([
            'message' => 'Inventory Removed'
        ]);
        return back();
    }

    public function inventory_destroy($id, $id_airline)
    {
        // dd($id .'/'. $id_airline);
        $inv_id = Crypt::decrypt($id);
        $airline_id = Crypt::decrypt($id_airline);
        $destroy_hotel = airline_inventory::where(['airline_id' => $airline_id, 'id_airline_inventory' => $inv_id])->first();
        //    dd($airline_id);
        $destroy_hotel->delete();
        session()->flash('success', 'Inventory Removed!');

        return back();
    }

    public function autocomplete(Request $request)
    {
        $data = [];

        if ($request->has('q')) {
            $search = $request->q;
            // dd($search);
            $data = Countries::join('cities', 'countries.id_countries', '=', 'cities.country_id')->orwhere('countries.country_name', 'LIKE', '%' . $search . '%')->orwhere('cities.name', 'LIKE', '%' . $search)->select('countries.country_name', 'cities.name')->get();
            // dd($data);
        }
        return response()->json($data);
        // $query = $request->get('query');
        // // dd($query);
        // $data=Countries::join('cities','countries.id_countries','=','cities.country_id')->orwhere("countries.country_name",'LIKE','%'.$query)->orwhere("cities.name",'LIKE','%'.$query)->select('countries.country_name','cities.name')->get();
        // // dd($join_country_city);
        // // $users = User::join('posts', 'users.id', '=', 'posts.user_id')
        // //        ->get(['users.*', 'posts.descrption']);
        // // $data = countries::where("name", "LIKE", $query)->select('name')
        // //     ->get();
        // return response()->json($data);
    }

    public function autocomplete_city(Request $request)
    {
        $data = [];
        $search = trim($request->input('q', $request->input('term', '')));

        // Popular pilgrimage & major international travel hubs shown when search is empty or short
        $popular = [
            'Makkah', 'Madinah', 'Jeddah', 'Riyadh', 'Taif', 'Dammam',
            'Karachi', 'Lahore', 'Islamabad', 'Peshawar', 'Multan', 'Faisalabad',
            'Dubai', 'Abu Dhabi', 'Sharjah', 'Doha', 'Muscat', 'Kuwait City',
            'London', 'Manchester', 'Istanbul', 'Kuala Lumpur', 'New York', 'Toronto'
        ];

        if (empty($search)) {
            foreach ($popular as $p) {
                $data[] = [
                    'id' => $p,
                    'text' => $p,
                    'name' => $p,
                ];
            }
            return response()->json($data);
        }

        // Add matching popular cities first
        foreach ($popular as $p) {
            if (stripos($p, $search) !== false) {
                $data[] = ['id' => $p, 'text' => $p, 'name' => $p];
            }
        }

        // Check for common Makkah / Mecca or Madinah / Madina aliases
        if (stripos($search, 'makk') !== false || stripos($search, 'mecc') !== false) {
            if (!in_array('Makkah', array_column($data, 'id'))) {
                $data[] = ['id' => 'Makkah', 'text' => 'Makkah', 'name' => 'Makkah'];
            }
        }
        if (stripos($search, 'madi') !== false) {
            if (!in_array('Madinah', array_column($data, 'id'))) {
                $data[] = ['id' => 'Madinah', 'text' => 'Madinah', 'name' => 'Madinah'];
            }
        }

        $rows = DB::table('cities')
            ->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere(function ($q) use ($search) {
                if (stripos($search, 'makk') !== false) {
                    $q->where('name', 'LIKE', '%Mecca%');
                } elseif (stripos($search, 'madi') !== false) {
                    $q->where('name', 'LIKE', '%Madina%');
                }
            })
            ->select('cities.name')
            ->limit(50)
            ->get();

        foreach ($rows as $row) {
            $cityName = ($row->name === 'Mecca') ? 'Makkah' : (($row->name === 'Madina') ? 'Madinah' : $row->name);
            if (!in_array($cityName, array_column($data, 'id'))) {
                $data[] = [
                    'id' => $cityName,
                    'text' => $cityName,
                    'name' => $cityName,
                ];
            }
        }

        return response()->json($data);
    }

    // Customer Controller
    public function customer_search(Request $request, $query = null)
    {
        if ($request->ajax()) {
            $bilal = $request->page;
            $artilces = ' ';

            if ($query != null && $query != '') {
                if (is_numeric($query)) {
                    //                     dd($query);
                    $customers = customer::orderBy('id_customers')->where('customer_cell', 'LIKE', '%' . $query . '%')->paginate(20);
                } else {
                    $customers = customer::orderBy('id_customers')->where('customer_name', 'LIKE', '%' . $query . '%')->paginate(20);
                }
                // $artilces = '';

                // dd($customers);
            } else {
                $artilces = ' ';
            }

            if (isset($customers)) {
                foreach ($customers as $result) {
                    //                    $artilces .= '<div class="card bg-light rounded-2"><div class="card-body"><div id="get_cus_details" data-id="' . $result->id_customers . '" onClick="CusDetails()" class="az-contact-item mt-3 clickable-data"><div class="az-img-user"><img src="' . asset('img/default_user_2.png') . '" alt=""></div><div class="az-contact-body"><h6>' . $result->customer_name . '</h6><span class="phone">' . $result->customer_cell . '</span></div></div></div></div><br>';
                    $artilces .= '
    <div class="card shadow-sm border-0 mb-2 clickable-data" style="cursor:pointer;" id="get_cus_details" data-id="' . $result->id_customers . '">
        <div class="d-flex align-items-center p-2">
            <img src="' . asset('img/default_user_2.png') . '" class="rounded-circle border me-2" style="width:40px;height:40px;" alt="User">
            <div class="flex-grow-1">
                <div class="fw-semibold text-dark">' . htmlspecialchars($result->customer_name) . '</div>
                <div class="small text-muted">' . htmlspecialchars($result->customer_cell) . '</div>
            </div>
            <i class="fa fa-chevron-right text-secondary"></i>
        </div>
    </div>';
                }
            }
            return $artilces;
        }
    }

    public function getData(Request $request)
    {
        // Retrieve all data
        // Use DataTables if available or return strict JSON for client-side processing
        // Since the view uses standard ajax: url, we normally need { data: [...] } if dataSrc is default
        // But let's check what it expects. The view columns map directly.
        // Returning simple array might work if dataSrc is empty string.
        // Let's safe-guard by returning { data: ... } standard format.

        $data = customer::with(['salePerson', 'city'])->orderBy('id_customers', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function getCity(Request $request)
    {
        if (is_numeric($request->country_id)) {
            $countries = countries::where('id_countries', $request->country_id)->first();
            $data['cities'] = \App\cities::where('country_id', $countries->id_countries)->get();
        } else {
            $countries = countries::where('name', $request->country_id)->first();
            $data['cities'] = \App\cities::where('country_id', $countries->id_countries)->get();
        }

        return response()->json($data);
        // $cities = "";
        // foreach ($data['cities'] as $key => $value) {
        //     $cities .= "<option " . $value->id . " >" . $value->name . "</option>";
        // }
        // return $cities;
    }

    public function get_customer_details(Request $request)
    {
        // Get customer data
        $data = customer::where('id_customers', $request->id)->first();

        // Get the latest inquiry details for the customer
        $inquiry_details = inquiry::where('customer_id', $request->id)->orderBy('id_inquiry', 'desc')->first();

        // Prepare the output
        $output = '
        <div class="small text-secondary mb-2 fw-semibold"> 
                <i class="fa fa-id-card me-1 text-info"></i> Selected Customer Info:
                </div>
<input type="hidden" value="' . htmlspecialchars($data->id_customers) . '" name="searched_customer_id"/>
<ul class="list-unstyled mb-0 small" style="font-size: 0.95rem;">
    <li class="mb-2">
        <i class="fa fa-user text-primary me-1"></i>
        <span class="fw-semibold">Customer:</span>
        <span class="badge rounded-pill bg-info text-white px-3 py-1 fs-6">' . htmlspecialchars($data->customer_name) . '</span>
    </li>
    <li class="mb-2">
        <i class="fa fa-phone text-success me-1"></i>
        <span class="fw-semibold">Contact#:</span>
        <span class="badge rounded-pill bg-warning text-dark px-3 py-1 fs-6">' . htmlspecialchars($data->customer_cell) . '</span>
    </li>
    <li class="mb-2">
        <i class="fa fa-envelope text-danger me-1"></i>
        <span class="fw-semibold">Email:</span>
        <span class="badge rounded-pill bg-secondary px-3 py-1 fs-6">' . htmlspecialchars($data->customer_email ?: 'N/A') . '</span>
    </li>';

        if ($inquiry_details) {
            $output .= '
    <li class="mb-2">
        <i class="fa fa-file-alt text-info me-1"></i>
        <span class="fw-semibold">Last Inquiry:</span>
        <span class="badge rounded-pill bg-primary px-3 py-1 fs-6">#' . htmlspecialchars($inquiry_details->id_inquiry) . '</span>
    </li>
    <li class="mb-2">
        <i class="fa fa-info-circle text-warning me-1"></i>
        <span class="fw-semibold">Status:</span>
        <span class="badge rounded-pill bg-success px-3 py-1 fs-6">' . htmlspecialchars($inquiry_details->status) . '</span>
    </li>';
        } else {
            $output .= '
    <li class="mb-2">
        <i class="fa fa-file-alt text-info me-1"></i>
        <span class="fw-semibold">Last Inquiry:</span>
        <span class="badge rounded-pill bg-danger px-3 py-1 fs-6">No inquiries available</span>
    </li>';
        }

        $output .= '</ul>';

        // Return the output as JSON
        return response()->json([
            'html' => $output,
            'customer' => $data
        ]);
    }

    public function check_customer_number($cell)
    {
        $get_customer = customer::where('customer_cell', $cell)->first();

        // dd($cell);

        if ($get_customer) {
            return response()->json([
                'getCell' => true
            ]);
        } else {
            return response()->json([
                'getCell' => false
            ]);
        }
    }

    // Unit Type Controller
    // public function getdata()
    // {
    //     $unit_type = UnitType::select('*');
    //     //->where('business_id',auth()->user()->business_id);
    //     return Datatables::of($unit_type)
    //             ->addColumn('action', function ($unit_type) {
    //                 $html = '
    //                     <a  href="'.url('edit_unittype/'.$unit_type->id_unit_type).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i> Edit</a>
    //                     <a onclick="deleteunittype('.$unit_type->id_unit_type.');" href="javascript:void(0);"  class="btn btn-sm btn-danger delete"><i class="fa fa-trash"></i> Delete</a>

    //                 ';
    //                 return $html;
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    // }

    // InquiryController
    function fetch_data(Request $request)
    {
        if ($request->ajax()) {
            // where('customer_name','LIKE', $request->q."%")
            $inquiry = inquiry::query();
            if ($request->city != null) {
                $inquiry = $inquiry->where('city', $request->city);
            }
            if ($request->status != null && $request->status != 0) {
                $inquiry = $inquiry->where('status', $request->status);
            }
            if ($request->inquiry_type != null && $request->inquiry_type != 0) {
                $inquiry = $inquiry->where('inquiry_type', $request->inquiry_type);
            }
            $inquiry = $inquiry->where('customer_name', 'LIKE', $request->q . '%')->paginate(10);
            // dd($inquiry);
            return view('inquiry.pagination', compact('inquiry'))->render();
        }
    }

    public function get_inquiry_remarks(Request $request, $id)
    {
        // dd($request->id);
        $inquiry = inquiry::where('id_inquiry', $request->id)->first();
        $remarks = remark::where('inquiry_id', $request->id)->get();
        $append_remarks = null;
        foreach ($remarks as $rem) {
            $append_remarks .= '<a href="#" class="tickets-card row mt-4">
            <div class="tickets-details col-lg-8 col-12">
                <div class="wrapper">
                    <h5>' . $rem->remarks . '</h5>

                    <div class="badge badge-primary">' . $rem->remarks_status . '</div>
                </div>
                <div class="wrapper text-muted d-none d-md-block">
                    <span>Assigned Date</span>
                    <span>' . $rem->created_at . '</span>

                    <span><i class="typcn icon typcn-time"></i></span>
                </div>
            </div>
            <div class="ticket-float col-lg-2 col-sm-6 d-none d-md-block">

                <button style="visibility: hidden;" class=" btn btn-primary" ><span class="">View Remarks</span></button>
            </div>

        </a>';
        }
        // dd($append_remarks);
        echo '<div class="modal-header">
        <button type="button" onclick="closeModal()" class="close"  data-dismiss="modal" aria-label="Close"><span
        aria-hidden="true">&times;</span></button>
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="hca-modal-header--account-details" id="account-details">
                    <ul>
                        <li>Inquiry#' . $inquiry->id_inquiry . '</li>

                    </ul>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-body">

        <div class="hca-modal-body--banner">

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="hca-modal-body--visit-details pull-left">

                            <h5><b>Inquiry#:</b><u>' . $inquiry->id_inquiry . '</u></h5>
                            <h5><b>Customer</b>: <u>' . $inquiry->customer_name . '</u></h5>
                            <h5><b>Contact</b>: <u>' . $inquiry->contact_1 . '</u></h5>
                            <h5><b>Inquiry Type</b>: <u>' . ($inquiry->inquiryType->type_name ?? '-') . '</u></h5>
                            <h5><b>Travel Date</b>: <u> ' . $inquiry->created_at->format('D d M Y') . '</u></h5>
                            <h5><b>City</b>:<u>' . $inquiry->city . '</u></h5>
                            <h5><b>Sale Reference</b>: <u> ' . $inquiry->sales_reference . '</u></h5>
                            <h5><b>Followup Date</b>: <u> ' . $inquiry->followup_date . '</u></h5>

                        </div>
                    </div>

                </div>

        </div><!-- /.hca-modal-body--banner -->

        <div class="hca-modal-body--main-content">
            <div class="container-sm">
                <div class="hca-modal-body--visit-details-wrap">
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="col-sm-12 col-md-12">

                                <div class="visit-details-section">
                                    <h5 class="visit-title">Progress Remarks</h5>
                                  ' . $append_remarks . '

                                </div>

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="panel panel-default">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>
';
    }

    function get_campaign_data($id)
    {
        $campaigns = campaign::where('id_campaigns', $id)->first();
        $services = other_service::where('parent_id', null)->get();
        // $get_inquiry = inquiry::where('id_inquiry', $inq_id)->first();
        $decode_services = json_decode($campaigns->services_and_sub_services);
        // dd($decode_services);

        //         $main_services = "";
        //         foreach ($decode_services as $key => $value) {
        //             $main_service = other_service::where('id_other_services', $value->service)->first();
        //             // dd($get_sub_services_name);
        //             $main_services .= '<option selected value="' . $main_service->id_other_services . '">
        //                 ' . $main_service->service_name . '
        //             </option>';
        //         }
        // dd($main_services);
        // dd($explode_sub_services);

        $echo_services_data = '';
        $echo_sub_services_data = '';
        // foreach ($services as $key => $service) {
        foreach ($decode_services as $key_main => $value) {
            $echo_sub_services_data = '';

            $main_service = other_service::where('id_other_services', $value->service)->first();
            // dd($get_sub_services_name);
            $echo_main_services_data = '<option selected value="' . $main_service->id_other_services . '">
                                ' . $main_service->service_name . '
                            </option>';

            $get_sub_services = other_service::where('parent_id', $value->service)->get();
            // dd($get_sub_services);
            foreach ($get_sub_services as $key => $sub_service) {
                $echo_sub_services_data .= '<option selected value="' . $sub_service->id_other_services . '">
                ' . $sub_service->service_name . '
            </option>';
            }

            if ($key_main == 0) {
                $key_name = '';
            } else {
                $key_name = $key_main;
            }

            $echo_services_data .= '<div class="col-lg-5 mg-t-20 mg-lg-t-0 rmv' . $key_main . '">
                <div class="form-group">
                    <label class="form-control-label">Services: <span
                            style="color:red;">*</span></label>
                    <select name="services[]" id="services" class="form-control"
                        required="required">
                        <option>Select Services </option>
    ' . $echo_main_services_data . '
                    </select>
                </div>
            </div>
            <div class="col-lg-7 mg-t-20 mg-lg-t-0 rmv' . $key_main . '">
                <div class="form-group">
                    <label class="form-control-label">Sub Services:</label>
                    <select style="width: 100%" name="sub_services' . $key_name . '[]" id="sub_services' . $key_main . '"
                        class="js-example-basic-multiple" multiple="multiple">
    ' . $echo_sub_services_data . '
                    </select>
                </div>
            </div>
           ';
        }
        // }
        // dd($services_option);

        return response()->json([
            'inquiry_id' => $campaigns->inquiry_type,
            'echo_services_data' => $echo_services_data,
        ]);
    }

    function get_sub_services($id)
    {
        // dd($id);
        $sub_services = other_service::where('parent_id', $id)->get();
        $data = "<option value=''>Select Sub Service</option>";
        foreach ($sub_services as $service) {
            $data .= '<option selected value="' . $service->id_other_services . '">' . $service->service_name . '</option>';
        }
        echo $data;
    }

    function get_sub_services_id($id, $inq_id)
    {
        // dd($inq_id);
        $get_inquiry = inquiry::where('id_inquiry', $inq_id)->first();

        $decode_services = json_decode($get_inquiry->services_sub_services);
        $data = "<option value='all'>Select All Service</option>";
        foreach ($decode_services as $key => $value) {
            $explode = explode('/', $value);
            $get_explode_sub_services = $explode[1];
            $services_id[] = $explode[0];
            $explode_sub_services = explode(',', $get_explode_sub_services);
            foreach ($explode_sub_services as $service) {
                $sub_services = other_service::where('id_other_services', $service)->first();
                $data .= '<option value="' . $sub_services->id_other_services . '">' . $sub_services->service_name . '</option>';
            }
        }

        // dd($data);
        // $sub_services = other_service::where('parent_id', $id)->get();

        // dd($data);
        return response()->json([
            'data' => $data
        ]);
        // echo $data;
    }

    // Inquiry Controller

    public function add_more_services($count)
    {
        $services = other_service::where('parent_id', null)->get();
        $services_option = '<option>Select Services</option>';
        // dd($services);
        foreach ($services as $key => $value) {
            if ($value->id_other_services != null) {
                $services_option .= '<option value="' . $value->id_other_services . '">' . $value->service_name . '</option>';
            }
        }
        // dd($services_option);
        $data = '<div class="col-lg-5 mg-t-20 mg-lg-t-0 rmv' . $count . '">
        <div class="form-group">

            <label class="form-control-label">Services: <span
                    style="color:red;">*</span></label>
            <select name="services[]" onchange="onchange_services(' . $count . ')" id="services' . $count . '" class="form-control" required="required">
                ' . $services_option . '
            </select>
        </div>
        </div>
        <div class="col-lg-6 mg-t-20 mg-lg-t-0 rmv' . $count . '" >
        <div class="form-group">

            <label class="form-control-label">Sub Services:</label>
            <select style="width: 100%" name="sub_services' . $count . '[]" id="sub_services' . $count . '"
                class="js-example-basic-multiple" multiple="multiple">
                <option>Select Sub Service</option>

            </select>
        </div>
        </div>
        <div class="col-lg-1 mg-t-20 mg-md-t-0 rmv' . $count . '">

        <button onclick="remove(' . $count . ')" class="btn btn-danger mt-4" type="button">Remove</button>
        </div>
         ';

        $script = ' <script>
         $("#services' . $count . '").on("change", function() {

           var val = $(this).val();
           $.ajax({
               url: "{{ url("get_sub_services") }}/" + val,
               type: "GET",
               success: function(data) {X
                   $("#sub_services' . $count . '").html(data);
               }
           });
       });
         </script> ';
        // dd($services_option);
        return response()->json([
            'data' => $data,
            'script' => $script,
            'count' => $count
        ]);
    }

    public function add_more_services_users($count)
    {
        $services = other_service::where('parent_id', null)->get();
        $services_option = '';
        foreach ($services as $key => $value) {
            $services_option .= '<option value="' . $value->id_other_services . '">' . $value->service_name . '</option>';
        }

        $data = '<div class="col-lg-5 mg-t-20 mg-lg-t-0 rmv' . $count . '">
        <div class="form-group">

            <label class="form-control-label">Services: <span
                    style="color:red;">*</span></label>
            <select name="services[]" id="services' . $count . '" class="form-control" required="required">
                <option>' . $services_option . '</option>

            </select>
        </div>
        </div>
        <div class="col-lg-6 mg-t-20 mg-lg-t-0 rmv' . $count . '" >
        <div class="form-group">

            <label class="form-control-label">Sub Services:</label>
            <select style="width: 100%" name="sub_services' . $count . '[]" id="sub_services' . $count . '"
                class="js-example-basic-multiple" multiple="multiple">
                <option>Select Sub Service</option>

            </select>
        </div>
        </div>
        <div class="col-lg-1 mg-t-20 mg-md-t-0 rmv' . $count . '">

        <button onclick="remove(' . $count . ')" class="btn btn-danger mt-4" type="button">Remove</button>
        </div>
         ';

        $script = ' <script>
         $("#services' . $count . '").on("change", function() {

           var val = $(this).val();
           $.ajax({
               url: "{{ url("get_sub_services") }}/" + val,
               type: "GET",
               success: function(data) {X
                   $("#sub_services' . $count . '").html(data);
               }
           });
       });
         </script> ';
        // dd($services_option);
        return response()->json([
            'data' => $data,
            'script' => $script,
            'count' => $count
        ]);
    }

    // Quotation Controller

    public function get_inquiry_from_id($inq_id)
    {
        $inquiry = inquiry::where('id_inquiry', $inq_id)->first();
        $customer = customer::where('id_customers', $inquiry->customer_id)->first();
        $get_sub_service_name_data = "<option value=''>Select</option>";
        // dd($customer)
        $services = json_decode($inquiry->services_sub_services);
        foreach ($services as $key => $value) {
            $decode = explode('/', $value);
            $services_ids[] = $decode[0];
        }
        foreach ($services as $key => $value) {
            $decode1 = explode('/', $value);
            $sub_services_id1 = $decode[1];
            $sub_services_id_decode = explode(',', $sub_services_id1);

            foreach ($sub_services_id_decode as $key => $value) {
                $get_sub_service_name = other_service::where('id_other_services', $value)->first();
                $get_sub_service_name_data .= '<option value="' . $get_sub_service_name->id_other_services . '">' . $get_sub_service_name->service_name . '</option>';
            }
        }

        // dd($get_sub_service_name_data);
        $get_service_name_data = '';
        foreach ($services_ids as $key => $value) {
            $get_service_name = other_service::where('id_other_services', $value)->first();
            $get_service_name_data .= '<span class="badge badge-pill badge-success">' . $get_service_name->service_name . '</span>';
        }

        // foreach ($sub_services_id_decode as $key => $value) {
        //     $get_service_name = other_service::where('id_other_services', $value)->first();
        //     // dd($get_service_name);
        //     $get_service_name_data .= '<option value="">' . $get_service_name->service_name . '</option>';
        // }
        return response()->json([
            'inquiry' => $inquiry,
            'customer' => $customer,
            'services_name' => $get_service_name_data,
            'services_id' => $get_service_name->id_other_services,
            'sub_services' => $get_sub_service_name_data,
        ]);
    }

    public function get_quotations_services(Request $request)
    {
        $data = [];

        if ($request->has('q')) {
            $search = $request->q;
            // dd($search);
            $data = Countries::join('cities', 'countries.id_countries', '=', 'cities.country_id')->orwhere('countries.country_name', 'LIKE', '%' . $search)->orwhere('cities.name', 'LIKE', '%' . $search)->select('countries.country_name', 'cities.name')->get();
            // dd($data);
        }
        return response()->json($data);
        // $query = $request->get('query');
        // // dd($query);
        // $data=Countries::join('cities','countries.id_countries','=','cities.country_id')->orwhere("countries.country_name",'LIKE','%'.$query)->orwhere("cities.name",'LIKE','%'.$query)->select('countries.country_name','cities.name')->get();
        // // dd($join_country_city);
        // // $users = User::join('posts', 'users.id', '=', 'posts.user_id')
        // //        ->get(['users.*', 'posts.descrption']);
        // // $data = countries::where("name", "LIKE", $query)->select('name')
        // //     ->get();
        // return response()->json($data);
    }

    public function get_package_from_sub_services($services_id, $inquiry_id, $count)
    {
        $packages = packages::where('package_status', 1)->get();

        foreach ($packages as $key => $value) {
            $decode = json_decode($value->services_and_sub_services);

            foreach ($decode as $key => $value2) {
                $explode = explode('/', $value2);
            }

            if ($explode[0] == $services_id) {
                $get_package_id[] = $value->id_packages;
            }
        }
        $get_package_id_unique = array_unique($get_package_id);

        $get_inquiry = inquiry::where('id_inquiry', $inquiry_id)->first();
        $no_of_persons = $get_inquiry->no_of_infants + $get_inquiry->no_of_childrens + $get_inquiry->no_of_adults;
        $travel_date = $get_inquiry->travel_date;
        // dd($get_inquiry->travel_date);
        foreach ($get_package_id_unique as $key => $value) {
            $get_package = packages::where('id_packages', $value)->orwhere('from_date', $travel_date)->where('no_of_persons', $no_of_persons)->first();
        }
        $package_details_data = '<tr id="remove_row' . $count . '"><td>1</td><td>
        <input type="date" value="' . $travel_date . '"  class="form-control">
        </td><td>
        <input type="date"  class="form-control">
        </td><td>
        <input style="width:100px" id="no_of_persons' . $count . '" type="text" value="' . $no_of_persons . '" class="form-control">
        </td><td>
        <input style="width:100px" id="sub_total' . $count . '" type="text" value="' . $no_of_persons * $get_package->package_price . '" class="form-control">
        </td><td>
        <input  style="width:100px" id="discount' . $count . '" type="text" value="' . $no_of_persons * $get_package->package_price . '" class="form-control">
        </td><td>
        <input id="total' . $count . '" style="width:100px" type="text" value="' . $no_of_persons * $get_package->package_price . '" class="form-control">
        </td>
        <td><button type="button" class="btn btn-danger" onclick="remove_row(' . $count . ')">X</button></td></tr>';

        return response()->json([
            'table_details' => $package_details_data,
        ]);
        // foreach ($decode as $key => $value2) {
        //     $explode = explode('/', $value2);
        // }
    }

    public function get_quotations_sub_services($sub_services_id, $services_id, $inquiry_id, $count)
    {
        $get_services_name = other_service::where('id_other_services', $sub_services_id)->first();
        //    dd($get_services_name);
        if ($get_services_name->service_name == 'VISA') {
            $services_name = 'VISA';
        }
        if ($get_services_name->service_name == 'Hotel') {
            $services_name = 'Hotel';
        }
        if ($get_services_name->service_name == 'Air Ticket') {
            $services_name = 'Air-Ticket';
        }
        return response()->json([
            'services_name' => $services_name,
        ]);
    }

    public function get_room_types_hotel_inv($hotel_inv_id)
    {
        $get_hotel_inventory = hotel_inventory::where('id_hotel_inventory', $hotel_inv_id)->first();
        $decode = json_decode($get_hotel_inventory->total_entries);
        $data = '';
        foreach ($decode as $key => $value) {
            $get_room_name = room_type::where('id_room_types', $value->room_type)->first();
            $data .= '<option value="' . $value->room_type . '">' . $get_room_name->name . '</option>';
        }
        $get_all_currency = currency_exchange_rate::where('status', 1)->get();
        $currency_data = '';
        foreach ($get_all_currency as $key => $value) {
            $currency_data .= '<option value="' . $value->currency_rate . '">' . $value->currency_name . '</option>';
        }
        return response()->json([
            'room_type' => $data,
            'currency' => $currency_data,
        ]);
    }

    public function get_hotel_inv_details($hotel_inv_id, $room_type_id)
    {
        $get_hotel_inventory = hotel_inventory::where('id_hotel_inventory', $hotel_inv_id)->first();
        $decode = json_decode($get_hotel_inventory->total_entries);
        foreach ($decode as $key => $value) {
            $value->room_type == $room_type_id;
            $final_value = $value;
        }
        // dd($final_value);
        return response()->json([
            'room_type' => $final_value
        ]);
    }

    public function get_all_currency()
    {
        $get_all_currency = currency_exchange_rate::where('status', 1)->get();
        $data = '';
        foreach ($get_all_currency as $key => $value) {
            $data .= '<option value="' . $value->currency_rate . '">' . $value->currency_name . '</option>';
        }
        // dd($final_value);
        return response()->json([
            'currency' => $data
        ]);
    }

    public function change_flight_class($airline_inv_id, $flight_class)
    {
        $get_airline_data = airline_inventory::where('id_airline_inventory', $airline_inv_id)->first();
        $decode = json_decode($get_airline_data->all_entries);
        $data = '';
        // dd('sdsdkj');
        foreach ($decode as $key => $value) {
            $value->flight_class == $flight_class;
            $final_value = $value;
        }
        // dd($final_value);
        return response()->json([
            'inv_entry_details' => $final_value
        ]);
    }

    // Vendor Start

    public function add_vendor_contact_details($count)
    {
        echo '<div class="col-md-4 rmv' . $count . '">
        <div class="form-group">
            <label class="az-content-label tx-11 tx-medium tx-gray-600">Select Country City</label>
            <select name="country_city[]" id="country-dropdown2" class="form-control livesearch">
            </select>
        </div>
    </div>
    <div class="col-md-4 rmv' . $count . '">
        <div class="form-group">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Contact Name</label>
                <input type="text" name="contact_name[]" class="form-control numeric" required />
            </div>
        </div>
    </div>
    <div class="col-md-2 rmv' . $count . '">
        <div class="form-group">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Contact Phone.</label>
                <input type="text" name="contact_phone[]" class="form-control numeric" required />
            </div>
        </div>
    </div>
    <div class="col-md-2 mt-4 rmv' . $count . '">
        <button type="button" onclick="add_vendor_details()"  class="btn btn-az-primary">Add</button>
        <button type="button" onclick="remove_vendor(' . $count . ')" class="btn btn-danger">Remove</button>
    </div>

    ';
    }

    public function get_department_users($dep_id)
    {
        $get_dep_users = assign_department_user::where('department_id', $dep_id)->where('is_head', 0)->select('user_id')->get();
        foreach ($get_dep_users as $key => $value) {
            $user = User::where('id', $value->user_id)->first();
            $dep_users[] = $user;
        }

        $dep_users_options = "<option value=''>Select</option>";
        foreach ($dep_users as $key => $value) {
            $dep_users_options .= '<option value="' . $value->id . '" >' . $value->name . '</option>';
        }

        return response()->json([
            'dep_users' => $dep_users_options
        ]);
    }

    public function parsing_details(request $request, $append_count)
    {
        $count_of_rows = count($request->data);
        // echo $time; // Output: 13:20
        $implode_data = [];

        // dd($request->data);
        foreach ($request->data as $key => $row) {
            $get_data[] = substr($row, 0, 2) . ' ' . substr($row, 3);
            $format_data[] = preg_replace('/\s+/', ' ', $get_data[$key]);

            if ($format_data != null) {
                $implode_data[] = explode(' ', $format_data[$key]);
            }
        }
        // dd($format_data);

        // dd($format_second);
        // dd($format_data);
        // dd($implode_data);
        // dd($implode_data);
        $airlines = airlines::all();
        $all_types_rate = room_type::where('status', 'Active')->get();
        $currency_rates = currency_exchange_rate::all();
        $addon = addon::where('status', 1)->get();
        // dd($addon);

        $selected_airline = '';
        $airline_options = '';
        foreach ($implode_data as $lg) {
            // echo $lg[5];
            $select_airline[] = $lg[0];
            $get_arrival_val = $lg[4];
            $get_departure_val = $lg[4];
            $hour = substr($get_arrival_val, 0, 2);
            $minute = substr($get_arrival_val, 2);
            $get_arrival_time[] = $hour . ':' . $minute;
            $hour_d = substr($get_departure_val, 0, 2);
            $minute_d = substr($get_departure_val, 2);
            $get_departure_time[] = $hour_d . ':' . $minute_d;
        }
        // exit();
        $room_type_options = '';
        $currency_rate_options = '';
        $selected_airline_count = 0;
        $addon_options = '';

        // exit();
        foreach ($all_types_rate as $key => $value) {
            $room_type_options .= "<option value='" . $value->id_room_types . "'>" . $value->name . '</option>';
        }
        foreach ($currency_rates as $key => $value) {
            $currency_rate_options .= "<option data='" . $value->currency_name . "' value='" . $value->currency_rate . "'>" . $value->currency_name . '</option>';
        }
        foreach ($addon as $key => $value) {
            $addon_options .= "<option id='addon_option' value='" . $value->id_addons . "'>" . $value->addon_name . '</option>';
        }
        // dd($implode_data);
        $all_legs = '';
        $count = 0;
        // dd($implode_data);
        foreach ($implode_data as $key_lg => $lg) {
            foreach ($airlines as $key => $value) {
                if ($lg[0] == $value->IATA) {
                    $selected_airline = 'selected';
                } else {
                    $selected_airline = '';
                }
                $airline_options .= '<option ' . $selected_airline . " value='" . $value->id_airlines . "'>" . $value->Airline . '</option>';
            }
            $str_date = strtotime($lg[2]);
            $flight_date = date('Y-m-d H:i:s', $str_date);
            $count = $count + 1;
            // Get Airline Destination Work
            $get_arrival_destination = substr($lg[3], 0, 3);
            $get_departure_destination = substr($lg[3], 3);
            $city_arrival = cities::where('city_code', $get_arrival_destination)->first();
            // print_r($city_arrival->country_id);
            if ($city_arrival->country_id != null) {
                $country_arrival = countries::where('id_countries', $city_arrival->country_id)->first();
            }
            $city_departure = cities::where('city_code', $get_departure_destination)->first();
            $country_departure = countries::where('id_countries', $city_departure->country_id)->first();
            // dd($city_arrival);
            // End Get Airline Destination Work
            $all_legs .= '<table class="table table-striped mt-2 table-inverse table-responsive airline_table' . $key_lg . '">
            <thead class="thead-inverse mt-2">
                <tr>
                    <th>Airline Name</th>
                    <th>Flight Number</th>
                    <th>Flight Date</th>
                    <th>Arrival Destination</th>
                    <th>Departure Destination</th>
                    <th>Arrival Time</th>
                    <th>Departure Time</th>
                    <th>Ticket Type</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <div id="append_airline_destination_tr">
                    <tr>
                    <input type="hidden" class="airline_inv_id"  name="air_ticket[' . $append_count . '][legs_count][airline_inv_id][]" id="airline_inv_id' . $append_count . '">
                        <td> <select style="width:150px" class="form-control select2' . $append_count . '" name="air_ticket[' . $append_count . '][legs_count][airline_name][]"  id="airline_name' . $append_count . '">' . $airline_options . '</select></td>
                        <td><input style="width:100px" value="' . $lg[1] . '" type="text" id="flight_number' . $append_count . '" onchange="airline_calculate()" name="air_ticket[' . $append_count . '][legs_count][flight_number][]" class="form-control"></td>
                        <td><input style="width:100px" type="text" readonly value="' . $flight_date . '" placeholder="MM/DD/YYYY" id="airline_arrival_date' . $append_count . '" onchange="airline_calculate()" name="air_ticket[' . $append_count . '][legs_count][airline_arrival_date][]" class="form-control fc-datepicker' . $append_count . ' "></td>
                        <td><select class="form-control livesearch_for_airline_destination' . $append_count . ' w-100" name="air_ticket[' . $append_count . '][legs_count][airline_arrival_destination][]" id="airline_arrival_destination' . $append_count . '">
<option value="' . $city_arrival->id . '">' . $country_arrival->country_name . '-' . $city_arrival->name . '</option>
                        </select></td>
                        <td><select class="form-control livesearch_for_airline_destination' . $append_count . '" name="air_ticket[' . $append_count . '][legs_count][airline_departure_destination][]" id="airline_departure_destination' . $append_count . '">
                        <option value="' . $city_departure->id . '">' . $country_departure->country_name . '-' . $city_departure->name . '</option>
                        </select></td>
                        <td><input style="width:100px" value="' . $get_arrival_time[$key_lg] . '" type="time" id="arival_time' . $append_count . '" onchange="airline_calculate()" name="air_ticket[' . $append_count . '][legs_count][arrival_time][]" class="form-control "></td>
                        <td><input style="width:100px" value="' . $get_departure_time[$key_lg] . '" type="time" id="departure_time' . $append_count . '" onchange="airline_calculate()" name="air_ticket[' . $append_count . '][legs_count][departure_time][]" class="form-control "></td>

                        <input type="hidden" class="airline_sum_legs" id="airline_sum_legs' . $append_count . '" name="airline_sum_legs">
                        <td><select class="form-control select2" onchange="on_change_on_flight_class()" name="air_ticket[' . $append_count . '][legs_count][airline_flight_class][]" id="airline_flight_class' . $append_count . '"><option value="Economy">Economy</option><option value="Premium Economy">Premium Economy</option><option value="Buisness">Buisness</option><option value="First Class">First Class</option></select></td>
                       <td> <button class="btn btn-danger" type="button" onclick="remove_airline(' . $key_lg . ')" ><i class="fa fa-trash"><i></button></td>

                        </tr>
                </div>
            </tbody>
        </table>';
        }

        return response()->json([
            'parsing_legs' => $all_legs,
        ]);
    }

    public function get_inventory_details_airline($id, $append_count, $inq_id)
    {
        $get_inquiry = inquiry::where('id_inquiry', $inq_id)->first();
        // $no_of_person=$get_inquiry->no_of_infants+$get_inquiry->no_of_children+$get_inquiry->no_of_adults;
        // dd($no_of_person);
        $get_now_date = Carbon::now();
        $get_airline_inv = airline_inventory::where('airline_id', $id)->whereDate('arrival_date', '>=', $get_now_date)->get();
        $get_airline_name = airlines::where('id_airlines', $id)->select('Airline')->first();
        $inv_details_table = '';
        foreach ($get_airline_inv as $inv) {
            $get_airline_entries = json_decode($inv->all_entries);
            // dd($get_airline_entries);
            $entries_html = '';
            foreach ($get_airline_entries as $entry) {
                $entries_html .= '<tr><td><span>' . $entry->flight_class . '</span></td>
 <td><span>' . $entry->qty . '</span></td>
 <td><span>' . $entry->cost_price . '</span></td>
 <td><span>' . $entry->selling_price . '</span></td>
 </tr>';
            }

            $inv_details_table .= '<table class="table table-striped mt-2 table-inverse table-responsive" >
            <thead class="thead-inverse mt-2">
                <tr>
                <th>Batch No</th>
                <th>Flight Number</th>
                <th>Description</th>
                <th>Arrival Date</th>
                <th>Departure Date</th>
                <th>Arrival Destination</th>
                <th>Departure Destination</th>
                <th>Add+</th>
                </tr>
            </thead>
            <tbody>
                <div >
                    <tr>
                        <td><span>' . $inv->batch_no . '</span></td>
                        <td><span>' . $inv->flight_no . '</span></td>
                        <td><table class="table table-striped mt-2 table-inverse table-responsive" >
                        <thead class="thead-inverse mt-2">
                            <tr>
                            <th>Flight Class</th>
                            <th>Qty</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <div >

' . $entries_html . '

                            </div>
                        </tbody>
                    </table></td>
                    <td><span>' . $inv->arrival_date . '</span></td>
                    <td><span>' . $inv->departure_date . '</span></td>
                    <td><span>' . $inv->arrival_destination . '</span></td>
                    <td><span>' . $inv->departure_destination . '</span></td>
                    <td><span><button type="button" onclick="add_airlrine_inventory(' . $append_count . ',' . $inv->id_airline_inventory . ')" class="btn btn-success text-white"><i class="fa fa-plus"></i></button></span></td>
                    </tr>
                </div>
            </tbody>
        </table>';
        }

        return response()->json([
            'airline_inventory_table' => $inv_details_table,
            'airline_name' => $get_airline_name->Airline
        ]);
    }

    public function get_airline_rates($append_count)
    {
        $get_now_date = Carbon::now();
        $get_rates = airline_rate::where('status', 1)->get();
        // dd($get_rates);

        $inv_details_table = '';
        foreach ($get_rates as $rate) {
            // dd($rate);
            $get_airline_name = airlines::where('id_airlines', $rate->airline_id)->select('Airline')->first();
            $entries_html = '';
            $inv_details_table .= '<table id="example2" class="table table-striped mt-2 table-inverse table-responsive" >
            <label>Adults </label><input checked id="adult_put_rates" type="checkbox"  /> <label>Children </label> <input checked id="children_put_rates" type="checkbox"  /> <label>infant </label> <input checked id="infant_put_rates" type="checkbox"  />
            <thead class="thead-inverse mt-2">
                <tr>
                <th>Airline Name</th>
                <th>Flight Class</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>From Location</th>
                <th>To Location</th>
                <th>Cost Price</th>
                <th>Add+</th>
                </tr>
            </thead>
            <tbody>
                <div >
                    <tr>
                        <td><span>' . $get_airline_name->Airline . '</span></td>
                        <td><span>' . $rate->flight_class . '</span></td>
                    <td><span>' . $rate->from_date . '</span></td>
                    <td><span>' . $rate->to_date . '</span></td>
                    <td><span>' . $rate->from_location . '</span></td>
                    <td><span>' . $rate->to_location . '</span></td>
                    <td><span>' . $rate->selling_price . '</span></td>
                    <td><span><button type="button" onclick="add_airline_rates(' . $append_count . ',' . $rate->id_airline_rates . ')" class="btn btn-success text-white"><i class="fa fa-plus"></i></button></span></td>
                    </tr>
                </div>
            </tbody>
        </table>';
        }

        return response()->json([
            'airline_inventory_table' => $inv_details_table,
            'airline_name' => $get_airline_name->Airline
        ]);
    }

    public function get_hotel_rates($id, $append_count, $room_type)
    {
        $get_now_date = Carbon::now();
        $get_rates = hotel_rate::where('hotel_id', $id)->join('room_types', 'room_types.id_room_types', 'hotel_rates.room_type_id')->where('hotel_rates.room_type_id', $room_type)->select('*', 'room_types.name as room_name')->get();
        // dd($get_rates);
        // dd($get_rates);
        $get_hotel_name = hotels::where('id_hotels', $id)->select('hotel_name')->first();
        // dd($get_hotel_name);
        $inv_details_table = '';
        foreach ($get_rates as $rate) {
            // dd($rate);
            $entries_html = '';
            $inv_details_table .= '
            <label>Adults </label><input checked id="adult_put_rates_hotel" type="checkbox"  /> <label>Children </label> <input checked id="children_put_rates_hotel" type="checkbox"  /> <label>infant </label> <input checked id="infant_put_rates_hotel" type="checkbox"  />
            <table class="table table-striped mt-2 table-inverse table-responsive" >
            <thead class="thead-inverse mt-2">
                <tr>
                <th>Hotel Name</th>
                <th>Room Type</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Cost Price</th>
                <th>Add+</th>
                </tr>
            </thead>
            <tbody>
                <div >
                    <tr>
                        <td><span>' . $get_hotel_name->hotel_name . '</span></td>
                        <td><span>' . $rate->room_name . '</span></td>
                    <td><span>' . $rate->from_date . '</span></td>
                    <td><span>' . $rate->to_date . '</span></td>
                    <td><span>' . $rate->selling_price . '</span></td>
                    <td><span><button type="button" onclick="add_hotel_rates(' . $append_count . ',' . $rate->id_hotel_rates . ')" class="btn btn-success text-white"><i class="fa fa-plus"></i></button></span></td>
                    </tr>
                </div>
            </tbody>
        </table>';
        }

        return response()->json([
            'hotel_inventory_table' => $inv_details_table,
            'airline_name' => $get_hotel_name->hotel_name
        ]);
    }

    public function add_airline_rates($airline_rate)
    {
        $get_rates = airline_rate::where('id_airline_rates', $airline_rate)->select('cost_price', 'selling_price')->first();
        // dd($get_rates);

        $inv_details_table = '';
        return response()->json([
            'cost_price' => $get_rates->cost_price,
            'selling_price' => $get_rates->selling_price,
        ]);
    }

    public function add_hotel_rates($hotel_rate)
    {
        $get_rates = hotel_rate::where('id_hotel_rates', $hotel_rate)->select('cost_price', 'selling_price', 'room_type_id')->first();
        $get_room_type = room_type::where('id_room_types', $get_rates->room_type_id)->select('no_of_beds')->first();
        // dd($get_room_type->no_of_beds);
        // dd($get_rates);
        // dd($get_rates);
        // $get_airline_name = airlines::where('id_airlines', $id)->select('Airline')->first();
        $inv_details_table = '';
        return response()->json([
            'cost_price' => $get_rates->selling_price,
            'no_of_beds' => $get_room_type->no_of_beds
        ]);
    }

    public function get_inventory_details_hotel($id, $append_count, $inq_id)
    {
        $get_inquiry = inquiry::where('id_inquiry', $inq_id)->first();
        // $no_of_person=$get_inquiry->no_of_infants+$get_inquiry->no_of_children+$get_inquiry->no_of_adults;
        // dd($no_of_person);
        $get_now_date = Carbon::now()->format('m/d/Y');
        // dd($get_now_date);
        $get_hotel_inv = hotel_inventory::where('hotel_id', $id)->where('from_date', '>=', $get_now_date)->get();
        // dd($id);

        $inv_details_table = '';
        foreach ($get_hotel_inv as $inv) {
            $get_hotel_entries = json_decode($inv->total_entries);
            // dd($get_airline_entries);
            $entries_html = '';
            foreach ($get_hotel_entries as $entry) {
                $entries_html .= '<tr><td><span>' . $entry->room_type . '</span></td>
                <td><span>' . $entry->qty . '</span></td>
                <td><span>' . $entry->beds . '</span></td>
 <td><span>' . $entry->cost_price . '</span></td>
 <td><span>' . $entry->selling_price . '</span></td>
 </tr>';
            }

            $inv_details_table .= '<table class="table table-striped mt-2 table-inverse table-responsive" >
            <thead class="thead-inverse mt-2">
                <tr>
                <th>Batch No</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Description</th>
                <th>Add+</th>
                </tr>
            </thead>
            <tbody>
                <div >
                    <tr>
                        <td><span>' . $inv->batch_number . '</span></td>
                        <td><span>' . $inv->from_date . '</span></td>
                        <td><span>' . $inv->to_date . '</span></td>
                        <td><table class="table table-striped mt-2 table-inverse table-responsive" >
                        <thead class="thead-inverse mt-2">
                            <tr>
                            <th>Room Type</th>
                            <th>Qty</th>
                            <th>Beds</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <div >

' . $entries_html . '
                            </div>
                        </tbody>
                    </table></td>
                    <td><span><button type="button" onclick="add_hotel_inventory(' . $append_count . ',' . $inv->id_hotel_inventory . ')" class="btn btn-success text-white"><i class="fa fa-plus"></i></button></span></td>
                    </tr>
                </div>
            </tbody>
        </table>';
        }

        return response()->json([
            'airline_inventory_table' => $inv_details_table
        ]);
    }

    public function add_airline_inv_details($inv_id, $append_count)
    {
        $get_airline_inv = airline_inventory::where('id_airline_inventory', $inv_id)->first();
        $arrival_time = Carbon::parse($get_airline_inv->arrival_date)->format('H:i:s');
        $departure_time = Carbon::parse($get_airline_inv->departure_date)->format('H:i:s');
        // $departure_time=2023-06-04 00:00:00;
        return response()->json([
            'airline_id' => $get_airline_inv->airline_id,
            'flight_no' => $get_airline_inv->flight_no,
            'arrival_date' => $get_airline_inv->arrival_date,
            'arrival_destination' => "<option value='$get_airline_inv->arrival_destination'>$get_airline_inv->arrival_destination</option>",
            'departure_destination' => "<option value='$get_airline_inv->departure_destination'>$get_airline_inv->departure_destination</option>",
            'arrival_time' => $arrival_time,
            'departure_time' => $departure_time,
            'append_count' => $append_count,
        ]);
    }

    public function add_hotel_inv_details($inv_id, $append_count)
    {
        $get_hotel_inv = hotel_inventory::where('id_hotel_inventory', $inv_id)->first();
        $get_hotel_inv_entries = json_decode($get_hotel_inv->total_entries);
        foreach ($get_hotel_inv_entries as $entry) {
            $room_types_id[] = $entry->room_type;
        }
        $uniq_ids = array_unique($room_types_id);
        $from_date = Carbon::parse($get_hotel_inv->form_date)->format('d/m/Y');
        $to_date = Carbon::parse($get_hotel_inv->to_date)->format('d/m/Y');
        $all_types_rate = room_type::where('status', 'Active')->whereIn('id_room_types', $uniq_ids)->get();
        // dd($all_types_rate);
        $room_type_options = "<option value=''>Select</option>";
        foreach ($all_types_rate as $key => $value) {
            $room_type_options .= "<option value='" . $value->id_room_types . "'>" . $value->name . '</option>';
        }
        $append_inv_input = "<input type='hidden' class='hotel_inv_id' data-id='" . $append_count . "' s_type='hotel' value='" . $get_hotel_inv->id_hotel_inventory . "' name='hotel[" . $append_count . "][hotel_inv_id][]' id='hotel_inv_id" . $append_count . "'>";
        // $departure_time=2023-06-04 00:00:00;
        return response()->json([
            'hotel_id' => $get_hotel_inv->hotel_id,
            'room_type' => $room_type_options,
            'check_in' => $from_date,
            'check_out' => $to_date,
            'append_inv_input' => $append_inv_input,
            'append_count' => $append_count,
        ]);
    }

    public function get_hotel_available_rooms($hotel_id, $append_count, $inq_id)
    {
        $get_hotel_details = hotel_details::where('hotel_id', $hotel_id)->first();
        $room_types_id = json_decode($get_hotel_details->room_availablity);
        $uniq_ids = array_unique($room_types_id);
        $all_types_rate = room_type::where('status', 'Active')->whereIn('id_room_types', $uniq_ids)->get();
        //         dd($uniq_ids);
        $room_type_options = "<option value=''>Select</option>";
        foreach ($all_types_rate as $key => $value) {
            $room_type_options .= "<option value='" . $value->id_room_types . "'>" . $value->name . '</option>';
        }

        return response()->json([
            'room_type' => $room_type_options,
            'append_count' => $append_count,
        ]);
    }

    function myFilter($var)
    {
        return ($var !== NULL && $var !== FALSE && $var !== '');
    }

    public function get_addons($array_ids_addon)
    {
        $explode_entries = explode(',', $array_ids_addon);
        $addon_selling_price = 0;
        $addon_cost_price = 0;
        // dd($explode_entries);
        foreach ($explode_entries as $addon) {
            $addon_details = Addon::where('id_addons', $addon)->first();
            $addon_selling_price += $addon_details->addon_selling_price;
            $addon_cost_price += $addon_details->addon_cost_price;
        };

        // dd($addon_selling_price);
        // dd($addon_cost_price);
        return response()->json([
            'selling_price' => $addon_selling_price,
            'cost_price' => $addon_cost_price,
        ]);
    }

    // Land Services Work
    function get_land_service_routes($id)
    {
        // dd($id);
        $get_land_service_route_options = "<option value=''>Select</option>";
        $get_transport_options = "<option value=''>Select</option>";
        $get_land_services = Landservicestypes::where('status', 1)->where('id_land_and_services_types', $id)->first();
        //         dd($get_land_services['total_entries']);
        $decode = json_decode($get_land_services->total_entries);
        foreach ($decode as $dc) {
            // $get_route_name = $dc->from_loc . '-' . $dc->to_loc;
            $get_route = route::where('status', 1)->where('id_route', $dc->route_id)->first();
            $get_land_service_route_options .= "<option value='" . $dc->route_id . "'>" . $get_route->route_location . '</option>';
        }
        foreach ($decode as $dc) {
            // dd($dc);
            // $get_route_name = $dc->from_loc . '-' . $dc->to_loc;
            $get_transport_options .= "<option value='" . $dc->transport . "'>" . $dc->transport . '</option>';
            $get_selling_price = $dc->selling_price;
        }
        return response()->json(
            [
                'land_services' => $get_land_service_route_options,
                'get_transport_options' => $get_transport_options,
                'cost_price' => $get_selling_price,
            ]
        );
    }

    //    function get_land_service_routes($id)
    //    {
    //        // dd($id);
    //        $get_land_service_route_options = "<option value=''>Select</option>";
    //        $get_transport_options = "<option value=''>Select</option>";
    //        $get_land_services = Landservicestypes::where('status', 1)->where('id_land_and_services_types', $id)->first();
    //         dd($get_land_services['total_entries']);
    //        $decode = json_decode($get_land_services->total_entries);
    //
    //        // foreach ($decode as $dc) {
    //            // $get_route_name = $dc->from_loc . '-' . $dc->to_loc;
    //            $get_route = route::where('status', 1)->where('id_route', $decode[0]['route_id'])->first();
    //            $get_land_service_route_options .= "<option value='" . $decode[0]['route_id'] . "'>" . $get_route->route_location . "</option>";
    //            $get_transport_options .= "<option value='" . $decode[0]['Bus'] . "'>" . $decode[0]['transport'] . "</option>";
    //        // }
    //
    //        return response()->json(
    //            [
    //                'land_services' => $get_land_service_route_options,
    //                'get_transport_options' => $get_transport_options,
    //
    //
    //
    //            ]
    //        );
    //    }
    function get_route_details($l_id, $id, $service_type)
    {
        // dd($service_type);
        $get_land_services = Landservicestypes::where('status', 1)->where('id_land_and_services_types', $l_id)->first();
        // dd($get_land_services['total_entries']);
        $decode = json_decode($get_land_services->total_entries);
        foreach ($decode as $dc) {
            if ($dc->key == $id) {
                $get_details = $dc;
            }
        }

        if ($get_details->transport_type == 'no_of_person') {
            $cost_price_service_level = $get_details->adult_cost_price + $get_details->children_cost_price + $get_details->infant_cost_price;
            $selling_price_service_level = $get_details->adult_selling_price + $get_details->children_selling_price + $get_details->infant_selling_price;
            if ($service_type == 'service_level') {
                return response()->json(
                    [
                        'service_type' => 'service_level',
                        'service_cost_price' => $cost_price_service_level,
                        'service_selling_price' => $selling_price_service_level,
                    ]
                );
            } else {
                return response()->json(
                    [
                        'service_type' => 'no_of_person',
                        'adult_cost_price' => $get_details->adult_cost_price,
                        'children_cost_price' => $get_details->children_cost_price,
                        'infant_cost_price' => $get_details->infant_cost_price,
                        'adult_selling_price' => $get_details->adult_selling_price,
                        'children_selling_price' => $get_details->children_selling_price,
                        'infant_selling_price' => $get_details->infant_selling_price,
                    ]
                );
            }
        } else {
            if ($service_type == 'no_of_person') {
                $cost_price_service_level_divide = ($get_details->cost_price) / 3;
                $selling_price_service_level_divide = ($get_details->selling_price) / 3;
                return response()->json(
                    [
                        // 'service_type' => 'service_level',
                        // 'service_cost_price' => $cost_price_service_level_divide,
                        // 'service_selling_price' => $selling_price_service_level_divide,
                        'service_type' => 'no_of_person',
                        'adult_cost_price' => $cost_price_service_level_divide,
                        'children_cost_price' => $cost_price_service_level_divide,
                        'infant_cost_price' => $cost_price_service_level_divide,
                        'adult_selling_price' => $selling_price_service_level_divide,
                        'children_selling_price' => $selling_price_service_level_divide,
                        'infant_selling_price' => $selling_price_service_level_divide,
                    ]
                );
            } else {
                return response()->json(
                    [
                        'service_type' => 'service_level',
                        'service_cost_price' => $get_details->cost_price,
                        'service_selling_price' => $get_details->selling_price,
                    ]
                );
            }
        }
        // dd($get_details);
    }

    // Yousuf Works Start Here

    public function getCitiesData()
    {
        $query = cities::query();
        $query
            ->select('cities.id', 'cities.name', 'cities.state_id', 'cities.state_code', 'cities.country_code', 'countries.country_name')
            ->leftJoin('countries', 'countries.id_countries', '=', 'cities.country_id');

        // Process search query (if provided)
        $searchValue = request('search')['value'];
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q
                    ->where('cities.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('cities.state_code', 'like', '%' . $searchValue . '%')
                    ->orWhere('cities.country_code', 'like', '%' . $searchValue . '%')
                    ->orWhere('countries.country_name', 'like', '%' . $searchValue . '%');
            });
        }

        // Process sorting (if needed)
        // ...

        // Get the total number of records (required for pagination)
        $totalRecords = $query->count();

        // Get the data for the current page
        $data = $query
            ->offset(request('start'))
            ->limit(request('length'))
            ->get();

        return response()->json([
            'draw' => intval(request('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,  // In most cases, recordsFiltered is the same as recordsTotal
            'data' => $data,
        ]);
    }

    function add_more_route_fun($itemCount)
    {
        echo '
        <div class="col-md-3  rmv_route' . $itemCount . '">
        <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Location</label>
                <input type="text" name="locations[]" class="form-control" required />
            </div>
            <button style="float: right;" type="button" onclick="remove_route(' . $itemCount . ')"
            class="btn btn-danger mt-4 ms-2">Remove</button> <hr>
            </div>

        ';
    }

    //     function add_land_services_routes_legs($route, $transport, $land_service, $append_count, $land_sl_count)
    //     {
    //         // dd(request()->service_type_id);
    //         $options = ['Bus', 'GMC', 'Railway', 'Car-Sedan', 'Car-SUV'];
    //         $transport_options = '';
    //         $get_legs = "";
    //         foreach ($options as $optionValue) {

    //             $transport_options .= '<option value="' . $optionValue . '">' . $optionValue . '</option>';
    //         };
    //         $get_no_of_persons = inquiry::where('id_inquiry', request()->inq_id)->select('no_of_adults', 'no_of_children', 'no_of_infants')->first();
    //         // dd($get_no_of_persons);
    //         // dd(request()->inq_id);
    //         // dd($transport);

    //         $service_type_no = 0;
    //         // dd(request()->service_type_id);
    //         if (request()->addmore == 0 && request()->service_type_id == 'no_of_person') {
    //             // dd($land_sl_count);
    //             $land_sl_count = $land_sl_count + 1;
    //             $append_manual_land_legs = '<div style="border-left:2px solid grey;padding:20px;border-right:2px solid grey;" class="rmv_land' . $land_sl_count . '" ><table class="table table-striped table-inverse table-responsive mt-2">
    //                 <thead class="thead-inverse">
    //                 <tr>
    //                 <th>Transport</th>
    //                 <th>Route</th>
    //                 <th>No Of Adults</th>
    //                 <th>No Of Children</th>
    //                 <th>No Of Infants</th>
    //                 <th>Remove</th>
    //                 </tr>
    //                 </thead>
    //                 <tbody>
    //                 <div id="append_hotel">
    //                 <tr>
    //                 <input type="hidden" name="service_type_id" id="service_type_id' . $land_sl_count . '"/>
    //                 <input type="hidden" name="land_services[' . $append_count . '][legs_count][land_services_adult_total][]" class="get_land_services_total' . $append_count . '" id="land_services_adult_total' . $land_sl_count . '"/>
    //                 <input type="hidden" name="land_services[' . $append_count . '][legs_count][land_services_children_total][]" class="get_land_services_total' . $append_count . '" id="land_services_children_total' . $land_sl_count . '"/>
    //                 <input type="hidden" name="land_services[' . $append_count . '][legs_count][land_services_infant_total][]" class="get_land_services_total' . $append_count . '" id="land_services_infant_total' . $land_sl_count . '"/>
    //                         <td> <select style="width: 100%" id="land_services_transport' . $land_sl_count . '" class="select_land_services_sl"
    //                         name="land_services[' . $append_count . '][legs_count][transport][]">
    //                 ' . $transport_options . '
    //                     </select></td>
    //                     <td><input type="text"   class="form-control" name="land_services[' . $append_count . '][legs_count][land_services_route][]" id="loc' . $land_sl_count . '"/></td>
    //                     <td><input  type="number" value="' . $get_no_of_persons->no_of_adults . '" id="land_services_no_of_adult' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][no_of_adult][]" class="form-control"></td>
    //                     <td><input  type="number" value="' . $get_no_of_persons->no_of_children . '" id="land_services_no_of_children' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][no_of_children][]" class="form-control"></td>
    //                     <td><input  type="number" value="' . $get_no_of_persons->no_of_infants . '" id="land_services_no_of_infant' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][no_of_infant][]" class="form-control"></td>
    //                     <td><button type="button" class="btn btn-danger" onclick="remove_land_services_legs(' . $land_sl_count . ')"><i class="fa fa-trash"></i></button></td>
    //                     </tr>
    //                 </div>
    //                 </tbody>
    //                 </table>
    //                 <table class="table table-striped table-inverse table-responsive mt-2">
    //                 <thead class="thead-inverse">
    //                 <tr>
    //                 <th>Adult Cost Price</th>
    //                 <th>Children Cost Price</th>
    //                 <th>Infant Cost Price</th>
    //                 </tr>
    //                 </thead>
    //                 <tbody>
    //                 <div id="append_hotel">
    //                 <tr>

    //                 <td><input  type="number" id="land_services_no_of_adult' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][adult_cost_price][]" class="form-control"></td>
    //                 <td><input  type="number" id="land_services_no_of_children' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][children_cost_price][]" class="form-control"></td>
    //                 <td><input  type="number" id="land_services_no_of_infant' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][infant_cost_price][]" class="form-control"></td>
    //                 </tr>
    //                 </div>
    //                 </tbody>
    //                 </table>
    //                 <table class="table table-striped table-inverse table-responsive mt-2">
    //                 <thead class="thead-inverse">
    //                 <tr>
    //                 <th>Adult Selling Price</th>
    //                 <th>Children Selling Price</th>
    //                 <th>Infant Selling Price</th>
    //                 </tr>
    //                 </thead>
    //                 <tbody>
    //                 <div id="append_hotel">
    //                 <tr>

    //                 <td><input  type="number" id="land_services_adult_selling_price' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][adult_selling_price][]" class="form-control land_services_adult_selling_price' . $append_count . '"></td>
    //                 <td><input  type="number" id="land_services_children_selling_price' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][children_selling_price][]" class="form-control land_services_children_selling_price' . $append_count . '"></td>
    //                 <td><input  type="number" id="land_services_infant_selling_price' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][infant_selling_price][]" class="form-control land_services_infant_selling_price' . $append_count . '"></td>
    //                 </tr>
    //                 </div>
    //                 </tbody>
    //                 </table></div>';
    //             return response()->json([
    //                 'data' => $get_legs,
    //                 'land_sl_count' => $land_sl_count,
    //                 'append_manual_land_legs' => $append_manual_land_legs,
    //             ]);
    //         } elseif (request()->addmore == 0 && request()->service_type_id == 'service_level') {
    //             $sub_name = "Land Services";
    //             $currency_rates = currency_exchange_rate::all();
    //             $land_services = Landservicestypes::where('status', 1)->get();
    //             // dd($addon);
    //             $land_services_options = "<option value=''>Select</option>";
    //             $currency_rate_options = "";
    //             $add_more_legs = 0;
    //             $addon_options = "<option value=''>Select</option>";
    //             foreach ($land_services as $key => $value) {
    //                 // dd($value->name);
    //                 $land_services_types = land_services_type::where('id_land_services_types', $value->name)->first();
    //                 $land_services_options .= "<option value='" . $value->id_land_and_services_types . "'>" . $land_services_types->service_name . "</option>";
    //             }
    //             foreach ($currency_rates as $key => $value) {
    //                 $currency_rate_options .= "<option data='" . $value->currency_name . "' value='" . $value->currency_rate . "'>" . $value->currency_name . "</option>";
    //             }
    //             $legs_count = 0;
    //             // means Service level (1)
    //             $service_type_no = 1;
    //             // dd($addon_options);
    //             $get_legs = '<div id="land_services_table' . $append_count . '" class="row"><h4 class="mt-2">Add Land Services Details</h4><div class="col-md-12" style="border:2px solid lightgrey;" >
    //                     <table class="table table-striped table-inverse table-responsive mt-2">
    //                     <thead class="thead-inverse">
    //                         <tr>
    //                         <th>Land Service</th>
    //                         <th>Transport</th>
    //                         <th>Route</th>
    //                         <th>Date</th>
    //                         </tr>
    //                     </thead>
    //                     <tbody>
    //                         <div id="append_hotel">
    //                         <tr>
    //                         <td>
    //                         <input type="hidden" name="service_type_id" id="service_type_id' . $append_count . '"/>

    //                         <select style="width:100%;" id="land_service' . $append_count . '" name="land_services[' . $append_count . '][legs_count][land_service][]" onchange="get_land_services_route(' . $append_count . ')" class="form-control select2' . $append_count . '" >
    //                         ' . $land_services_options . '
    //                                                 </select></td>
    //                                                 <td><select style="width:100%;" class="form-control select2' . $append_count . '" onchange="get_route_details(' . $legs_count . ',' . $append_count . ')" name="land_services[' . $append_count . '][transport][]" id="transport' . $append_count . '" >
    //                                                 </select></td>
    //                                                 <td><select style="width:100%;" class="form-control select2' . $append_count . '" onchange="get_route_details(' . $legs_count . ',' . $append_count . ')" name="land_services[' . $append_count . '][land_services_route][]" id="land_services_route' . $append_count . '" >
    //                                                 </select></td>
    //                                                 <td><input style="width:100%;" type="text" placeholder="mm/dd/yyyy" readonly id="land_services_date' . $append_count . '" name="land_services[' . $append_count . '][legs_count][date][]" class="form-control fc-datepicker' . $append_count . '"></td>
    //                         </tr>
    //                         </div>
    //                     </tbody>
    //                 </table>
    //                 <div id="append_land_services_legs' . $append_count . '"></div>
    //                 </div></div>
    //             ';
    //             return response()->json([
    //                 // 'data' => $get_legs,
    //                 'land_sl_count' => $land_sl_count,
    //                 'append_manual_land_legs' => $get_legs,
    //             ]);
    //         }
    //         $land_services = Landservicestypes::where('id_land_and_services_types', $land_service)->first();

    //         if ($land_services) {
    //             $decode = json_decode($land_services->total_entries);
    //             foreach ($decode as $key => $value) {
    //                 if ($value->key == $route) {
    //                     $get_route[] = $value;
    //                 }
    //                 if ($value->key == $transport) {
    //                     $get_transport[] = $value;
    //                 }
    //             }
    //             foreach ($options as $optionValue) {
    //                 if ($get_transport[0]->transport == $optionValue) {

    //                     $transport_options .= '<option selected value="' . $optionValue . '">' . $optionValue . '</option>';
    //                 } else {
    //                     $transport_options .= '<option value="' . $optionValue . '">' . $optionValue . '</option>';
    //                 }
    //             };

    //             $service_type_no = 0;
    //             $get_route_id = $get_route[0]->route_id;
    //             $get_route_details = route::where('id_route', $get_route_id)->first();
    //             $get_route_details_decode = json_decode($get_route_details->locations);
    //             // dd($get_route_details_decode);
    //             $get_legs = "";
    //             $append_manual_land_legs = "";
    //             $get_size_of_route = sizeof($get_route_details_decode);
    //         }

    //         // dd($get_transport);

    //         // dd(request()->addmore == 0);

    //         if (request()->service_type_id == 'no_of_person') {

    //             $append_manual_land_legs = null;
    //             for ($i = 0; $i < $get_size_of_route; $i++) {
    //                 if (array_key_last($get_route_details_decode) > $i) {
    //                     $to_loc_route = $get_route_details_decode[$i + 1];
    //                 } else {
    //                     $to_loc_route = $get_route_details_decode[$i];
    //                 }
    //                 // dd($loc);
    //                 $land_sl_count = $land_sl_count + 3;
    //                 $get_legs .= '<table class="table table-striped table-inverse table-responsive mt-2 rmv_land' . $land_sl_count . '"  >
    //         <thead class="thead-inverse">
    //             <tr>
    //             <th>Transport</th>
    //             <th>Route</th>
    //             <th>No Of Adults</th>
    //             <th>No Of Children</th>
    //             <th>No Of Infants</th>
    //             <th>Remove</th>
    //             </tr>
    //         </thead>
    //         <tbody>
    //             <div id="append_hotel">
    //             <tr>
    //             <input type="hidden" name="land_services[' . $append_count . '][legs_count][land_services_adult_total][]" class="get_land_services_total' . $append_count . '" id="land_services_adult_total' . $land_sl_count . '"/>
    //             <input type="hidden" name="land_services[' . $append_count . '][legs_count][land_services_children_total][]" class="get_land_services_total' . $append_count . '" id="land_services_children_total' . $land_sl_count . '"/>
    //             <input type="hidden" name="land_services[' . $append_count . '][legs_count][land_services_infant_total][]" class="get_land_services_total' . $append_count . '" id="land_services_infant_total' . $land_sl_count . '"/>
    //             <input type="hidden" name="service_type_id" id="service_type_id' . $land_sl_count . '"/>
    //                        <td> <select style="width: 100%" id="land_services_transport' . $land_sl_count . '" class="select_land_services_sl"
    //                        name="land_services[' . $append_count . '][legs_count][transport][]">
    // ' . $transport_options . '
    //                    </select></td>
    //                    <td><input type="text"  value="' . $get_route_details_decode[$i]->locations . '-' . $to_loc_route->locations . '" class="form-control" name="land_services[' . $append_count . '][legs_count][land_services_route][]" id="loc' . $land_sl_count . '"/></td>
    //                  <td><input  type="number" value="' . $get_no_of_persons->no_of_adults . '"  id="land_services_no_of_adult' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][no_of_adult][]" class="form-control"></td>
    //                  <td><input  type="number"  value="' . $get_no_of_persons->no_of_children . '" id="land_services_no_of_children' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][no_of_children][]" class="form-control"></td>
    //                  <td><input  type="number"   value="' . $get_no_of_persons->no_of_infants . '" id="land_services_no_of_infant' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][no_of_infant][]" class="form-control"></td>
    //                  <td><button type="button" class="btn btn-danger" onclick="remove_land_services_legs(' . $land_sl_count . ')"><i class="fa fa-trash"></i></button></td>
    //             </tr>
    //             </div>
    //         </tbody>
    //     </table>
    //     <table class="table table-striped table-inverse table-responsive mt-2 rmv_land' . $land_sl_count . '">
    //         <thead class="thead-inverse">
    //             <tr>
    //             <th>Adult Cost Price</th>
    //             <th>Children Cost Price</th>
    //             <th>Infant Cost Price</th>
    //             </tr>
    //         </thead>
    //         <tbody>
    //             <div id="append_hotel">
    //             <tr>
    //                  <td><input  type="number" id="land_services_no_of_adult' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][adult_cost_price][]" class="form-control"></td>
    //                  <td><input  type="number" id="land_services_no_of_children' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][children_cost_price][]" class="form-control"></td>
    //                  <td><input  type="number" id="land_services_no_of_infant' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][infant_cost_price][]" class="form-control"></td>

    //             </tr>
    //             </div>
    //         </tbody>
    //     </table>
    //     <table class="table table-striped table-inverse table-responsive mt-2 rmv_land' . $land_sl_count . '">
    //         <thead class="thead-inverse">
    //             <tr>
    //             <th>Adult Selling Price</th>
    //             <th>Children Selling Price</th>
    //             <th>Infant Selling Price</th>
    //             </tr>
    //         </thead>
    //         <tbody>
    //             <div id="append_hotel">
    //             <tr>

    //                  <td><input  type="number" id="land_services_adult_selling_price' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][adult_selling_price][]" class="form-control land_services_adult_selling_price' . $append_count . '"></td>
    //                  <td><input  type="number" id="land_services_children_selling_price' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][children_selling_price][]" class="form-control land_services_children_selling_price' . $append_count . '"></td>
    //                  <td><input  type="number" id="land_services_infant_selling_price' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][infant_selling_price][]" class="form-control land_services_infant_selling_price' . $append_count . '"></td>
    //             </tr>
    //             </div>
    //         </tbody>
    //     </table>';
    //             }
    //         } else {

    //             $sub_name = "Land Services";
    //             $currency_rates = currency_exchange_rate::all();
    //             $land_services = Landservicestypes::where('status', 1)->get();
    //             // dd($addon);
    //             $land_services_options = "<option value=''>Select</option>";
    //             $currency_rate_options = "";
    //             $add_more_legs = 0;
    //             $addon_options = "<option value=''>Select</option>";
    //             foreach ($land_services as $key => $value) {
    //                 // dd($value->name);
    //                 $land_services_types = land_services_type::where('id_land_services_types', $value->name)->first();
    //                 $land_services_options .= "<option value='" . $value->id_land_and_services_types . "'>" . $land_services_types->service_name . "</option>";
    //             }
    //             foreach ($currency_rates as $key => $value) {
    //                 $currency_rate_options .= "<option data='" . $value->currency_name . "' value='" . $value->currency_rate . "'>" . $value->currency_name . "</option>";
    //             }
    //             $legs_count = 0;
    //             // means Service level (1)
    //             $service_type_no = 1;
    //             // dd($addon_options);
    //             $get_legs = '<div id="land_services_table' . $append_count . '" class="row"><h4 class="mt-2">Add Land Services Details</h4><div class="col-md-12" style="border:2px solid lightgrey;" >
    //                     <table class="table table-striped table-inverse table-responsive mt-2">
    //                     <thead class="thead-inverse">
    //                         <tr>
    //                         <th>Land Service</th>
    //                         <th>Transport</th>
    //                         <th>Route</th>
    //                         <th>Date</th>
    //                         </tr>
    //                     </thead>
    //                     <tbody>
    //                         <div id="append_hotel">
    //                         <tr>
    //                         <td>
    //                         <input type="hidden" name="service_type_id" id="service_type_id' . $append_count . '"/>

    //                         <select style="width:100%;" id="land_service' . $append_count . '" name="land_services[' . $append_count . '][legs_count][land_service][]" onchange="get_land_services_route(' . $append_count . ')" class="form-control select2' . $append_count . '" >
    //                         ' . $land_services_options . '
    //                                                 </select></td>
    //                                                 <td><select style="width:100%;" class="form-control select2' . $append_count . '" onchange="get_route_details(' . $legs_count . ',' . $append_count . ')" name="land_services[' . $append_count . '][transport][]" id="transport' . $append_count . '" >
    //                                                 </select></td>
    //                                                 <td><select style="width:100%;" class="form-control select2' . $append_count . '" onchange="get_route_details(' . $legs_count . ',' . $append_count . ')" name="land_services[' . $append_count . '][land_services_route][]" id="land_services_route' . $append_count . '" >
    //                                                 </select></td>
    //                                                 <td><input style="width:100%;" type="text" placeholder="mm/dd/yyyy" readonly id="land_services_date' . $append_count . '" name="land_services[' . $append_count . '][legs_count][date][]" class="form-control fc-datepicker' . $append_count . '"></td>
    //                         </tr>
    //                         </div>
    //                     </tbody>
    //                 </table>
    //                 <div id="append_land_services_legs' . $append_count . '"></div>
    //                     <table class="table table-striped table-inverse table-responsive">
    //                     <thead class="thead-inverse">

    //                         <tr>
    //                         <th>Sub Total</th>
    //                         <th>Discount</th>
    //                         <th>Total</th>
    //                         <th id="land_services_exchange_head' . $append_count . '">Exchange</th>
    //                         <th>Add</th>
    //                         <th>Remove</th>
    //                         </tr>
    //                     </thead>
    //                     <tbody>
    //                         <div>
    //                             <tr>
    //                                 <input type="hidden" id="land_services_service_id' . $append_count . '" name="land_services[' . $append_count . '][land_services_service_id][]">
    //                                 <input type="hidden" id="land_services_sub_service_id' . $append_count . '" name="land_services[' . $append_count . '][land_services_sub_service_id][]">
    //                                 <input type="hidden" id="land_services_currency_name' . $append_count . '" name="land_services[' . $append_count . '][land_services_currency_name][]">
    //                                 <td><input  type="number" id="land_services_sub_total' . $append_count . '" onchange="land_services_calculate(' . $legs_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][land_services_sub_total][]" class="form-control"></td>
    //                                 <td><input  type="number" id="land_services_discount' . $append_count . '" onchange="land_services_calculate(' . $legs_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][land_services_discount][]" class="form-control"></td>
    //                                 <td><input  type="number" id="land_services_total' . $append_count . '" onchange="land_services_calculate(' . $legs_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][land_services_total][]" class="form-control"></td>
    //                                 <td><select name="land_services[' . $append_count . '][land_services_currency][]"   onchange="onchange_get_curr_data_land_services(' . $append_count . ')" id="land_services_currency' . $append_count . '" class="form-control js-example-basic-single" style="width: 100%"> <option value="">Select</option> ' . $currency_rate_options . ' </select></td>
    //                                 <td><button class="btn btn-success text-white" type="button" style="margin:0;" onclick="get_route_details(' . $legs_count . ',' . $append_count . ',' . $add_more_legs . ')"><i class="fa fa-plus"></i></button></td>
    //                                 <td><button class="btn btn-danger" type="button" style="margin:0;"  onClick="remove_land_services(' . $append_count . ')"><i class="fa fa-trash"></i></button></td>
    //                             </tr>
    //                         </div>
    //                     </tbody>
    //                     </table>
    //                 </div></div>
    //             ';
    //         }

    //         return response()->json([
    //             'data' => $get_legs,
    //             'append_manual_land_legs' => $append_manual_land_legs,
    //             'land_sl_count' => $land_sl_count,
    //         ]);
    //         // dd();
    //     }

    function add_land_services($itemCount)
    {
        $get_routes = Route::where('status', 1)->get();
        $routes_options = '';
        $get_vendors = service_vendor::where('vendor_status', 1)->get();
        $vendor_options = '';
        foreach ($get_routes as $route) {
            $routes_options .= "<option value='" . $route->id_route . "'>" . $route->route_location . '</option>';
        }
        foreach ($get_vendors as $vendor) {
            $vendor_options .= "<option value='" . $vendor->id_service_vendors . "'>" . $vendor->vendor_name . '</option>';
        }

        echo '<hr class=" rmv_land_services' . $itemCount . '"><div class="row row-sm mg-b-20 rmv_land_services' . $itemCount . ' "><div class="col-md-6 mt-2  rmv_land_services' . $itemCount . ' ">
        <div class="form-group">
            <label class="az-content-label tx-11 tx-medium tx-gray-600">Select Transport</label>
            <select style="width: 100%" id="select_transport' . $itemCount . '" class="js-example-basic-single"
                name="transport[]">
                <option value="">Select</option>
                <option value="Bus">Bus</option>
                <option value="GMC">GMC</option>
                <option value="Railway">Railway</option>
                <option value="Car-Sedan">Car-Sedan</option>
                <option value="Car-SUV">Car-SUV</option>

            </select>
        </div>
    </div>

    <div class="col-md-6 mt-2  rmv_land_services' . $itemCount . '">
        <div class="form-group">
            <label class="az-content-label tx-11 tx-medium tx-gray-600">Select Route</label>
            <select style="width: 100%" id="select_route' . $itemCount . '" class="js-example-basic-single"
                name="route[]">
                <option value="">Select</option>
                ' . $routes_options . '
            </select>
        </div>
    </div>
    <div class="col-md-6 mt-2  rmv_land_services' . $itemCount . '">
        <div class="form-group">
            <label class="az-content-label tx-11 tx-medium tx-gray-600">Select Vendor</label>

            <select style="width: 100%" id="select_vendor' . $itemCount . '" class="js-example-basic-single"
                name="vendor[]">
                <option value="">Select</option>
               ' . $vendor_options . '

            </select>
        </div>
    </div>
    <div class="col-md-6 mt-2  rmv_land_services' . $itemCount . '">
                            <div class="form-group">
                                <label class="az-content-label tx-11 tx-medium tx-gray-600">Select Transport Type</label>
                                <select style="width: 100%" onchange="change_transport_type(' . $itemCount . ',this)" id="transport_type' . $itemCount . '"
                                    class="js-example-basic-single" name="transport_type[]">
                                    <option value="">Select</option>
                                    <option selected value="no_of_person">No Of Person</option>
                                    <option value="service_level">Service Level</option>

                                </select>

                            </div>
                        </div>
    </div>
    <div id="no_of_person' . $itemCount . '" class=" rmv_land_services' . $itemCount . '">
    <div class="row row-sm mg-b-20  rmv_land_services' . $itemCount . ' ">
        <div class="col-md-6 ">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Adults Cost Price</label>
                <input type="number" name="adult_cost_price[]" class="form-control"  />
            </div>
        </div>
        <div class="col-md-6  rmv_land_services' . $itemCount . '">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Adults Selling Price</label>
                <input type="number" name="adult_selling_price[]" class="form-control"  />
            </div>
        </div>
    </div>
    <div class="row row-sm mg-b-20  rmv_land_services' . $itemCount . ' ">
        <div class="col-md-6 ">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Children Cost Price</label>
                <input type="number" name="children_cost_price[]" class="form-control"  />
            </div>
        </div>
        <div class="col-md-6  rmv_land_services' . $itemCount . ' ">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Children Selling
                    Price</label>
                <input type="number" name="children_selling_price[]" class="form-control"
                     />
            </div>
        </div>
    </div>
    <div class="row row-sm mg-b-20  rmv_land_services' . $itemCount . '">

        <div class="col-md-6">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Infant Cost Price</label>
                <input type="number" name="infant_selling_price[]" class="form-control"  />
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Infant Selling
                    Price</label>
                <input type="number" name="infant_cost_price[]" class="form-control"  />
            </div>
        </div>
        <div class="col-md-2 mt-4 ">
            <div class="form-group">
            <button type="button" onclick="remove_land_services(' . $itemCount . ')" class="btn btn-danger btn-block text-white">Remove
                        </button>
            </div>
        </div>
    </div>
</div>
<div style="display:none;" id="service_level' . $itemCount . '">
    <div class="row row-sm mg-b-20 ">
        <div class="col-md-6 ">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Cost Price</label>
                <input type="number" name="cost_price[]" class="form-control"  />
            </div>
        </div>

        <div class="col-md-6 ">
            <div class="form-group">
                <label class="az-content-label tx-11 tx-medium tx-gray-600">Selling
                    Price</label>
                <input type="number" name="selling_price[]" class="form-control"  />
            </div>
        </div>
    </div>
</div>';
    }

    // Yousuf Works End Here
    // inquiry Create Services On Selection Of Campaign

    public function append_hotel_beds($hotel_room_type, $append_count)
    {
        $get_details = room_type::where('id_room_types', $hotel_room_type)->first();
        return response()->json([
            'beds' => $get_details->no_of_beds
        ]);
    }

    public function get_visa_rates($visa_rate_id)
    {
        $get_details = Visa_rates::where('id_visa_rates', $visa_rate_id)->first();
        return response()->json([
            // "child_c" => $get_details->child_cost_price,
            'child_s' => $get_details->child_selling_price,
            // "adult_c" => $get_details->adult_cost_price,
            'adult_s' => $get_details->adult_selling_price,
            // "infant_c" => $get_details->infant_cost_price,
            'infant_s' => $get_details->infant_selling_price,
        ]);
    }

    public function add_land_services_routes_legs($route, $transport, $land_service, $append_count, $land_sl_count)
    {
        $sub_name = 'Land Services';
        $currency_rates = currency_exchange_rate::all();
        $land_services = Landservicestypes::where('status', 1)->get();
        // dd($addon);
        $land_services_options = "<option value=''>Select</option>";
        $currency_rate_options = '';
        $add_more_legs = 0;
        $addon_options = '';
        foreach ($land_services as $key => $value) {
            // dd($value->name);
            $land_services_types = land_services_type::where('id_land_services_types', $value->name)->first();
            $land_services_options .= "<option value='" . $value->id_land_and_services_types . "'>" . $land_services_types->service_name . '-' . $value->service_type . '</option>';
        }
        foreach ($currency_rates as $key => $value) {
            $currency_rate_options .= "<option data='" . $value->currency_name . "' value='" . $value->currency_rate . "'>" . $value->currency_name . '</option>';
        }
        $legs_count = 0;
        // means Service level (1)
        $service_type_no = 1;
        // dd($land_sl_count);

        if (request()->service_type_id == 'service_level') {
            $get_legs = '<div id="land_services_table' . $land_sl_count . '" class="row rmv_land' . $land_sl_count . '"><div class="col-md-12" >
            <table class="table table-striped table-inverse table-responsive mt-2">
            <thead class="thead-inverse">
                <tr>
                <th>Land Service</th>
                <th>Transport</th>
                <th>Route</th>
                <th class="float-end">Remove</th>
                </tr>
            </thead>
            <tbody>
                <div id="append_hotel">
                <tr>
                <td>
                <input type="hidden" name="service_type_id" id="service_type_id' . $land_sl_count . '"/>

                <select style="width:100%;" id="land_service' . $land_sl_count . '" name="land_services[' . $append_count . '][legs_count][land_service][]" onchange="get_land_services_route(' . $land_sl_count . ')" class="form-control select2' . $land_sl_count . '" >
                ' . $land_services_options . '
                                        </select></td>
                                        <td><select style="width:100%;" class="form-control select2' . $land_sl_count . '"  name="land_services[' . $append_count . '][legs_count][transport][]" id="transport' . $land_sl_count . '" >
                                        </select></td>
                                        <td ><select style="width:100%;" class="form-control select2' . $land_sl_count . '"  name="land_services[' . $append_count . '][legs_count][land_services_route][]" id="land_services_route' . $land_sl_count . '" >
                                        </select></td>
                                        <td style="width:75px;position:relative;" ><button style="position:absolute;top:-11px"  class="btn btn-danger float-end" type="button" onclick="remove_land_services_legs(' . $land_sl_count . ')" ><i class="fa fa-trash"><i></button></td>
                                        </tr>
            </div>
            </tbody>
        </table>
        <table class="table table-striped table-inverse table-responsive mt-2">
        <thead class="thead-inverse">
            <tr>
            <th>Cost Price</th>
            <th>Selling Price</th>
            </tr>
        </thead>
        <tbody>
            <div id="append_hotel">
            <tr>
                                    <td><input style="width:100%;" type="text" placeholder="Cost Price"  onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" id="land_services_cost_price' . $land_sl_count . '" name="land_services[' . $append_count . '][legs_count][land_services_cost_price][]" class="form-control land_services_cost_price' . $append_count . '"></td>
                                    <td><input style="width:100%;" type="text" placeholder="Selling Price" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')"  id="land_services_selling_price' . $land_sl_count . '" name="land_services[' . $append_count . '][legs_count][land_services_selling_price][]" class="form-control land_services_selling_price' . $append_count . ' "></td>
                                    </tr>
            </div>
        </tbody>
    </table>

        </div></div>
    ';
        } else {
            $get_legs = '<div id="land_services_table' . $land_sl_count . '" class="row rmv_land' . $land_sl_count . '"><div class="col-md-12" >
            <table class="table table-striped table-inverse table-responsive mt-2">
            <thead class="thead-inverse">
                <tr>
                <th>Land Service</th>
                <th>Transport</th>
                <th>Route</th>
                <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <div id="append_hotel">
                <tr>
                <td>
                <input type="hidden" name="service_type_id" id="service_type_id' . $land_sl_count . '"/>

                <select style="width:100%;" id="land_service' . $land_sl_count . '" name="land_services[' . $append_count . '][legs_count][land_service][]" onchange="get_land_services_route(' . $land_sl_count . ')" class="form-control select2' . $land_sl_count . '" >
                ' . $land_services_options . '
                                        </select></td>
                                        <td><select style="width:100%;" class="form-control select2' . $land_sl_count . '"  name="land_services[' . $append_count . '][legs_count][transport][]" id="transport' . $land_sl_count . '" >
                                        </select></td>
                                        <td><select style="width:100%;" class="form-control select2' . $land_sl_count . '"  name="land_services[' . $append_count . '][legs_count][land_services_route][]" id="land_services_route' . $land_sl_count . '" >
                                        </select></td>
                                        <td><button class="btn btn-danger" type="button" onclick="remove_land_services_legs(' . $land_sl_count . ')" ><i class="fa fa-trash"><i></button></td>
                                        </tr>
                </div>
            </tbody>
        </table>
        <table class="table table-striped mt-2 table-inverse table-responsive" >
        <thead class="thead-inverse mt-2">
            <tr>
                <th>Cost Price</th>
            </tr>
        </thead>
        <tbody>
            <div>
                <tr>
                <input type="hidden" id="land_services_adult_total_cost_price' . $land_sl_count . '"  class="adult_cost_price_sum">
                <td><input type="number" id="adult_land_cost_price' . $land_sl_count . '" onchange="land_services_calculate(' . $land_sl_count . ',' . $append_count . ',' . $service_type_no . ')" name="land_services[' . $append_count . '][legs_count][land_services_adult_cost_price][]" class="form-control  adult_land_services_sum_cost_price' . $append_count . '"></td>
                </tr>
            </div>
        </tbody>
    </table>

        </div></div>
    ';
        }

        return response()->json([
            'get_legs' => $get_legs,
        ]);
    }

    function send_quotation_to_approval($q_id, $inq_id)
    {
        $get_approval_group_id = approval_group::select('user_id')->get()->toArray();
        $get_approval_ids = [];
        foreach ($get_approval_group_id as $app_user_id) {
            $get_approval_ids[] = $app_user_id['user_id'];
        }
        $get_decode_ids = json_encode($get_approval_ids);
        if (count($get_approval_ids) > 0) {
            $get_quotation = quotation::where('id_quotations', $q_id)->first();
            $get_quotation->status = 1;  // send to appproval
            $get_quotation->save();

            $store = new quotation_approval();
            $store->quotation_id = $q_id;
            $store->inquiry_id = $inq_id;
            $store->user_id = $get_decode_ids;
            $store->status = 'Open';
            $store->created_by = auth()->user()->id;
            $store->save();
            if ($store) {
                $store = new remarks();
                $store->inquiry_id = $inq_id;
                $store->remarks = 'Quotation Send For Approval - ' . $get_quotation->quotation_no;
                $store->remarks_status = 'Quotation Send For Approval';
                $store->cancel_reason = '';
                $store->type = 'quotation';
                $store->quotation_id = $q_id;
                $store->followup_date = '';
                $store->created_by = auth()->user()->id;
                $store->save();
                foreach ($get_approval_group_id as $app_user_id) {
                    sendNoti('New Approval Recived Against-' . $get_quotation->quotation_no, null, 'quotation_approval', $app_user_id['user_id']);
                }
            }

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    function send_quotation_to_issuance($q_id, $inq_id, $services_type)
    {
        $get_quotation = quotation::where('id_quotations', $q_id)->select('quotation_no')->first();

        $service_type = explode(',', $services_type);
        // dd($service_type);
        // $store='';
        foreach ($service_type as $type) {
            if ($type != null) {
                $get_q_d = quotations_detail::where('quotation_id', $q_id)->where('services_type', $type)->first();
                // dd($type);
                $store = new quotation_issuance();
                $store->quotation_id = $q_id;
                $store->inquiry_id = $inq_id;
                $store->services_type = $get_q_d->services_type;
                $store->created_by = auth()->user()->id;
                $store->status = 'Un-Assign';
                $store->save();

                if ($store) {
                    $store = new remarks();
                    $store->inquiry_id = $inq_id;
                    $store->remarks = 'Quotation(' . $get_q_d->services_type . ')Send For Issuance - ' . $get_quotation->quotation_no;
                    $store->remarks_status = 'Quotation Send For Issuance';
                    $store->cancel_reason = '';
                    $store->type = 'quotation';
                    $store->quotation_id = $q_id;
                    $store->followup_date = '';
                    $store->created_by = auth()->user()->id;
                    $store->save();
                    // dd($type);
                    $get_main_menu = DB::table('main_menu')->whereIn('id_main_menu', [109, 110, 111, 112])->get();
                    //                     dd($get_main_menu);
                    foreach ($get_main_menu as $m_key => $menu) {
                        $get_service_type = preg_replace('/Issuance /', '', $menu->title);
                        // dd($get_service_type);
                        if ($get_service_type == $type) {
                            $role_permission = role_permission::where('menu_id', $menu->id_main_menu)->pluck('role_id');
                            $users = User::whereIn('role_id', $role_permission)->get();
                            foreach ($users as $use) {
                                sendNoti('Issuance(' . $get_q_d->services_type . ') Send For Verification ', $use->name, 'quotation_issuance', $use->id, null);
                            }
                        }
                    }
                } else {
                }
            }
        }

        if ($store) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    function get_hotel_city_category($city, $cat)
    {
        $trim_city = trim($city);
        // dd($trim_city);
        $get_hotels = hotels::where('hotel_category', $cat)->whereHas('get_hotel_details', function ($query) use ($trim_city) {
            return $query->where('city', '=', $trim_city);
        })->get();

        // dd($get_hotels);

        $hotel_options = '';
        foreach ($get_hotels as $key => $value) {
            $hotel_options .= "<option value='" . $value->id_hotels . "'>" . $value->hotel_name . '</option>';
        }

        // dd($hotel_options);

        return response()->json([
            'hotel_options' => $hotel_options
        ]);
        // $store='';
    }

    function get_quotation_approvals_data()
    {
        if (request()->ajax()) {
            $quotation_approval = quotation_approval::join('quotations', 'quotations.id_quotations', '=', 'quotation_approvals.quotation_id')->select('*', 'quotation_approvals.status as q_status')->where('quotation_approvals.status', 'Open')->get();

            $get_quotations_approval_data = [];
            foreach ($quotation_approval as $key => $value) {
                // dd($value->q_status);
                $get_id_array = json_decode($value->user_id);
                // dd($get_id_array);
                $get_count = count($get_id_array);
                for ($i = 0; $i < $get_count; $i++) {
                    // dd($value->q_status);
                    if ($get_id_array[$i] == auth()->user()->id) {
                        $get_user_name = auth()->user()->name;
                        $get_quotations_approval_data[] = [
                            'id_quotation_approvals' => $value->id_quotation_approvals,
                            'quotation_no' => $value->quotation_no,
                            'inquiry_id' => $value->inquiry_id,
                            'status' => $value->q_status,
                            'quotation_id' => $value->quotation_id,
                            'user_name' => $get_user_name,
                            'created_at' => $value->created_at,
                        ];
                    }
                }
            }
            // $object = object;
            // foreach ($get_quotations_approval_data as $key => $value) {
            //     $object->{$key} = $value['quotation_no'];
            // };

            $query = $get_quotations_approval_data;
            // dd($query);
            // $query->select('cities.id', 'cities.name', 'cities.state_id', 'cities.state_code', 'cities.country_code', 'countries.country_name')
            //     ->leftJoin('countries', 'countries.id_countries', '=', 'cities.country_id');

            // // Process search query (if provided)
            // $searchValue = request('search')['value'];
            // if ($searchValue) {
            //     $query->where(function ($q) use ($searchValue) {
            //         $q->where('cities.name', 'like', '%' . $searchValue . '%')
            //             ->orWhere('cities.state_code', 'like', '%' . $searchValue . '%')
            //             ->orWhere('cities.country_code', 'like', '%' . $searchValue . '%')
            //             ->orWhere('countries.country_name', 'like', '%' . $searchValue . '%');
            //     });
            // }

            // Process sorting (if needed)
            // ...

            // Get the total number of records (required for pagination)
            $totalRecords = count($query);
            // $object = (object) $query;

            // Get the data for the current page

            // Define pagination parameters
            $start = request('start', 0);  // Starting index (default to 0 if not provided)
            $length = request('length', 10);  // Number of records per page (default to 10 if not provided)

            // Use array_slice to get the paginated data
            $data = array_slice($query, $start, $length);

            return response()->json([
                'draw' => intval(request('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,  // In most cases, recordsFiltered is the same as recordsTotal
                'data' => $data,
            ]);
        }
    }

    function view_invoice_payment_details($pay_id)
    {
        if (request()->ajax()) {
            $get_payment_details = payments_account::where('id_account_payments', $pay_id)->first();
            //            dd($get_payment_details);exit;
            return response()->json([
                'payment_number' => $get_payment_details->pay_no,
                'payment_id' => $get_payment_details->id_account_payments,
                'payment_status' => $get_payment_details->status,
                'payment_type' => $get_payment_details->payment_type,
                'payment_remarks' => $get_payment_details->payment_remarks,
                'payment_verification' => $get_payment_details->payment_verification,
                'customer_bank' => $get_payment_details->bank_name,
                'cheque_date' => $get_payment_details->cheque_date,
                'bank_name' => $get_payment_details->bank_name,
                'deposit_date' => $get_payment_details->deposit_date,
                'clearing_date' => $get_payment_details->clearing_date,
                'account_number' => $get_payment_details->account_number,  // In most cases, recordsFiltered is the same as recordsTotal
                'cheque_number' => $get_payment_details->cheque_number,
                'amount' => $get_payment_details->paid_amount,
                'recieving_number' => $get_payment_details->recieving_number,
                'attachment' => $get_payment_details->attachment,
            ]);
        }
    }

    function get_pay_quotation_details($i_id)
    {
        if (request()->ajax()) {
            $get_q = quotation::where('inquiry_id', $i_id)->where('status', '>=', 3)->where('customer_verified', 1)->latest()->first();

            if (!empty($get_q)) {
                $get_q_details = quotations_detail::where('quotation_id', $get_q->id_quotations)->get();
                //             dd($get_q_details);
                $services_option = "<option value=''>Select</option>";
                $get_total = 0;

                foreach ($get_q_details as $q) {
                    $services_option .= "<option onchange='onchange_services_get_s_amount(" . $q->id_quotation_details . ")' value='" . $q->id_quotation_details . "' >" . $q->services_type . '</option>';
                    if ($q->type == 'service_level') {
                        $get_total_amount = quotations_detail::where('uniq_id', $q->uniq_id)
                            ->select('total', 'services_type', 'default_rate_of_exchange_amt')
                            ->get();

                        $hotel_exchange_total = null;
                        $visa_exchange_total = null;
                        $land_exchange_total = null;
                        $ticket_exchange_total = null;
                        $quote_grand_total = null;

                        if (isset($get_total_amount)) {
                            foreach ($get_total_amount as $quote_total) {
                                if ($quote_total->services_type == 'Hotel') {
                                    $hotel_exchange_total = $quote_total->total * $quote_total->default_rate_of_exchange_amt;
                                }
                                if ($quote_total->services_type == 'Visa') {
                                    $visa_exchange_total = $quote_total->total * $quote_total->default_rate_of_exchange_amt;
                                }
                                if ($quote_total->services_type == 'Land Services') {
                                    $land_exchange_total = $quote_total->total * $quote_total->default_rate_of_exchange_amt;
                                }
                                if ($quote_total->services_type == 'Air Ticket') {
                                    $ticket_exchange_total = $quote_total->total;
                                }
                            }
                            $quote_grand_total = $hotel_exchange_total + $visa_exchange_total + $land_exchange_total + $ticket_exchange_total;
                        }
                        $get_total = $quote_grand_total;
                    } else {
                        $get_total_detail = quotations_detail::where('uniq_id', $q->uniq_id)
                            ->select('total')
                            ->first();
                        $get_total += $get_total_detail->total;
                    }
                }
                $get_payment = payment::where('quotation_id', $get_q->id_quotations)->select('paid_amount')->first();
                // dd($get_payment);

                if ($get_payment) {
                    $get_total = $get_total - $get_payment->paid_amount;
                }
                // dd($get_total);
                return response()->json([
                    'services_option' => $services_option,
                    'total_amount' => $get_total,
                    'quotation_status' => 1,
                    'quotation_details' => '<span>' . $get_q->id_quotations . '</span>'
                ]);
            } else {
            }
            return response()->json([
                'quotation_status' => 0,
                'total_amount' => 0
            ]);
        }
    }

    function onchange_amount_val($i_id)
    {
        if (request()->ajax()) {
            $get_q = quotation::where('inquiry_id', $i_id)->where('status', '>=', 3)->latest()->first();
            $get_q_details = quotations_detail::where('quotation_id', $get_q->id_quotations)->get();
            // dd($get_q_details);
            $services_option = "<option value=''>Select</option>";
            $get_total = 0;

            foreach ($get_q_details as $q) {
                $services_option .= "<option onchange='onchange_services_get_s_amount(" . $q->id_quotation_details . ")' value='" . $q->id_quotation_details . "' >" . $q->services_type . '</option>';
                if ($q->type == 'service_level') {
                    $get_total_detail = quotations_detail::where('uniq_id', $q->uniq_id)
                        ->select('total')
                        ->get()
                        ->sum('total');
                    $get_total = $get_total_detail;
                } else {
                    $get_total_detail = quotations_detail::where('uniq_id', $q->uniq_id)
                        ->select('total')
                        ->first();
                    $get_total += $get_total_detail->total;
                }
            }
            $get_payment = payment::where('quotation_id', $get_q->id_quotations)->select('paid_amount')->first();
            // dd($get_payment);

            if ($get_payment) {
                $get_total = $get_total - $get_payment->paid_amount;
            }
            // dd($get_total);
            return response()->json([
                'total_amount' => $get_total,
            ]);
        }
    }

    function get_followup_details($followup_id)
    {
        $get_follow_up = followup_remark::where('id_followup_remarks', $followup_id)->first();
        $follow_up_name = \App\follow_up_type::where('id_follow_up_types', $get_follow_up->followup_type)->first();
        //        dd($follow_up_name);
        return response()->json([
            'followup_id' => $followup_id,
            'id_followup_remarks' => $get_follow_up->id_followup_remarks,
            'remarks' => $get_follow_up->remarks,
            'follow_up_id' => $get_follow_up->follow_up_id,
            'followup_date' => $get_follow_up->followup_date,
            'followup_type' => $get_follow_up->followup_type,
            'followup_type_name' => $follow_up_name->type_name,
            'followup_status' => $get_follow_up->followup_status,
            'user_id' => $get_follow_up->user_id,
        ]);
    }

    // 27/10/23 Yousuf Works

    public function getescalationsData()
    {
        $query = escallation::latest()->where('is_read', 0);
        $esc = escallation::latest()->limit(5)->where('is_read', 0)->get();

        $totalRecords = $query->count();
        $data = $query
            ->offset(request('start'))
            ->limit(request('length'))
            ->get();

        $get_data = [];
        // foreach ($data as $esc_not) {

        // for ($i = 0; $i < $count; $i++) {
        foreach ($data as $key => $value) {
            $decode = json_decode($value->user_id);
            // dd($decode);
            $count = sizeof($decode);
            if (isset($decode[$key]) && $decode[$key] == auth()->user()->id) {
                // dd($value);
                if ($value->is_read == '0') {
                    $row = '<button class="btn btn-danger">Unread</button>';
                } else {
                    $row = '<button class="btn btn-success">Read</button>';
                }
                if ($value->type == 'quotation_issuance') {
                    $type = '<span class="">Issuance Quotation</span>';
                } else {
                    $type = 'Escalation';
                }
                $DateValue = Carbon::parse($value->created_at)->format('d-m-y');
                $get_data[] = array(++$key, $type, 'New Escalation ' . $value->escellation_status . ' | Against Inquiry#' . $value->inquiry_id, $row, $DateValue);
            }
        }
        // }
        // }

        $output = array(
            'draw' => intval($_GET['draw']),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $get_data
        );

        echo json_encode($output);
    }

    public function getissuanceData()
    {
        $query = Notification::query();
        $query
            ->select('notifications.id', 'notifications.type', 'notifications.message', 'notifications.is_read', 'notifications.created_at')
            ->where('notifications.type', '=', 'quotation_issuance');

        $totalRecords = $query->count();
        $data = $query
            ->offset(request('start'))
            ->limit(request('length'))
            ->get();

        $get_data = [];
        foreach ($data as $key => $value) {
            if ($value->is_read == '0') {
                $row = '<button class="btn btn-danger">Unread</button>';
            } else {
                $row = '<button class="btn btn-success">Read</button>';
            }
            if ($value->type == 'quotation_issuance') {
                $type = '<span class="">Issuance Quotation</span>';
            } else {
                $type = '';
            }
            $DateValue = Carbon::parse($value->created_at)->format('d-m-y');
            $get_data[] = array(++$key, $type, $value->message, $row, $DateValue);
        }

        $output = array(
            'draw' => intval($_GET['draw']),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $get_data
        );

        echo json_encode($output);
    }

    public function getnotificationsData()
    {
        $query = Notification::query();
        $query
            ->select('notifications.id', 'notifications.type', 'notifications.message', 'notifications.is_read', 'notifications.created_at')
            ->where('notifications.type', '=', 'self_inquiry');

        $totalRecords = $query->count();
        $data = $query
            ->offset(request('start'))
            ->limit(request('length'))
            ->get();

        $get_data = [];

        foreach ($data as $key => $value) {
            if ($value->is_read == '0') {
                $row = '<a class="btn btn-danger" href="' . url('/notification_read_my_jobs' . '/' . Crypt::encrypt($value->id)) . '">Un Read</a>';
            } else {
                $row = '<button class="btn btn-success">Read</button>';
            }
            if ($value->type == 'self_inquiry') {
                $type = '<span class="">Self Inquiry</span>';
            } else {
                $type = '';
            }
            $DateValue = Carbon::parse($value->created_at)->format('d-m-y');
            $get_data[] = array(++$key, $type, $value->message, $row, $DateValue);
        }

        $output = array(
            'draw' => intval($_GET['draw']),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $get_data
        );

        echo json_encode($output);
    }

    public function getteamnotificationsData()
    {
        $query = Notification::query();
        $query
            ->select('notifications.id', 'notifications.type', 'notifications.message', 'notifications.is_read', 'notifications.created_at')
            ->where('notifications.type', '=', 'team_inquiry');
        $totalRecords = $query->count();
        $data = $query
            ->offset(request('start'))
            ->limit(request('length'))
            ->get();
        $get_data = [];
        foreach ($data as $key => $value) {
            if ($value->is_read == '0') {
                $row = '<button class="btn btn-danger">Unread</button>';
            } else {
                $row = '<button class="btn btn-success">Read</button>';
            }
            if ($value->type == 'team_inquiry') {
                $type = '<span class="">Team Iquiry</span>';
            } else {
                $type = '';
            }
            $DateValue = Carbon::parse($value->created_at)->format('d-m-y');
            $get_data[] = array(++$key, $type, $value->message, $row, $DateValue);
        }

        $output = array(
            'draw' => intval($_GET['draw']),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $get_data
        );

        echo json_encode($output);
    }

    public function getapprovalData()
    {
        $query = Notification::query();
        $query
            ->select('notifications.id', 'notifications.type', 'notifications.message', 'notifications.is_read', 'notifications.created_at')
            ->where('notifications.type', '=', 'quotation_approval');

        $totalRecords = $query->count();
        $data = $query
            ->offset(request('start'))
            ->limit(request('length'))
            ->get();

        $get_data = [];
        foreach ($data as $key => $value) {
            if ($value->is_read == '0') {
                $row = '<button class="btn btn-danger btn-center">Unread</button>';
            } else {
                $row = '<button type="button" class="custom-button text-center">Read</button>';
            }
            if ($value->type == 'quotation_approval') {
                $type = 'Qoutation Approval';
            } else {
                $type = '';
            }
            $DateValue = Carbon::parse($value->created_at)->format('d-M-Y');
            $get_data[] = array(++$key, $type, $value->message, $row, $DateValue);
        }

        $output = array(
            'draw' => intval($_GET['draw']),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $get_data
        );

        echo json_encode($output);
    }

    public function get_team_users($id)
    {
        $depart_team_head = department_team::where('id_department_teams', $id)->first();
        $get_users = json_decode($depart_team_head->user_id);
        $options = '<option value="">Select</option>';
        foreach ($get_users as $key => $value) {
            if ($value) {
                $user = User::where('id', $value)->select('name')->first();
                $options .= "<option value='" . $value . "'>" . $user->name . '</option>';
            }
        }
        return response()->json([
            'data' => $options,
        ]);
    }

    public function getCustomerData()
    {
        $query = customer::query();
        $query
            ->select(
                'customers.id_customers',
                'customers.customer_name',
                'customers.customer_type',
                'users.name as sale_person',
                'customers.whatsapp_number',
                'customers.customer_phone2',
                'customers.customer_email',
                'cities.name as city_name',
                'countries.name as country_name'
            )
            ->leftJoin('users', 'users.id', '=', 'customers.sale_person')
            ->leftJoin('cities', 'cities.id', '=', 'customers.city_id')
            ->leftJoin('countries', 'countries.id_countries', '=', 'customers.country');

        // Process search query (if provided)
        $searchValue = request('search')['value'] ?? '';
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q
                    ->where('customers.customer_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('customers.customer_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('customers.whatsapp_number', 'like', '%' . $searchValue . '%')
                    ->orWhere('customers.customer_phone2', 'like', '%' . $searchValue . '%')
                    ->orWhere('customers.customer_email', 'like', '%' . $searchValue . '%')
                    ->orWhere('cities.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('countries.name', 'like', '%' . $searchValue . '%');
            });
        }

        // Get the total number of records (required for pagination)
        $totalRecords = customer::count();

        // Get the data for the current page
        $data = $query
            ->offset(request('start'))
            ->limit(request('length'))
            ->get()
            ->map(function ($item) {
                $item->actions = '<a class="btn btn-sm btn-primary" title="Edit" href="' . url('customers/edit/' . Crypt::encrypt($item->id_customers)) . '"><i class="fa fa-edit"></i></a> '
                    . '<a class="btn btn-sm btn-success" title="View" href="' . url('customers/view/' . Crypt::encrypt($item->id_customers)) . '"><i class="fa fa-eye"></i></a> '
                    . '<a class="btn btn-sm btn-danger" title="Delete" href="' . url('customers/destroy/' . Crypt::encrypt($item->id_customers)) . '"><i class="fa fa-trash"></i></a>';
                return $item;
            });

        return response()->json([
            'draw' => intval(request('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $query->count(),
            'data' => $data,
        ]);
    }
}
