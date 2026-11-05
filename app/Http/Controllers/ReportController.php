<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\inquirytypes;
use App\sales_reference;
use App\other_service;
use App\customer;
use App\inquiry;
use Yajra\DataTables\DataTables;

class ReportController extends Controller
{
    public function index()
    {
        $services = other_service::whereNull('parent_id')->where('status', 'Active')->get();
        $inquiry_types = inquirytypes::all();
        $sales_reference = sales_reference::all();
        // Assuming user model for sales person
        $sales_person = User::select('id', 'name')->get();
        // Extracting distinct cities and statuses from DB for dropdowns
        $cities = customer::select('city')->whereNotNull('city')->where('city', '!=', '')->distinct()->pluck('city');
        $statuses = inquiry::select('status')->whereNotNull('status')->where('status', '!=', '')->distinct()->pluck('status');

        return view('reports.index', compact('services', 'inquiry_types', 'sales_reference', 'sales_person', 'cities', 'statuses'));
    }

    public function getData(Request $request)
    {
        $query = inquiry::with(['customer', 'inquiryType', 'salesPerson', 'salesReference', 'createdBy', 'latestFollowup'])
            ->select(['inquiry.*']);

        if ($request->filled('services')) {
            $services = $request->services;
            $query->where(function ($q) use ($services) {
                foreach ($services as $service) {
                    $q->orWhere('services_sub_services', 'LIKE', '%' . $service . '%');
                }
            });
        }
        if ($request->filled('inquiry_type')) {
            $query->whereIn('inquiry_type', $request->inquiry_type);
        }
        if ($request->filled('sales_reference')) {
            $query->whereIn('sales_reference', $request->sales_reference);
        }
        if ($request->filled('sales_person')) {
            $query->whereIn('saleperson', $request->sales_person);
        }
        if ($request->filled('city')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->whereIn('city', $request->city);
            });
        }
        if ($request->filled('status')) {
            $query->whereIn('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->date_from)));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->date_to)));
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->orderColumn('customer_name', function ($query, $order) {
                $query->orderBy(\App\customer::select('customer_name')->whereColumn('customers.id_customers', 'inquiry.customer_id')->limit(1), $order);
            })
            ->orderColumn('customer_cell', function ($query, $order) {
                $query->orderBy(\App\customer::select('customer_cell')->whereColumn('customers.id_customers', 'inquiry.customer_id')->limit(1), $order);
            })
            ->orderColumn('customer_city', function ($query, $order) {
                $query->orderBy(\App\customer::select('city')->whereColumn('customers.id_customers', 'inquiry.customer_id')->limit(1), $order);
            })
            ->orderColumn('inquiry_type_name', function ($query, $order) {
                $query->orderBy(\App\inquirytypes::select('type_name')->whereColumn('inquirytypes.type_id', 'inquiry.inquiry_type')->limit(1), $order);
            })
            ->orderColumn('salesperson_name', function ($query, $order) {
                $query->orderBy(\App\User::select('name')->whereColumn('users.id', 'inquiry.saleperson')->limit(1), $order);
            })
            ->orderColumn('sales_ref_name', function ($query, $order) {
                $query->orderBy(\App\sales_reference::select('type_name')->whereColumn('sales_reference.type_id', 'inquiry.sales_reference')->limit(1), $order);
            })
            ->orderColumn('followup_date', function ($query, $order) {
                $query->orderBy(\App\followup_remark::select('followup_date')->whereColumn('followup_remarks.inquiry_id', 'inquiry.id_inquiry')->orderByDesc('id_followup_remarks')->limit(1), $order);
            })
            ->addColumn('customer_name', function($row) {
                return $row->customer->customer_name ?? '-';
            })
            ->addColumn('customer_cell', function($row) {
                return $row->customer->customer_cell ?? '-';
            })
            ->addColumn('customer_city', function($row) {
                return $row->customer->city ?? '-';
            })
            ->addColumn('inquiry_type_name', function($row) {
                return $row->inquiryType->type_name ?? '-';
            })
            ->addColumn('salesperson_name', function($row) {
                return $row->salesPerson->name ?? '-';
            })
            ->addColumn('sales_ref_name', function($row) {
                return $row->salesReference->type_name ?? '-';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? date('Y-m-d H:i', strtotime($row->created_at)) : '-';
            })
            ->editColumn('travel_date', function ($row) {
                return $row->travel_date ? date('Y-m-d', strtotime($row->travel_date)) : '-';
            })
            ->editColumn('followup_date', function ($row) {
                $fud = $row->latestFollowup->followup_date ?? $row->followup_date;
                return $fud ? date('Y-m-d', strtotime($fud)) : '-';
            })
            ->make(true);
    }

    public function export(Request $request)
    {
        $query = \App\inquiry::with(['customer', 'inquiryType', 'salesPerson', 'salesReference', 'createdBy', 'latestFollowup'])->select(['inquiry.*']);

        if ($request->filled('services')) {
            $services = $request->services;
            $query->where(function ($q) use ($services) {
                foreach ($services as $service) {
                    $q->orWhere('services_sub_services', 'LIKE', '%' . $service . '%');
                }
            });
        }
        if ($request->filled('inquiry_type')) {
            $query->whereIn('inquiry_type', $request->inquiry_type);
        }
        if ($request->filled('sales_reference')) {
            $query->whereIn('sales_reference', $request->sales_reference);
        }
        if ($request->filled('sales_person')) {
            $query->whereIn('saleperson', $request->sales_person);
        }
        if ($request->filled('city')) {
            $cities = $request->city;
            $query->whereHas('customer', function($q) use ($cities) {
                $q->whereIn('city', $cities);
            });
        }
        if ($request->filled('status')) {
            $query->whereIn('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('inquiry.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('inquiry.created_at', '<=', $request->date_to);
        }

        $inquiries = $query->orderBy('inquiry.created_at', 'desc')->get();

        $format = $request->query('format', 'excel');
        if ($format == 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="Travel_IMS_Report_' . date('Y_m_d') . '.xls"');
            return view('reports.export', compact('inquiries'));
        } else {
            return view('reports.export', compact('inquiries'));
        }
    }


    public function count(Request $request)
    {
        $query = \App\inquiry::query();

        if ($request->filled("services")) {
            $services = $request->services;
            $query->where(function ($q) use ($services) {
                foreach ($services as $service) {
                    $q->orWhere("services_sub_services", "LIKE", "%" . $service . "%");
                }
            });
        }
        if ($request->filled("inquiry_type")) {
            $query->whereIn("inquiry_type", $request->inquiry_type);
        }
        if ($request->filled("sales_reference")) {
            $query->whereIn("sales_reference", $request->sales_reference);
        }
        if ($request->filled("sales_person")) {
            $query->whereIn("saleperson", $request->sales_person);
        }
        if ($request->filled("city")) {
            $cities = $request->city;
            $query->whereHas("customer", function($q) use ($cities) {
                $q->whereIn("city", $cities);
            });
        }
        if ($request->filled("status")) {
            $query->whereIn("status", $request->status);
        }
        if ($request->filled("date_from")) {
            $query->whereDate("created_at", ">=", $request->date_from);
        }
        if ($request->filled("date_to")) {
            $query->whereDate("created_at", "<=", $request->date_to);
        }

        return response()->json(["count" => $query->count()]);
    }

}