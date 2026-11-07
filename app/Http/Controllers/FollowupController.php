<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\follow_up_type;
use App\inquiry;
use App\inquirytypes;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FollowupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $followup_types = follow_up_type::get();
        $sales_person = User::all();
        $data['inquiry_type'] = inquirytypes::select('type_id', 'type_name')->get();
        // dd($data['inquiry_type']);
        return view('followups.index', compact('data', 'followup_types', 'sales_person'));
    }

    public function getData(Request $request)
    {
        $query = \App\followup_remark::leftJoin('inquiry', 'followup_remarks.inquiry_id', '=', 'inquiry.id_inquiry')
            ->leftJoin('customers', 'inquiry.customer_id', '=', 'customers.id_customers')
            ->leftJoin('inquirytypes', 'inquiry.inquiry_type', '=', 'inquirytypes.type_id')
            ->leftJoin('users as sp', 'inquiry.saleperson', '=', 'sp.id')
            ->leftJoin('sales_reference', 'inquiry.sales_reference', '=', 'sales_reference.type_id')
            ->leftJoin('users as cb', 'followup_remarks.created_by', '=', 'cb.id')
            ->select([
                'followup_remarks.*',
                'customers.customer_name',
                'customers.customer_cell',
                'inquirytypes.type_name as inquiry_type_name',
                'sp.name as salesperson_name',
                'sales_reference.type_name as sales_ref_name',
                'cb.name as created_by_name'
            ])
            ->orderBy('followup_remarks.id_followup_remarks', 'DESC');

        if (!$request->has('order')) {
            $query->orderBy('followup_remarks.followup_date', 'DESC');
        }

        $authUser = auth()->user();
        $isAdmin = $authUser && ($authUser->hasRole(['Admin', 'Super-Admin', 'admin', 'super-admin']) || $authUser->role_id == 1 || $authUser->can('Super-Admin'));
        $onlyAssigned = \App\Models\SystemSetting::get('only_assigned_inquiries', '0') === '1';

        if (!$isAdmin && $onlyAssigned) {
            $query->where(function ($q) use ($authUser) {
                $q->where('inquiry.saleperson', $authUser->id)
                  ->orWhere('inquiry.created_by', $authUser->id);
            });
        }

        // Apply filters
        if ($v = request('sales_person')) {
            $query->whereHas('inquiry', function ($q) use ($v) {
                $q->where('saleperson', $v);
            });
        }

        if ($v = request('status')) {
            $query->where('followup_status', $v);
        }

        // Search filter (Customer name or phone)
        if ($v = request('search_val')) {
            $query->whereHas('inquiry.customer', function ($q) use ($v) {
                $q
                    ->where('customer_name', 'LIKE', "%{$v}%")
                    ->orWhere('customer_cell', 'LIKE', "%{$v}%");
            });
        }

        if ($v = request('date_from')) {
            $query->whereDate('followup_date', '>=', $v);
        }
        if ($v = request('date_to')) {
            $query->whereDate('followup_date', '<=', $v);
        }

        if ($v = request('customer_name')) {
            $query->where('customers.customer_name', 'like', "%{$v}%");
        }
        if ($v = request('customer_cell')) {
            $query->where('customers.customer_cell', 'like', "%{$v}%");
        }
        if ($rawIdInput = request('id_inquiry')) {
            $rawTokens = preg_split('/[,]+/', (string) $rawIdInput);
            $ids = [];
            foreach ($rawTokens as $tok) {
                $c = trim(ltrim(trim($tok), '#'));
                if ($c !== '' && is_numeric($c)) {
                    $ids[] = (int) $c;
                }
            }
            $ids = array_values(array_unique($ids));
            if (count($ids) > 1) {
                $query->whereIn('followup_remarks.inquiry_id', $ids);
            } elseif (count($ids) === 1) {
                $query->where('followup_remarks.inquiry_id', $ids[0]);
            } else {
                $query->where('followup_remarks.inquiry_id', $rawIdInput);
            }
        }

        // If "Follow-up Today" filter is requested
        if (request('filter') == 'today') {
            $query->whereDate('followup_date', now()->toDateString());
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('id_inquiry', fn($fr) => $fr->inquiry_id)
            ->addColumn('inquiry_type', fn($fr) => $fr->inquiry_type_name ?? '-')
            ->addColumn('customer_name', fn($fr) => $fr->customer_name ?? '-')
            ->addColumn('customer_cell', fn($fr) => $fr->customer_cell ?? '-')
            ->addColumn('saleperson', fn($fr) => $fr->salesperson_name ?? '-')
            ->addColumn('sales_reference', fn($fr) => $fr->sales_ref_name ?? '-')
            ->addColumn('remarks', function ($fr) {
                return '<span title="' . e($fr->remarks) . '">' . \Illuminate\Support\Str::limit($fr->remarks, 50) . '</span>';
            })
            ->editColumn('status', function ($fr) {
                $status = $fr->followup_status;
                $color = 'secondary';
                if ($status == 'Open')
                    $color = 'info';
                elseif ($status == 'Completed' || $status == 'Confirmed' || $status == 'Confirm')
                    $color = 'success';
                elseif ($status == 'Cancelled')
                    $color = 'danger';
                elseif ($status == 'In-Progress')
                    $color = 'primary';
                elseif ($status == 'Hold')
                    $color = 'warning';
                return '<span class="badge badge-sm bg-gradient-' . $color . '">' . $status . '</span>';
            })
            ->addColumn('followup_date', function ($fr) {
                if ($fr->followup_date) {
                    $date = date('d M Y', strtotime($fr->followup_date));
                    $time = $fr->followup_time ? ' <small class="text-muted">' . date('h:i A', strtotime($fr->followup_time)) . '</small>' : '';
                    return $date . $time;
                }
                return '-';
            })
            ->addColumn('created_at', fn($fr) => $fr->created_at ? \Carbon\Carbon::parse($fr->created_at)->format('d M Y') : '-')
            ->addColumn('created_by', fn($fr) => $fr->created_by_name ?? '-')
            ->addColumn('updated_at', fn($fr) => $fr->updated_at ? \Carbon\Carbon::parse($fr->updated_at)->format('d M Y') : ($fr->created_at ? \Carbon\Carbon::parse($fr->created_at)->format('d M Y') : '-'))
            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->where('customers.customer_name', 'like', "%{$keyword}%");
            })
            ->filterColumn('customer_cell', function ($query, $keyword) {
                $query->where('customers.customer_cell', 'like', "%{$keyword}%");
            })
            ->filterColumn('inquiry_type', function ($query, $keyword) {
                $query->where('inquirytypes.type_name', 'like', "%{$keyword}%");
            })
            ->filterColumn('saleperson', function ($query, $keyword) {
                $query->where('sp.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('sales_reference', function ($query, $keyword) {
                $query->where('sales_reference.type_name', 'like', "%{$keyword}%");
            })
            ->filterColumn('created_by', function ($query, $keyword) {
                $query->where('cb.name', 'like', "%{$keyword}%");
            })
            ->addColumn('row_class', fn($i) => $this->rowClass($i, request('followup_past', 0), request('followup_today', 0)))
            ->setRowClass(function ($fr) {
                if ($fr->followup_date) {
                    $date = \Carbon\Carbon::parse($fr->followup_date);
                    if ($date->isToday())
                        return 'row-today';
                    if ($date->isPast())
                        return 'row-overdue';
                }
                return '';
            })
            ->escapeColumns([])
            ->make(true);
    }
    private function rowClass($inquiry, $past, $today)
    {
        $date = $inquiry->followup_date ?? ($inquiry->latestFollowup->followup_date ?? null);
        if (!$date)
            return 'row-no-followup';

        $followDate = date('Y-m-d', strtotime($date));
        $currentDate = date('Y-m-d');

        if ($followDate < $currentDate) {
            return 'row-overdue';
        }
        if ($followDate === $currentDate) {
            return 'row-today';
        }

        return 'row-upcoming';
    }
}
