<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\care_of_detail;
use App\City;
use App\countries;
use App\customer;
use App\inquiry;
use App\quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function customer_search(Request $request, $query = null)
    {
        if ($request->ajax()) {
            $bilal = $request->page;
            $artilces = ' ';

            if ($query != null && $query != '') {
                // $artilces = '';
                $customers = customer::orderBy('id_customers')->where('customer_name', 'LIKE', '%' . $query . '%')->orWhere('customer_cell', '=', $query)->paginate(20);
                // dd($customers);
            } else {
                $artilces = ' ';
            }

            if (isset($customers)) {
                foreach ($customers as $result) {
                    $artilces .= '<div id="get_cus_details" data-id="' . $result->id_customers . '" onClick="CusDetails()" class="az-contact-item mt-3 clickable-data"><div class="az-img-user"><img src="' . asset('img/default_user.png') . '" alt=""></div><div class="az-contact-body"><h6>' . $result->customer_name . '</h6><span class="phone">' . $result->customer_cell . '</span></div></div>';
                }
            }
            return $artilces;
        }
    }

    public function index(Request $request)
    {
        // $user = Auth::user();
        // $user->assignRole('Admin');
        // dd($user->getRoleNames());
        // exit;
        if ($request->ajax()) {
            $customers = customer::with(['salePerson', 'city'])
                ->select('customers.*')
                ->orderBy('customers.id_customers', 'desc');

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('image', function ($customer) {
                    return $customer->customer_image
                        ? '<img src="' . asset('uploads/customer_images/' . $customer->customer_image) . '" style="height:40px;width:40px;border-radius:50%"/>'
                        : '<img src="' . asset('img/default_user.png') . '" style="height:40px;width:40px;border-radius:50%"/>';
                })
                ->addColumn('sale_person_name', function ($customer) {
                    return $customer->salePerson->name ?? '';
                })
                ->addColumn('whatsapp_enabled', function ($customer) {
                    return $customer->whatsapp_check == 1
                        ? '<img src="' . asset('img/whatsapp.png') . '" style="height:20px;width:20px;border-radius:50%"/>'
                        : '';
                })
                ->addColumn('customer_mobile', function ($customer) {
                    return $customer->customer_cell;  // Not customer_cell
                })
                ->addColumn('customer_phone1', function ($customer) {
                    return $customer->customer_phone1;  // Not customer_cell
                })
                ->addColumn('customer_phone2', function ($customer) {
                    return $customer->customer_phone2;  // Not customer_cell
                })
                ->addColumn('action', function ($customer) {
                    $editBtn = '';
                    $deleteBtn = '';
                    if (auth()->user()?->can('Customers edit')) {
                        $editBtn = '<a href="' . route('customers.edit', $customer->id_customers) . '" class="btn btn-sm btn-primary me-2"><i class="fa fa-pen"></i></a>';
                    }
                    if (auth()->user()?->can('Customers delete')) {
                        // Delete is sent as a POST form with CSRF token (not a plain GET link)
                        $deleteBtn = '<form method="POST" action="' . url('customers/destroy/' . $customer->id_customers) . '" class="d-inline m-0" onsubmit="return confirm(\'Are you sure?\')">'
                            . '<input type="hidden" name="_token" value="' . csrf_token() . '">'
                            . '<button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></form>';
                    }
                    return '<div class="d-flex justify-content-center">' . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['image', 'whatsapp_enabled', 'action'])
                ->make(true);
        }

        return view('customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getData(Request $request)
    {
        // Define the number of records to load
        $perPage = 1;

        // Retrieve data based on the scroll position
        $data = customer::skip($perPage)
            ->take($perPage)
            ->get();
        // Return the data as JSON response
        return response()->json($data);
    }

    public function create(Request $request)
    {
        $countries = countries::all();
        $sale_persons = \App\Models\User::select('users.name', 'users.id')->where('role_id', '=', 6)->get()->toArray();
        //        dd($sale_persons);
        return view('customers.create', compact('countries', 'sale_persons'));
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
        $data = customer::where('id_customers', $request->id)->first();
        $html = '<h5>ID: <span style="text-decoration: underline;">' . $data->id_customers . '<input type="hidden" value="' . $data->id_customers . '" name="searched_customer_id"/></span></h5>
            <h5>Customer: <span style="text-decoration: underline;">' . htmlspecialchars($data->customer_name) . '</span></h5>
            <p>Contact# <span style="text-decoration: underline;">' . htmlspecialchars($data->customer_cell) . '</span></p>
            <p>Email: <span style="text-decoration: underline;">' . htmlspecialchars($data->customer_email ?? '') . '</span></p>
            <p>Last Inquiry# <span style="text-decoration: underline;"></span></p>';

        return response()->json([
            'html' => $html,
            'customer' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'customer_cell' => 'required',
            'customer_name' => 'required',
            'sale_person' => 'required'
        ]);
        //        // try {
        //        $size = sizeof($request->care_of_name);
        //        $care_of_name = $request->care_of_name;
        //        $care_of_relation = $request->care_of_relation;
        //        $care_of_cell = $request->care_of_cell;
        //        $care_of_email = $request->care_of_email;
        //        $care_of_age = $request->care_of_age;

        $Customer = new customer();
        $Customer->customer_name = $request->customer_name;
        $Customer->customer_type = $request->customer_type;
        $Customer->customer_cell = $request->customer_cell;
        if ($request->whatsapp_check == 'on') {
            //            dd($request->whatsapp_check);
            $Customer->whatsapp_check = 1;
        }
        $Customer->customer_phone1 = $request->customer_whatsapp;
        $Customer->customer_phone2 = $request->customer_phone_2;
        $Customer->customer_address = $request->customer_address;
        $Customer->customer_email = $request->customer_email;
        $Customer->business_id = 1;
        $Customer->customer_reference = $request->customer_reference;
        $Customer->customer_remarks = $request->customer_remarks;
        $Customer->sale_person = $request->sale_person;
        $Customer->status = $request->status;
        $Customer->accounts_customer_rating = $request->accounts_customer_rating;
        $Customer->country = $request->country;
        $Customer->city_id = $request->city;
        $Customer->created_by = auth()->user()->id;

        if (!empty($request->customer_image)) {
            $file = $request->file('customer_image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('uploads/customer_images/'), $filename);
            $data['image'] = $filename;
            $Customer->customer_image = $data['image'];
        }
        $cus_id = customer::max('id_customers');
        // dd($cus_id);

        $Customer->save();

        //        for ($i = 1; $i < $size; $i++) {
        //
        //            $detail = new care_of_detail();
        //            $detail->customer_id = $cus_id;
        //            $detail->care_of_name = $care_of_name[$i];
        //            $detail->care_of_relation = $care_of_relation[$i];
        //            $detail->care_of_cell = $care_of_cell[$i];
        //            $detail->care_of_email = $care_of_email[$i];
        //            $detail->care_of_age = $care_of_age[$i];
        //            $detail->save();
        //        }
        session()->flash('success', 'Customer Added Successfully');

        sendNoti('New Customer Added By ' . 'bilal', 'sds', 'Installation');
        return redirect()->back();
        // } catch (\Throwable $th) {
        //     session()->flash('error', $th->getMessage());
        //     return redirect()->back();
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(service_vendor $service_vendor) {}

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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $customer = customer::findOrFail($id);

        $countries = countries::all();
        $sale_persons = User::where('role_id', 6)
            ->select('id', 'name')
            ->get();

        return view('customers.edit', compact('customer', 'countries', 'sale_persons'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_cell' => 'required',
            'customer_name' => 'required',
            'sale_person' => 'required'
        ]);

        $Customer = customer::findOrFail($id);

        $Customer->customer_name = $request->customer_name;
        $Customer->customer_type = $request->customer_type;
        $Customer->customer_cell = $request->customer_cell;
        $Customer->whatsapp_check = $request->whatsapp_check == 'on' ? 1 : 0;
        $Customer->customer_phone1 = $request->customer_whatsapp;
        $Customer->customer_phone2 = $request->customer_phone_2;
        $Customer->customer_address = $request->customer_address;
        $Customer->customer_email = $request->customer_email;
        $Customer->customer_reference = $request->customer_reference;
        $Customer->customer_remarks = $request->customer_remarks;
        $Customer->sale_person = $request->sale_person;
        $Customer->status = $request->status;
        $Customer->accounts_customer_rating = $request->accounts_customer_rating;
        $Customer->country = $request->country;
        $Customer->city_id = $request->city;

        if (!empty($request->customer_image)) {
            $file = $request->file('customer_image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('uploads/customer_images/'), $filename);
            $Customer->customer_image = $filename;
        }

        $Customer->save();

        session()->flash('success', 'Customer Updated Successfully');
        return redirect()->back();
    }

    public function view($id)
    {
        $view = customer::where('id_customers', $id)->first();
        // $inquiry1 = inquiry::where('customer_id', $view->id_customers)->get();

        $inquiry = inquiry::select('inquiry.*', 'inquirytypes.type_id', 'inquirytypes.type_name')
            ->join('inquirytypes', 'inquirytypes.type_id', 'inquiry.id_inquiry')
            // ->join('users' ,'users.id' , 'inquiry.saleperson' )
            ->where('customer_id', $view->id_customers)
            ->get();

        $customers = customer::all();
        $view = customer::where('id_customers', $id)->first();
        // dd($view);
        return view('customers.view', compact('customers', 'view', 'inquiry'));
    }

    /** Update the specified resource in storage. */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $dec_id = Crypt::decrypt($id);
        $edit_vendor = customer::where('id_customers', $id)->delete();
        // dd($edit_vendor);
        session()->flash('success', 'Deleted Successfully');
        return redirect()->back();
    }
}
