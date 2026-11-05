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
            $artilces = ' ';

            if ($query != null && $query != '') {
                $customers = customer::orderBy('id_customers')
                    ->where('customer_name', 'LIKE', '%' . $query . '%')
                    ->orWhere('customer_cell', '=', $query)
                    ->paginate(20);
            }

            if (isset($customers)) {
                foreach ($customers as $result) {
                    $artilces .= '<div id="get_cus_details" data-id="' . $result->id_customers . '" onClick="CusDetails()" class="az-contact-item mt-3 clickable-data"><div class="az-img-user"><img src="' . asset('img/default_user.png') . '" alt=""></div><div class="az-contact-body"><h6>' . e($result->customer_name) . '</h6><span class="phone">' . e($result->customer_cell) . '</span></div></div>';
                }
            }
            return $artilces;
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = customer::with(['salePerson', 'city'])
                ->select('customers.*')
                ->orderBy('customers.id_customers', 'desc');

            if ($request->filled('customer_name')) {
                $customers->where('customers.customer_name', 'like', '%' . $request->customer_name . '%');
            }
            if ($request->filled('customer_cell')) {
                $customers->where('customers.customer_cell', 'like', '%' . $request->customer_cell . '%');
            }
            if ($request->filled('customer_email')) {
                $customers->where('customers.customer_email', 'like', '%' . $request->customer_email . '%');
            }
            if ($request->filled('city')) {
                $cityVal = $request->city;
                $customers->where(function ($q) use ($cityVal) {
                    $q->where('customers.city', 'like', '%' . $cityVal . '%')
                        ->orWhereHas('city', function ($cq) use ($cityVal) {
                            $cq->where('name', 'like', '%' . $cityVal . '%');
                        });
                });
            }

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('image', function ($customer) {
                    $hasCustomImg = $customer->customer_image
                        && $customer->customer_image !== 'default_user'
                        && file_exists(public_path('uploads/customer_images/' . $customer->customer_image));
                    return $hasCustomImg
                        ? '<img src="' . asset('uploads/customer_images/' . $customer->customer_image) . '" style="height:40px;width:40px;border-radius:50%;object-fit:cover"/>'
                        : '<img src="' . asset('img/default_user.png') . '" style="height:40px;width:40px;border-radius:50%"/>';
                })
                ->addColumn('sale_person_name', function ($customer) {
                    if ($customer->salePerson && $customer->salePerson->name) {
                        return $customer->salePerson->name;
                    }
                    $raw = $customer->getAttributes()['sale_person'] ?? '';
                    return (!is_numeric($raw) && !empty($raw)) ? $raw : '-';
                })
                ->addColumn('city_name', function ($customer) {
                    $relCity = $customer->relationLoaded('city') ? $customer->getRelation('city') : null;
                    return $relCity->name ?? ($customer->getAttributes()['city'] ?? '-');
                })
                ->addColumn('whatsapp_enabled', function ($customer) {
                    return $customer->whatsapp_check == 1
                        ? '<img src="' . asset('img/whatsapp.png') . '" style="height:20px;width:20px;border-radius:50%"/>'
                        : '';
                })
                ->addColumn('customer_mobile', function ($customer) {
                    return $customer->customer_cell;
                })
                ->addColumn('customer_phone1', function ($customer) {
                    return $customer->customer_phone1 ?: $customer->whatsapp_number;
                })
                ->addColumn('customer_phone2', function ($customer) {
                    return $customer->customer_phone2;
                })
                ->addColumn('action', function ($customer) {
                    $editBtn = '';
                    $deleteBtn = '';
                    if (auth()->user()?->can('Customers edit')) {
                        $editBtn = '<a href="' . route('customers.edit', $customer->id_customers) . '" class="btn btn-sm btn-primary me-2"><i class="fa fa-pen"></i></a>';
                    }
                    if (auth()->user()?->can('Customers delete')) {
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

    public function getData(Request $request)
    {
        $perPage = 1;
        $data = customer::skip($perPage)
            ->take($perPage)
            ->get();
        return response()->json($data);
    }

    public function create(Request $request)
    {
        $countries = countries::all();
        $sale_persons = User::orderBy('name')->select('users.name', 'users.id')->get()->toArray();
        return view('customers.create', compact('countries', 'sale_persons'));
    }

    public function getCity(Request $request)
    {
        if (is_numeric($request->country_id)) {
            $countries = countries::where('id_countries', $request->country_id)->first();
        } else {
            $countries = countries::where('name', $request->country_id)->first();
        }

        $data['cities'] = $countries
            ? \App\cities::where('country_id', $countries->id_countries)->get()
            : [];

        return response()->json($data);
    }

    public function get_customer_details(Request $request)
    {
        $data = customer::where('id_customers', $request->id)->first();
        if (!$data) {
            return response()->json(['html' => '', 'customer' => null], 404);
        }
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
        $request->validate([
            'customer_cell' => 'required',
            'customer_name' => 'required',
            'sale_person' => 'required'
        ]);

        $Customer = new customer();
        $Customer->customer_name = $request->customer_name;
        $Customer->customer_type = $request->customer_type ?: 'Individual';
        $Customer->customer_cell = $request->customer_cell;
        if ($request->whatsapp_check == 'on') {
            $Customer->whatsapp_check = 1;
        }
        $Customer->customer_phone1 = $request->customer_whatsapp;
        $Customer->whatsapp_number = $request->customer_whatsapp;
        $Customer->customer_phone2 = $request->customer_phone_2;
        $Customer->customer_address = $request->customer_address;
        $Customer->customer_email = $request->customer_email;
        $Customer->business_id = 1;
        $Customer->customer_reference = $request->customer_reference;
        $Customer->customer_remarks = $request->customer_remarks;
        $Customer->sale_person = $request->sale_person;
        $Customer->status = $request->status ?: 'Verified';
        $Customer->accounts_customer_rating = $request->accounts_customer_rating;
        $Customer->country = $request->country;

        if (is_numeric($request->city)) {
            $Customer->city_id = (int) $request->city;
            $cityRow = \App\cities::find((int) $request->city);
            if ($cityRow) {
                $Customer->city = $cityRow->name;
            }
        } else {
            $Customer->city = $request->city;
        }

        $Customer->created_by = auth()->user()->id;

        if ($request->hasFile('customer_image')) {
            $file = $request->file('customer_image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('uploads/customer_images/'), $filename);
            $Customer->customer_image = $filename;
        }

        $Customer->save();

        session()->flash('success', 'Customer Added Successfully');

        try {
            if (function_exists('sendNoti')) {
                sendNoti('New Customer Added By ' . (auth()->user()->name ?? 'Admin'), 'Customer Created', 'Installation');
            }
        } catch (\Throwable $e) {
        }

        return redirect(url('customers'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {}

    public function check_customer_number($cell)
    {
        $get_customer = customer::where('customer_cell', $cell)->first();

        return response()->json([
            'getCell' => (bool) $get_customer
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $customer = customer::findOrFail($id);

        $countries = countries::all();
        $sale_persons = User::orderBy('name')
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
        $Customer->customer_type = $request->customer_type ?: 'Individual';
        $Customer->customer_cell = $request->customer_cell;
        $Customer->whatsapp_check = $request->whatsapp_check == 'on' ? 1 : 0;
        $Customer->customer_phone1 = $request->customer_whatsapp;
        $Customer->whatsapp_number = $request->customer_whatsapp;
        $Customer->customer_phone2 = $request->customer_phone_2;
        $Customer->customer_address = $request->customer_address;
        $Customer->customer_email = $request->customer_email;
        $Customer->customer_reference = $request->customer_reference;
        $Customer->customer_remarks = $request->customer_remarks;
        $Customer->sale_person = $request->sale_person;
        $Customer->status = $request->status ?: 'Verified';
        $Customer->accounts_customer_rating = $request->accounts_customer_rating;
        $Customer->country = $request->country;

        if (is_numeric($request->city)) {
            $Customer->city_id = (int) $request->city;
            $cityRow = \App\cities::find((int) $request->city);
            if ($cityRow) {
                $Customer->city = $cityRow->name;
            }
        } else {
            $Customer->city = $request->city;
            $Customer->city_id = null;
        }

        if ($request->input('remove_image') == '1') {
            $Customer->customer_image = null;
        }

        if ($request->hasFile('customer_image')) {
            $file = $request->file('customer_image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('uploads/customer_images/'), $filename);
            $Customer->customer_image = $filename;
        }

        $Customer->save();

        session()->flash('success', 'Customer Updated Successfully');
        return redirect(url('customers'));
    }

    public function view($id)
    {
        $view = customer::where('id_customers', $id)->firstOrFail();

        $inquiry = inquiry::select('inquiry.*', 'inquirytypes.type_id', 'inquirytypes.type_name')
            ->leftJoin('inquirytypes', 'inquirytypes.type_id', '=', 'inquiry.inquiry_type')
            ->where('customer_id', $view->id_customers)
            ->get();

        $customers = customer::all();
        return view('customers.view', compact('customers', 'view', 'inquiry'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        customer::where('id_customers', $id)->delete();
        session()->flash('success', 'Deleted Successfully');
        return redirect()->back();
    }
}
