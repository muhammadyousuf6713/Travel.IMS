<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\campaign;
use App\countries;
use App\customer;
use App\department_service;
use App\department_sub_service;
use App\departments;
use App\follow_up;
use App\follow_up_type;
use App\followup_remark;
use App\inquiry;
use App\inquirytypes;
use App\my_job;
use App\my_team_job;
use App\other_service;
use App\remarks;
use App\role_permission;
use App\sales_reference;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Cache;

class InquiryController extends Controller
{
    protected $role_id;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $inquiry = inquiry::select('inquiry.*', 'users.name as admin_name', 'inquiry.saleperson as sales_man', 'inquirytypes.*', 'inquiry.created_at as inquiry_date', 'inquiry.updated_at as inquiry_update_date', 'inquiry.created_by as created_admin', 'sales_reference.type_name as sales_ref', 'users.name as created_by_name')
            ->join('inquirytypes', 'inquirytypes.type_id', '=', 'inquiry.inquiry_type', 'left')
            ->join('sales_reference', 'sales_reference.type_id', '=', 'inquiry.sales_reference', 'left')
            ->join('users', 'users.id', '=', 'inquiry.created_by', 'left')
            ->groupBy('inquiry.id_inquiry')
            ->orderBy('inquiry.id_inquiry', 'DESC')
            ->get()
            ->toArray();
        // dd($inquiry);
        $saved_remarks = remarks::select('*', 'users.name as remarks_by', 'remarks.created_at as created_on')
            ->join('users', 'users.id', '=', 'remarks.created_by', 'left')
            ->get()
            ->toArray();

        echo '<pre>';
        print_r($inquiry);
        exit;
        return view('./inquiry.index', compact('inquiry', 'saved_remarks'));
    }

    public function get_inquiry_list()
    {
        $followup_types = follow_up_type::get();
        $sales_person = User::all();  // adjust filter if you only want sales users

        $data['inquiry_type'] = inquirytypes::select('type_id', 'type_name')->get();
        $data['sales_reference'] = \App\sales_reference::select('type_id', 'type_name')->get();

        $query = inquiry::with([
            'customer:id_customers,customer_name,customer_cell,customer_email,customer_phone1',
            'inquiryType:type_id,type_name',
            'salesReference:type_id,type_name',
            'createdBy:id,name'
        ])
            ->select('inquiry.*')  // Add only necessary fields
            ->orderBy('id_inquiry', 'desc');

        if (isset(request()->q)) {
            $query->where('id_inquiry', request()->q);
        }

        $inquiry = $query->paginate(50);
        if ($inquiry === null) {
            $inquiry = collect();
        }

        return view('inquiry.index', compact('inquiry', 'data', 'followup_types', 'sales_person'));
    }

    // public function getdata()
    // {
    //     // Show colorful status badge
    //     function status_controller($status_id)
    //     {
    //         switch ($status_id) {
    //             case 'Open':
    //                 return '<span class="badge bg-warning text-dark">Open</span>';
    //             case 'In-Progress':
    //                 return '<span class="badge bg-primary">In-Progress</span>';
    //             case 'Completed':
    //                 return '<span class="badge bg-success">Completed</span>';
    //             case 'Cancelled':
    //                 return '<span class="badge bg-danger">Cancelled</span>';
    //             case 'Confirmed':
    //                 return '<span class="badge bg-info text-dark">Confirmed</span>';
    //             case 'Hold':
    //                 return '<span class="badge bg-secondary">Hold</span>';
    //             default:
    //                 return '<span class="badge bg-light text-dark">Unknown</span>';
    //         }
    //     }

    //     function progress_remarks($old_remarks, $id)
    //     {
    //         $my_inquiry = '';
    //         $my_fr_inquiry = '';

    //         $followup_remarks = followup_remark::with('createdBy')
    //             ->where('inquiry_id', $id)
    //             ->orderByDesc('id_followup_remarks')
    //             ->get();

    //         foreach ($followup_remarks as $fr) {
    //             $status_badge = status_controller($fr->followup_status);
    //             $followup_date = $fr->followup_date ? date('d-m-Y', strtotime($fr->followup_date)) : '-';
    //             $followup_status = '<span class="badge bg-light text-dark">Followup: ' . $followup_date . '</span>';

    //             $my_fr_inquiry .= '
    //             <div class="p-2 mb-2 border-start border-primary">
    //                 <strong class="text-dark">Follow-up Remarks:</strong>
    //                 <em class="text-muted">' . e($fr->remarks) . '</em>
    //                 <br><small class="text-secondary">~' . e($fr->createdBy->name) . ' on
    //                 <span class="badge bg-light text-dark">' . date('d-m-Y H:i', strtotime($fr->created_at)) . '</span></small>
    //                 ' . $status_badge . ' ' . $followup_status . '
    //             </div>';
    //         }

    //         $saved_remarks = remarks::with('createdBy')
    //             ->where('inquiry_id', $id)
    //             ->orderByDesc('id_remarks')
    //             ->get();

    //         foreach ($saved_remarks as $progress) {
    //             $status_badge = status_controller($progress->remarks_status);
    //             $followup_date = $progress->followup_date ? date('d-m-Y', strtotime($progress->followup_date)) : '-';
    //             $followup_status = '<span class="badge bg-light text-dark">Followup: ' . $followup_date . '</span>';

    //             $my_inquiry .= '
    //             <div class="p-2 mb-2 border-start border-success">
    //                 <strong class="text-dark">Progress Remarks:</strong>
    //                 <em class="text-muted">' . e($progress->remarks) . '</em>
    //                 <br><small class="text-secondary">~' . e($progress->created_by) . ' on
    //                 <span class="badge bg-light text-dark">' . date('d-m-Y H:i', strtotime($progress->created_on)) . '</span></small>
    //                 ' . $status_badge . ' ' . $followup_status . '
    //             </div>';
    //         }

    //         return strip_tags($old_remarks) . '<hr>' . $my_fr_inquiry;
    //     }

    //     function followup_system($followup_date)
    //     {
    //         if (!$followup_date) return '-';
    //         $date_str = date('d-m-y', strtotime($followup_date));
    //         $today = date('d-m-y');
    //         if ($date_str == $today) {
    //             return '<span class="badge bg-danger">Today: ' . $date_str . '</span>';
    //         } elseif (date('d', strtotime($followup_date)) == date('d', strtotime('-1 day'))) {
    //             return '<span class="badge bg-warning text-dark">Tomorrow: ' . $date_str . '</span>';
    //         } else {
    //             return '<span class="badge bg-success">Upcoming: ' . $date_str . '</span>';
    //         }
    //     }

    //     function services_sub_services($ids)
    //     {
    //         if (!is_array($ids)) return '-';
    //         $html = '';
    //         foreach ($ids as $value) {
    //             $parts = explode('/', $value);
    //             $service = other_service::find($parts[0]);
    //             $subs = explode(',', $parts[1]);
    //             if ($service) {
    //                 $html .= '<div><strong>' . e($service->service_name) . '</strong>: ';
    //                 foreach ($subs as $sid) {
    //                     $sub = other_service::find($sid);
    //                     if ($sub) {
    //                         $html .= '<span class="badge bg-success me-1 mb-1">' . e($sub->service_name) . '</span> ';
    //                     }
    //                 }
    //                 $html .= '</div>';
    //             }
    //         }
    //         return $html ?: '-';
    //     }

    //     // Build base query
    //     $inquiry = inquiry::select('inquiry.*', 'users.name as created_by_name')
    //         ->join('users', 'users.id', '=', 'inquiry.created_by', 'left')
    //         ->orderByDesc('id_inquiry');

    //     $followup_past = intval(request()->followup_past); // 0 or 1
    //     $followup_today = intval(request()->followup_today);
    //     $today = now()->toDateString();

    //     // If 'followup_past' is unchecked → remove those whose latest follow-up is in the past (before today)
    //     if ($followup_past != 1) {
    //         $inquiry->where(function ($q) use ($today) {
    //             $q->whereDoesntHave('latestFollowup')
    //                 ->orWhereHas('latestFollowup', function ($q2) use ($today) {
    //                     $q2->whereDate('followup_date', '>=', $today);  // today or future
    //                 });
    //         });
    //     }

    //     // If 'followup_today' is unchecked → remove those whose latest follow-up is exactly today
    //     if ($followup_today != 1) {
    //         $inquiry->where(function ($q) use ($today) {
    //             $q->whereDoesntHave('latestFollowup')
    //                 ->orWhereHas('latestFollowup', function ($q2) use ($today) {
    //                     $q2->whereDate('followup_date', '!=', $today);  // past or future
    //                 });
    //         });
    //     }

    //     // for custom filter
    //     if (request()->has('inquiry_type') && request()->inquiry_type != '') {
    //         $inquiry->where('inquiry.inquiry_type', request()->inquiry_type);
    //     }

    //     if (request()->has('sales_person') && request()->sales_person != '') {
    //         $inquiry->where('inquiry.saleperson', request()->sales_person);
    //     }

    //     if (request()->has('status') && request()->status != '') {
    //         $inquiry->where('inquiry.status', request()->status);
    //     }

    //     if (request()->has('id_inquiry') && request()->id_inquiry != '') {
    //         $inquiry->where('inquiry.id_inquiry', request()->id_inquiry);
    //     }

    //     if (request()->has('date_from') && request()->date_from != '') {
    //         $inquiry->whereDate('inquiry.created_at', '>=', request()->date_from);
    //     }
    //     if (request()->has('date_to') && request()->date_to != '') {
    //         $inquiry->whereDate('inquiry.created_at', '<=', request()->date_to);
    //     }

    //     return DataTables::of($inquiry)
    //         ->addIndexColumn() // optional: will give index when not using custom
    //         // ->addColumn('sno', function () use (&$count) {
    //         //     return ++$count;
    //         // })
    //         ->addColumn('customer_name', function ($inquiry) {
    //             return $inquiry->customer ? $inquiry->customer->customer_name : '-';
    //         })
    //         ->filterColumn('customer_name', function ($query, $keyword) {
    //             $query->whereHas('customer', function ($q) use ($keyword) {
    //                 $q->where('customer_name', 'like', "%{$keyword}%");
    //             });
    //         })
    //         ->addColumn('customer_cell', function ($inquiry) {
    //             return $inquiry->customer ? $inquiry->customer->customer_cell : '-';
    //         })
    //         ->filterColumn('customer_cell', function ($query, $keyword) {
    //             $query->whereHas('customer', function ($q) use ($keyword) {
    //                 $q->where('customer_cell', 'like', "%{$keyword}%");
    //             });
    //         })
    //         // ->addColumn('customer_info', function ($inquiry) {
    //         //     if ($inquiry->customer) {
    //         //         return $inquiry->customer->customer_name . ' | ' . $inquiry->customer->customer_cell;
    //         //     }
    //         //     return '-';
    //         // })

    //         // ->filterColumn('customer_info', function ($query, $keyword) {
    //         //     $query->whereHas('customer', function ($q) use ($keyword) {
    //         //         $q->where('customer_name', 'like', "%{$keyword}%")
    //         //             ->orWhere('customer_cell', 'like', "%{$keyword}%");
    //         //     });
    //         // })

    //         ->editColumn('initial_remarks', function ($inquiry) {
    //             return strip_tags($inquiry->remarks);
    //         })
    //         ->editColumn('services', function ($inquiry) {
    //             $decode_services = json_decode($inquiry->services_sub_services);
    //             return $decode_services ? services_sub_services($decode_services) : '-';
    //         })
    //         ->filterColumn('inquiry_type', function ($query, $keyword) {
    //             $query->whereHas('inquiry_type', fn($q) => $q->where('type_name', 'like', "%{$keyword}%"));
    //         })

    //         ->filterColumn('saleperson', function ($query, $keyword) {
    //             $query->whereHas('salesPerson', fn($q) => $q->where('name', 'like', "%{$keyword}%"));
    //         })
    //         ->editColumn('inquiry_type', function ($inquiry) {
    //             $Inquiry_type = inquirytypes::find($inquiry->inquiry_type);
    //             return $Inquiry_type ? $Inquiry_type->type_name : '-';
    //         })
    //         ->editColumn('contact_1', function ($inquiry) {
    //             return $inquiry->customer ? $inquiry->customer->customer_cell : '-';
    //         })
    //         ->editColumn('saleperson', function ($inquiry) {
    //             if ($inquiry->saleperson == 'un_assign') {
    //                 return 'Un Assigned';
    //             }
    //             $sale_person = User::find($inquiry->saleperson);
    //             return $sale_person ? $sale_person->name : '-';
    //         })
    //         ->editColumn('status', function ($inquiry) {
    //             return status_controller($inquiry->status);
    //         })
    //         ->editColumn('sales_reference', function ($inquiry) {
    //             $sales_reference = sales_reference::find($inquiry->sales_reference);
    //             return $sales_reference ? $sales_reference->type_name : '-';
    //         })
    //         ->editColumn('remarks', function ($inquiry) {
    //             return progress_remarks($inquiry->remarks, $inquiry->id_inquiry);
    //         })
    //         ->editColumn('email', function ($inquiry) {
    //             return $inquiry->customer ? $inquiry->customer->customer_email : '-';
    //         })
    //         ->editColumn('travel_date', function ($inquiry) {
    //             return $inquiry->travel_date ? date('d-m-y', strtotime($inquiry->travel_date)) : '-';
    //         })
    //         ->editColumn('followup_date', function ($inquiry) {
    //             return followup_system($inquiry->followup_date);
    //         })
    //         ->editColumn('created_at', function ($inquiry) {
    //             return $inquiry->created_at ? date('d-m-y H:i', strtotime($inquiry->created_at)) : '-';
    //         })
    //         ->editColumn('created_by', function ($inquiry) {
    //             return $inquiry->created_by_name ?  $inquiry->created_by_name : '-';
    //         })
    //         ->editColumn('contact_2', function ($inquiry) {
    //             return $inquiry->customer ? $inquiry->customer->customer_phone2 : '-';
    //         })
    //         ->addColumn('action', function ($inquiry) {
    //             $html = '<div class="d-flex justify-content-center gap-1">';

    //             if (Auth::user() && Auth::user()->can('Inquiry edit')) {
    //                 $html .= '<a href="' . url('/edit_inquiry/' . $inquiry->id_inquiry) . '"
    //                 class="btn btn-sm btn-primary"
    //                 data-bs-toggle="tooltip" title="Edit">
    //                 <i class="fa fa-pen"></i>
    //               </a>';
    //             }

    //             $html .= '<button type="button"
    //             class="btn btn-sm btn-secondary view-followup"
    //             data-id="' . $inquiry->id_inquiry . '"
    //             data-bs-toggle="tooltip" title="Follow-up">
    //             <i class="fa fa-comments"></i>
    //           </button>';

    //             if (Auth::user() && Auth::user()->can('Inquiry Progress View')) {
    //                 $html .= '<button type="button"
    //                 class="btn btn-sm btn-info view-progress"
    //                 data-id="' . $inquiry->id_inquiry . '"
    //                 data-bs-toggle="tooltip" title="Progress">
    //                 <i class="fa fa-tasks"></i>
    //               </button>';
    //             }

    //             if (Auth::user() && Auth::user()->can('Inquiry delete')) {
    //                 $html .= '<a href="' . url('/inquiry/delete/' . $inquiry->id_inquiry) . '"
    //                 class="btn btn-sm btn-danger"
    //                 onclick="return confirm(\'Are you sure you want to delete this inquiry?\')"
    //                 data-bs-toggle="tooltip" title="Delete">
    //                 <i class="fa fa-trash"></i>
    //               </a>';
    //             }

    //             $html .= '</div>';
    //             return $html;
    //         })

    //         ->addColumn('followup_status_badge', function ($inquiry) {
    //             $last_followup = \App\followup_remark::where('inquiry_id', $inquiry->id_inquiry)
    //                 ->orderByDesc('id_followup_remarks')
    //                 ->value('followup_date');

    //             if ($last_followup) {
    //                 $follow_date = date('Y-m-d', strtotime($last_followup));
    //                 $today = date('Y-m-d');

    //                 if ($follow_date == $today) {
    //                     return '<span class="badge bg-success">Today</span>';
    //                 } elseif ($follow_date < $today) {
    //                     return '<span class="badge bg-warning text-dark">Past</span>';
    //                 } else {
    //                     return '<span class="badge bg-primary">Upcoming</span>';
    //                 }
    //             } else {
    //                 return '<span class="badge bg-secondary">No Followup</span>';
    //             }
    //         })

    //         // for followup check box
    //         ->addColumn('row_class', function ($inquiry) use ($followup_past, $followup_today) {
    //             $last_followup = \App\followup_remark::where('inquiry_id', $inquiry->id_inquiry)
    //                 ->orderByDesc('id_followup_remarks')
    //                 ->value('followup_date');

    //             $today = date('Y-m-d');
    //             if ($last_followup) {
    //                 $follow_date = date('Y-m-d', strtotime($last_followup));

    //                 if ($follow_date < $today) {
    //                     return $followup_past ? 'followup-past' : 'hidden-row';
    //                 } elseif ($follow_date == $today) {
    //                     return $followup_today ? 'followup-today' : 'hidden-row';
    //                 } else {
    //                     return 'followup-future'; // tomorrow / future
    //                 }
    //             } else {
    //                 return 'no-followup';
    //             }
    //         })

    //         ->rawColumns(['action', 'services', 'initial_remarks', 'customer', 'inquiry_type', 'status', 'remarks', 'email', 'followup_date'])
    //         ->make(true);
    // }
    public function getdata()
    {
        try {
            $servicesMap = Cache::remember('all_services_map', 300, function () {
                return other_service::pluck('service_name', 'id_other_services')->toArray();
            });

            $query = inquiry::with(['customer', 'inquiryType', 'salesPerson', 'salesReference', 'createdBy', 'latestFollowup'])
                ->select(['inquiry.*']);

            if (!request()->has('order')) {
                $query->orderByDesc('inquiry.id_inquiry');
            }

            // Filters
            foreach (
                [
                    'inquiry_type' => 'inquiry.inquiry_type',
                    'sales_person' => 'inquiry.saleperson',
                    'status' => 'inquiry.status',
                ] as $param => $column
            ) {
                if ($v = request($param)) {
                    $query->where($column, $v);
                }
            }

            // Comma-separated ID search (e.g. "01, 02, 30" or single "12")
            if ($rawIdInput = request('id_inquiry')) {
                $rawTokens = preg_split('/[,]+/', (string) $rawIdInput);
                $numericIds = [];
                $stringTokens = [];
                foreach ($rawTokens as $tok) {
                    $tok = trim($tok);
                    if ($tok === '') continue;
                    $clean = ltrim($tok, '#');
                    if (is_numeric($clean)) {
                        $numericIds[] = (int) $clean;
                    } else {
                        $stringTokens[] = $tok;
                    }
                }
                $numericIds = array_values(array_unique($numericIds));
                if (!empty($numericIds) && empty($stringTokens)) {
                    if (count($numericIds) === 1) {
                        $first = $numericIds[0];
                        $query->where(function ($q) use ($first, $rawIdInput) {
                            $q->where('inquiry.id_inquiry', $first)
                              ->orWhere('inquiry.id_inquiry', 'like', "%{$rawIdInput}%");
                        });
                    } else {
                        $query->whereIn('inquiry.id_inquiry', $numericIds);
                    }
                } elseif (!empty($numericIds) || !empty($stringTokens)) {
                    $query->where(function ($q) use ($numericIds, $stringTokens) {
                        if (!empty($numericIds)) {
                            $q->whereIn('inquiry.id_inquiry', $numericIds);
                        }
                        foreach ($stringTokens as $st) {
                            $q->orWhere('inquiry.id_inquiry', 'like', "%{$st}%");
                        }
                    });
                }
            }

            if ($from = request('date_from')) {
                $query->whereDate('inquiry.created_at', '>=', $from);
            }
            if ($to = request('date_to')) {
                $query->whereDate('inquiry.created_at', '<=', $to);
            }

            if ($v = request('sales_reference')) {
                $query->where('inquiry.sales_reference', $v);
            }
            if ($v = request('customer_name')) {
                $query->where('customers.customer_name', 'like', "%{$v}%");
            }
            if ($v = request('customer_cell')) {
                $query->where('customers.customer_cell', 'like', "%{$v}%");
            }
            if ($v = request('customer_email')) {
                $query->where('customers.customer_email', 'like', "%{$v}%");
            }

            $today = now()->toDateString();
            $fudFilter = request('fud_filter');
            if ($fudFilter) {
                $query->whereIn('id_inquiry', function ($sub) use ($fudFilter, $today) {
                    $sub->select('inquiry_id')
                        ->from('followup_remarks')
                        ->whereIn('id_followup_remarks', function ($sub2) {
                            $sub2->selectRaw('MAX(id_followup_remarks)')
                                ->from('followup_remarks')
                                ->groupBy('inquiry_id');
                        });
                        
                    if ($fudFilter === 'today') {
                        $sub->whereDate('followup_date', '=', $today);
                    } elseif ($fudFilter === 'upcoming') {
                        $sub->whereDate('followup_date', '>', $today);
                    } elseif ($fudFilter === 'overdue') {
                        $sub->whereDate('followup_date', '<', $today);
                    }
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('inquiry_type', function ($row) {
                    return $row->inquiry_type_name ?? '';
                })
                // ✅ Per-column filters for related models
                ->filterColumn('customer_name', function ($query, $keyword) {
                    $query->whereHas('customer', function($q) use ($keyword) {
                        $q->where('customer_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('customer_cell', function ($query, $keyword) {
                    $query->whereHas('customer', function($q) use ($keyword) {
                        $q->where('customer_cell', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('inquiry_type_name', function ($query, $keyword) {
                    $query->whereHas('inquiryType', function($q) use ($keyword) {
                        $q->where('type_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('salesperson_name', function ($query, $keyword) {
                    $query->whereHas('salesPerson', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('sales_ref_name', function ($query, $keyword) {
                    $query->whereHas('salesReference', function($q) use ($keyword) {
                        $q->where('type_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('travel_date', function ($query, $keyword) {
                    $date = date('Y-m-d', strtotime($keyword));
                    if ($date != '1970-01-01') {
                        $query->whereDate('inquiry.travel_date', $date);
                    } else {
                        $query->where('inquiry.travel_date', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('followup_date', function ($query, $keyword) {
                    $date = date('Y-m-d', strtotime($keyword));
                    if ($date != '1970-01-01') {
                        $query->whereHas('latestFollowup', function ($q) use ($date) {
                            $q->whereDate('followup_date', $date);
                        });
                    }
                })
                // ✅ Per-column ordering for related models
                ->orderColumn('customer_name', function ($query, $order) {
                    $query->orderBy(\App\customer::select('customer_name')->whereColumn('customers.id_customers', 'inquiry.customer_id')->limit(1), $order);
                })
                ->orderColumn('customer_cell', function ($query, $order) {
                    $query->orderBy(\App\customer::select('customer_cell')->whereColumn('customers.id_customers', 'inquiry.customer_id')->limit(1), $order);
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
                ->orderColumn('travel_date', function ($query, $order) {
                    $query->orderBy('inquiry.travel_date', $order);
                })
                ->orderColumn('followup_date', function ($query, $order) {
                    $query->orderBy(\App\followup_remark::select('followup_date')->whereColumn('followup_remarks.inquiry_id', 'inquiry.id_inquiry')->orderByDesc('id_followup_remarks')->limit(1), $order);
                })
                ->orderColumn('created_by', function ($query, $order) {
                    $query->orderBy(\App\Models\User::select('name')->whereColumn('users.id', 'inquiry.created_by')->limit(1), $order);
                })
                // ✅ Optional: search status (e.g., Open, Completed) text badge
                ->filterColumn('status', function ($query, $keyword) {
                    $query->where('status', 'like', "%{$keyword}%");
                })
                // ✅ Global search fallback for other fields
                ->filter(function ($query) {
                    if ($keyword = request('search.value')) {
                        $query->where(function ($q) use ($keyword) {
                            if (str_contains($keyword, ',')) {
                                $rawTokens = preg_split('/[,]+/', (string) $keyword);
                                $numIds = [];
                                foreach ($rawTokens as $tok) {
                                    $c = trim(ltrim(trim($tok), '#'));
                                    if ($c !== '' && is_numeric($c)) {
                                        $numIds[] = (int) $c;
                                    }
                                }
                                if (!empty($numIds)) {
                                    $q->whereIn('inquiry.id_inquiry', $numIds);
                                    return;
                                }
                            }
                            $q
                                ->where('inquiry.id_inquiry', 'like', "%$keyword%")
                                ->orWhere('inquiry.status', 'like', "%$keyword%")
                                ->orWhereHas('customer', function ($cq) use ($keyword) {
                                    $cq->where('customer_name', 'like', "%$keyword%")
                                        ->orWhere('customer_cell', 'like', "%$keyword%");
                                })
                                ->orWhereHas('salesPerson', function ($sq) use ($keyword) {
                                    $sq->where('name', 'like', "%$keyword%");
                                })
                                ->orWhereHas('createdBy', function ($cbq) use ($keyword) {
                                    $cbq->where('name', 'like', "%$keyword%");
                                });
                        });
                    }
                })
                // Columns
                ->addColumn('checkbox', function ($i) {
                    return '<input type="checkbox" name="inquiry_ids[]" value="' . $i->id_inquiry . '" class="form-check-input inquiry-checkbox">';
                })
                ->editColumn('customer_name', fn($i) => $i->customer->customer_name ?? '-')
                ->editColumn('customer_cell', function ($i) {
                    if (!empty($i->customer->customer_cell)) {
                        $raw = trim($i->customer->customer_cell);
                        if (str_starts_with($raw, '0')) {
                            $whatsAppNumber = '+92' . substr($raw, 1);
                        } elseif (str_starts_with($raw, '+91')) {
                            $whatsAppNumber = $raw;
                        } else {
                            $whatsAppNumber = str_starts_with($raw, '+') ? $raw : '+' . $raw;
                        }
                        return '<a href="https://wa.me/' . str_replace(['+', ' '], '', $whatsAppNumber) . '" target="_blank" class="text-success text-decoration-none">
                                    <i class="fab fa-whatsapp me-1"></i>' . htmlspecialchars($raw) . '
                                </a>';
                    }
                    return '-';
                })
                ->editColumn('initial_remarks', fn($i) => strip_tags($i->remarks))
                ->editColumn('services', fn($i) => $this->servicesSubServices($i->services_sub_services, $servicesMap))
                ->editColumn('remarks', fn($i) => $this->progressRemarks($i))
                ->addColumn('inquiry_type_name', fn($i) => $i->inquiryType->type_name ?? '-')
                ->addColumn('salesperson_name', fn($i) => $i->saleperson === 'un_assign' ? 'Un Assigned' : ($i->salesPerson->name ?? '-'))
                ->addColumn('sales_ref_name', fn($i) => $i->salesReference->type_name ?? '-')
                ->editColumn('status', fn($i) => $this->statusController($i->status))
                ->editColumn('sales_reference', fn($i) => $i->salesReference->type_name ?? '-')
                ->editColumn('email', fn($i) => $i->customer->customer_email ?? '-')
                ->editColumn('travel_date', fn($i) => $i->travel_date ? date('d-m-y', strtotime($i->travel_date)) : '-')
                ->addColumn('followup_date', fn($i) => $this->followupSystem($i->latestFollowup->followup_date ?? null))
                ->addColumn('created_by', fn($i) => $i->createdBy->name ?? '-')
                ->editColumn('created_at', fn($i) => $i->created_at ? date('d-m-y H:i', strtotime($i->created_at)) : '-')
                ->editColumn('contact_2', fn($i) => $i->customer->customer_phone2 ?? '-')
                ->addColumn('action', fn($i) => $this->actionButtons($i))
                ->addColumn('progress_remarks_html', fn($i) => $this->progressRemarksOnly($i))
                ->addColumn('followup_status_badge', fn($i) => $this->followupStatusBadge($i))
                ->addColumn('row_class', fn($i) => $this->rowClass($i, request('followup_past', 0), request('followup_today', 0)))
                ->rawColumns([
                    'checkbox',
                    'customer_cell',
                    'action',
                    'services',
                    'status',
                    'remarks',
                    'progress_remarks_html',
                    'followup_date',
                    'followup_status_badge',
                ])
                ->make(true);
        } catch (\Throwable $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json([
                'draw' => request('draw', 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Helper methods
    // Helper methods

    public function bulkUpdateSalesPerson(Request $request)
    {
        $request->validate([
            'inquiry_ids' => 'required|array',
            'inquiry_ids.*' => 'exists:inquiry,id_inquiry',
            'sales_person_id' => 'required',
        ]);

        $count = inquiry::whereIn('id_inquiry', $request->inquiry_ids)
            ->update(['saleperson' => $request->sales_person_id]);

        return response()->json([
            'success' => true,
            'message' => $count . ' inquiries assigned successfully.'
        ]);
    }

    private function statusController($status)
    {
        $badges = [
            'Open' => 'bg-gradient-secondary shadow-sm',
            'In-Progress' => 'bg-gradient-primary shadow-sm',
            'Completed' => 'bg-gradient-success shadow-sm',
            'Quotation' => 'bg-gradient-warning text-dark shadow-sm',
            'Cancelled' => 'bg-gradient-danger shadow-sm',
            'Confirmed' => 'bg-gradient-success shadow-sm',
            'Hold' => 'bg-gradient-secondary shadow-sm'
        ];

        $class = $badges[$status] ?? 'bg-light text-dark border';
        return '<span class="badge rounded-pill ' . $class . '">' . ($status ?: 'Unknown') . '</span>';
    }

    private function progressRemarks(Inquiry $inquiry)
    {
        // start with the initial remarks (attribute)
        $html = "<div style='word-wrap: break-word; white-space: normal; overflow-wrap: break-word;'>" . strip_tags($inquiry->remarks) . "</div><hr>";

        // 1) FOLLOW‑UP REMARKS
        $followups = $inquiry
            ->followups()  // explicitly call the relation
            ->latest('created_at')
            ->take(5)
            ->with('createdBy:id,name')
            ->get();

        foreach ($followups as $fr) {
            $date = $fr->followup_date
                ? date('d-m-Y', strtotime($fr->followup_date))
                : '-';
            $html .= "
            <div class='p-2 mb-2 border-start border-primary' style='word-wrap: break-word; white-space: normal; overflow-wrap: break-word;'>
              <strong>Follow-up Remarks:</strong> 
              <em>" . e($fr->remarks) . '</em><br>
              <small>~' . e($fr->createdBy->name ?? '') . " on 
                <span class='badge bg-light text-dark'>"
                . date('d-m-Y H:i', strtotime($fr->created_at))
                . '</span>
              </small>
              ' . $this->statusController($fr->followup_status) . "
              <span class='badge bg-light text-dark'>Followup: {$date}</span>
            </div>
        ";
        }

        // 2) PROGRESS REMARKS
        $progressList = $inquiry
            ->remarks()  // explicitly call the relation
            ->latest('id_remarks')
            ->take(5)
            ->with('createdBy:id,name')
            ->get();

        foreach ($progressList as $p) {
            $date = $p->followup_date
                ? date('d-m-Y', strtotime($p->followup_date))
                : '-';
            $html .= "
            <div class='p-2 mb-2 border-start border-success' style='word-wrap: break-word; white-space: normal; overflow-wrap: break-word;'>
              <strong>Progress Remarks:</strong> 
              <em>" . e($p->remarks) . '</em><br>
              <small>~' . e($p->createdBy->name ?? '') . " on 
                <span class='badge bg-light text-dark'>"
                . date('d-m-Y H:i', strtotime($p->created_at))
                . '</span>
              </small>
              ' . $this->statusController($p->remarks_status) . "
              <span class='badge bg-light text-dark'>Followup: {$date}</span>
            </div>
        ";
        }

        return $html;
    }

    private function progressRemarksOnly(Inquiry $inquiry)
    {
        $html = '';
        $progressList = $inquiry
            ->remarks()  // explicitly call the relation
            ->latest('id_remarks')
            ->take(5)
            ->with('createdBy:id,name')
            ->get();

        foreach ($progressList as $p) {
            $date = $p->followup_date
                ? date('d-m-Y', strtotime($p->followup_date))
                : '-';
            $html .= "
            <div class='p-2 mb-2 border-start border-success shadow-sm bg-white rounded' style='word-wrap: break-word; white-space: normal; overflow-wrap: break-word;'>
              <div class='d-flex justify-content-between align-items-center mb-1'>
                  <strong class='text-dark'><i class='fa fa-tasks text-success me-1'></i> Progress Remarks:</strong>
                  <span class='badge bg-light text-dark border'><i class='fa fa-calendar-alt me-1'></i> Followup: {$date}</span>
              </div>
              <em class='text-muted d-block mb-2'>" . e($p->remarks) . "</em>
              <div class='d-flex justify-content-between align-items-center'>
                  <small class='text-secondary'><i class='fa fa-user me-1'></i> " . e($p->createdBy->name ?? '') . "</small>
                  <small class='text-secondary'><i class='fa fa-clock me-1'></i> " . date('d-m-Y H:i', strtotime($p->created_at)) . "</small>
              </div>
            </div>
        ";
        }
        return $html;
    }

    /**
     * Turn the services_sub_services JSON string into badges.
     * @param  string  $servicesJson
     * @param  array   $servicesMap   [ id => name ]
     * @return string
     */
    private function servicesSubServices($servicesJson, array $servicesMap)
    {
        // decode to array
        $decoded = json_decode($servicesJson, true);
        if (!is_array($decoded)) {
            return '-';
        }

        $html = '';
        foreach ($decoded as $value) {
            // Expecting "serviceId/subId,subId"
            if (!is_string($value) || !str_contains($value, '/')) {
                continue;
            }
            [$svcId, $subList] = explode('/', $value, 2);
            $svcName = $servicesMap[$svcId] ?? null;
            if (!$svcName) {
                continue;
            }

            $html .= '<div><strong>' . e($svcName) . ':</strong> ';
            foreach (explode(',', $subList) as $sid) {
                if (isset($servicesMap[$sid])) {
                    $html .= "<span class='badge bg-success me-1 mb-1'>"
                        . e($servicesMap[$sid])
                        . '</span> ';
                }
            }
            $html .= '</div>';
        }

        return $html ?: '-';
    }

    private function followupSystem($followup_date)
    {
        if (!$followup_date || strtotime($followup_date) === false) {
            return '<span class="badge bg-secondary" style="border-radius: 4px; padding: 4px 8px;">-</span>';
        }

        $date = date('d-m-Y', strtotime($followup_date));
        $today = date('Y-m-d');
        $followDate = date('Y-m-d', strtotime($followup_date));

        if ($followDate === $today) {
            return '<span class="badge" style="background-color: #d9534f; color: white; border-radius: 4px; padding: 4px 10px; font-weight: bold;">TODAY: ' . $date . '</span>';
        }

        if ($followDate < $today) {
            return '<span class="badge" style="background-color: #f0ad4e; color: white; border-radius: 4px; padding: 4px 10px; font-weight: bold;">OVERDUE: ' . $date . '</span>';
        }

        return '<span class="badge" style="background-color: #5cb85c; color: white; border-radius: 4px; padding: 4px 10px; font-weight: bold;">UPCOMING: ' . $date . '</span>';
    }

    private function actionButtons($inquiry)
    {
        $html = '<div class="d-flex justify-content-center gap-1">';

        if (Auth::user()?->can('Inquiry edit')) {
            $html .= '<a href="' . url('/edit_inquiry/' . $inquiry->id_inquiry) . '" 
            class="btn btn-sm btn-primary p-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;" title="Edit">
            <i class="fa fa-pen" style="font-size: 12px;"></i></a>';
        }

        $html .= '<button type="button" class="btn btn-sm btn-dark p-2 view-followup" 
            data-id="' . $inquiry->id_inquiry . '" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;" title="Follow-up">
            <i class="fa fa-comments" style="font-size: 12px;"></i></button>';

        if (Auth::user()?->can('Inquiry Progress View')) {
            $html .= '<button type="button" class="btn btn-sm btn-info p-2 view-progress" 
                data-id="' . $inquiry->id_inquiry . '" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;" title="Progress">
                <i class="fa fa-tasks" style="font-size: 12px;"></i></button>';
        }

        if (Auth::user()?->can('Inquiry edit')) {
            $currentSP = $inquiry->saleperson === 'un_assign' ? '' : $inquiry->saleperson;
            $html .= '<button type="button" class="btn btn-sm btn-warning p-2 change-salesperson" 
                data-id="' . $inquiry->id_inquiry . '" 
                data-current-sp="' . $currentSP . '" 
                style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"
                title="Change Sales Person">
                <i class="fa fa-user-edit" style="font-size: 12px;"></i></button>';
        }

        if (Auth::user()?->can('Inquiry delete')) {
            // Delete is sent as a POST form with CSRF token (not a plain GET link)
            $html .= '<form method="POST" action="' . url('/inquiry/delete/' . $inquiry->id_inquiry) . '" class="m-0"
            onsubmit="return confirm(\'Are you sure?\')">
            <input type="hidden" name="_token" value="' . csrf_token() . '">
            <button type="submit" class="btn btn-sm btn-danger p-2 mb-0"
            style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;" title="Delete">
            <i class="fa fa-trash" style="font-size: 12px;"></i></button></form>';
        }

        return $html . '</div>';
    }

    private function followupStatusBadge($inquiry)
    {
        if (!$inquiry->latestFollowup) {
            return '<span class="badge rounded-pill bg-gradient-secondary shadow-sm">No Followup</span>';
        }

        $date = $inquiry->latestFollowup->followup_date;
        if (!$date) {
            return '<span class="badge rounded-pill bg-gradient-secondary shadow-sm">No Followup</span>';
        }
        $followDate = date('Y-m-d', strtotime($date));
        $today = date('Y-m-d');

        if ($followDate === $today)
            return '<span class="badge rounded-pill bg-gradient-warning text-dark shadow-sm">Today</span>';
        if ($followDate < $today)
            return '<span class="badge rounded-pill bg-gradient-danger shadow-sm">Past</span>';

        return '<span class="badge rounded-pill bg-gradient-success shadow-sm">Upcoming</span>';
    }

    private function rowClass($inquiry, $past, $today)
    {
        if (!$inquiry->latestFollowup)
            return 'row-no-followup';

        $date = $inquiry->latestFollowup->followup_date;
        if (!$date) {
            return 'row-no-followup';
        }
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

    // <a class="btn btn-sm btn-info" href="'.url('customerBrands/'.$inquiry->id_inquiry).'"><i class="icon-bag fa-fw"></i> Customer Brands</a>
    // <a class="btn btn-sm btn-danger" href="'.url('delete_customer/'.$inquiry->id_inquiry).'"><i class="fa fa-trash"></i> Delete</a>

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $inquiry_types = inquirytypes::all();
        $sales_reference = sales_reference::all();
        $customers = customer::all();
        $sales_person = User::get();
        // Removed heavy queries for countries/cities to prevent crash
        $campaigns = \App\campaign::all();
        $services = other_service::where(function($q) {
            $q->whereNull('parent_id')->orWhere('parent_id', 0)->orWhere('parent_id', '');
        })->where('status', 'Active')->get();

        $get_role_id = Auth::user()->role_id;
        $get_per_of_assign_others = role_permission::where('role_id', $get_role_id)->where('menu_id', 101)->first();
        $get_per_of_unassign_inquiry = role_permission::where('role_id', $get_role_id)->where('menu_id', 100)->first();
        $get_permission_data = [
            'assign_others' => $get_per_of_assign_others ? 'true' : 'false',
            'unassign_inquiry' => $get_per_of_unassign_inquiry ? 'true' : 'false',
        ];
        // dd($get_permission_data);
        $sale_persons = \App\User::select('users.name', 'users.id')->get()->toArray();
        // dd( $sale_persons);
        $users = User::all();
        foreach ($users as $key => $value) {
            $user_role_id = $value->role_id;
            $all_roles_id[] = [$user_role_id, $value->id];
        }
        // dd($all_roles_id);
        $final_user_ids = [];
        foreach ($all_roles_id as $key => $value) {
            $get_roles_permission = role_permission::where('role_id', $value[0])->where('menu_id', 96)->first();
            if ($get_roles_permission) {
                $final_permission[] = $get_roles_permission;
                // dd($value);
                $final_user_ids[] = $value[1];
            }
        }
        // dd($final_user_ids);
        // $sale_persons = [];
        // $uniq_user_id = array_unique($final_user_ids);
        // if ($get_permission_data['assign_others'] == 'true') {
        //     $sale_persons = User::whereIn('id', $uniq_user_id)->get();
        // } else {
        //     $sale_persons = User::where('id', auth()->user()->id)->get();
        // }

        // dd($sale_persons);
        return view('inquiry.create', compact('inquiry_types', 'get_permission_data', 'sales_person', 'sales_reference', 'customers', 'services', 'sale_persons', 'campaigns'));

        //    dd($sale_persons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //    dd($request);
        $searched_customer_id = $request->searched_customer_id;
        //
        $service_name = other_service::where('id_other_services', $request['services'][0])->first();
        //        dd($service_name);exit;
        if (empty($searched_customer_id)) {
            if ($request->sp_assign_check == 'on') {
                $this->validate($request, [
                    'customer_name' => 'required',
                    'customer_cell' => 'required',
                    'inquiry_type' => 'required',
                    'services' => 'required',
                    'travel_date' => 'required',
                    'sale_person' => 'required',
                    'remarks' => ['required', function ($attribute, $value, $fail) {
                        if (trim(strip_tags($value)) === '') {
                            $fail('The ' . $attribute . ' field is required.');
                        }
                    }],
                ]);
            } else {
                $this->validate($request, [
                    'customer_name' => 'required',
                    'customer_cell' => 'required',
                    'inquiry_type' => 'required',
                    'services' => 'required',
                    'travel_date' => 'required',
                    'remarks' => ['required', function ($attribute, $value, $fail) {
                        if (trim(strip_tags($value)) === '') {
                            $fail('The ' . $attribute . ' field is required.');
                        }
                    }],
                ]);
            }

            $customer = new customer();
            $customer->customer_name = $request->customer_name;
            $customer->customer_type = $request->customer_type;
            $customer->customer_cell = $request->customer_cell;
            $customer->whatsapp_number = $request->customer_whatsapp;
            $customer->customer_address = $request->customer_address;
            $customer->city = $request->customer_city;
            // $customer->customer_phone2    = $request->customer_phone_2;
            $customer->customer_email = $request->customer_email;
            $customer->customer_reference = $request->customer_reference;
            $customer->customer_remarks = $request->customer_details;
            // $customer->sale_person        = $request->sp_assign_check == "on" ? "un_assign" : $request->sale_person;
            $customer->sale_person = Auth::user()->name;
            $customer->save();

            if ($customer) {
                $id_customer = $customer->id_customers;

                // dd($id_customer);
                $services_count = count($request->services);
                // dd($services_count);
                $data = $request->all();
                // dd($data);
                for ($i = 0; $i < $services_count; $i++) {
                    // dd($i);
                    $services[] = $data['services'][$i];
                    if ($i == 0) {
                        $sub = isset($data['sub_services']) ? implode(',', (array)$data['sub_services']) : '';
                        $sub_services[] = $services[$i] . '/' . $sub;
                    } else {
                        $sub = isset($data['sub_services' . $i]) ? implode(',', (array)$data['sub_services' . $i]) : '';
                        $sub_services[] = $services[$i] . '/' . $sub;
                    }
                }

                //                 dd(json_encode($sub_services));
                $inquiry = inquiry::forceCreate([
                    'customer_id' => $id_customer,
                    'campaign_id' => $request->campaign,
                    'inquiry_category' => $request->inquiry_category,
                    'services' => json_encode($request->services),
                    'sales_reference' => $request->sale_reference,
                    // 'buisness_id' => 1,
                    'saleperson' => $request->sp_assign_check == 'on' ? 'un_assign' : $request->sale_person,
                    'created_by' => Auth::user()->id,
                    'inquiry_type' => $request->inquiry_type,
                    'travel_date' => $request->travel_date,
                    'remarks' => $request->remarks,
                    'no_of_infants' => $request->no_of_infants ? $request->no_of_infants : 0,
                    'no_of_children' => $request->no_of_children ? $request->no_of_children : 0,
                    'no_of_adults' => $request->no_of_adults ? $request->no_of_adults : 0,
                    'services_sub_services' => json_encode($sub_services),
                    // 'hotel_id' => $request->hotel,
                    // 'airline_id' => $request->airline,
                    // 'saleperson' => auth()->user()->id,
                    // 'remarks' => $request->remarks,
                    // 'inquiry_type' => $request->inquiry_type,
                    // 'travel_date' => $request->travel_date,
                    'status' => 'Open',
                    // 'created_by' => 'Super Admin',
                    'created_at' => date('Y-m-d H:i:s'),
                    'escalation_time_for_assign' => date('Y-m-d H:i:s'),
                    'escalation_time_for_open' => date('Y-m-d H:i:s'),
                    'priority' => $request->priority,
                    'updated_at' => null,
                ]);
                $inquiry_id = $inquiry->id_inquiry;
                if ($inquiry) {
                    // Code Of Checking Services And SubServices -----Start---------

                    $department_query = departments::select(
                        'departments.id_departments',
                        'ds.id_department_services',
                        'dss.id_department_sub_services'
                    )
                        ->leftJoin('department_services as ds', 'ds.department_id', '=', 'departments.id_departments')
                        ->leftJoin('department_sub_services as dss', 'dss.departments_id', '=', 'departments.id_departments')
                        ->groupBy('departments.id_departments', 'ds.id_department_services', 'dss.id_department_sub_services')
                        ->get()
                        ->toArray();

                    $final_services_ids = null;
                    // dd($department_query);

                    // foreach ($department_query as $key => $value) {
                    //     $department_services = department_service::where('id_department_services', $value['id_department_services'])->first();
                    //     // dd(strlen($department_services->service_id));
                    //     // dd($department_services);
                    //     // dd(count($services));
                    //     // dd($services);
                    //     // dd(count($services));
                    //     if (count($services) == 1) {
                    //         // dd($services[0]);
                    //         if ($department_services->service_id !== null) {
                    //             if ($department_services->service_id == $services[0]) {
                    //                 $final_services_ids[] = $department_services->id_department_services;
                    //             }
                    //         }
                    //     } else {
                    //         if ($department_services->service_id !== null) {
                    //             // $key=$key-1;
                    //             // if ($department_services->service_id == $services[$key]) {
                    //             //     $final_services_ids[] = $department_services->id_department_services;
                    //             // }
                    //             if (isset($services[$key]) && $department_services->service_id == $services[$key]) {
                    //                 $final_services_ids[] = $department_services->id_department_services;
                    //             }
                    //         }
                    //     }
                    // }
                    $department_ids_final_unique = null;
                    if ($final_services_ids !== null) {
                        foreach ($final_services_ids as $key => $value) {
                            $department_sub_services = department_sub_service::where('id_department_sub_services', $value)->first();
                            $decode = json_decode($department_sub_services->sub_services_id);
                            // dd($key);
                            if (count($services) == 1) {
                                $inquiry_sub_services = $sub_services[0];
                            } else {
                                $inquiry_sub_services = $sub_services[$key];
                            }
                            $decode_inquiry_sub_services = explode('/', $inquiry_sub_services);
                            $intersect = array_intersect($decode_inquiry_sub_services, $decode);
                            $department_ids_final = null;
                            if ($intersect != null) {
                                $intersect_final[] = $intersect;
                                $department_ids_final[] = $department_sub_services->departments_id;
                            }
                        }
                        // dd($department_ids_final);
                        if ($department_ids_final != null) {
                            $department_ids_final_unique = array_unique($department_ids_final);
                        }
                    }

                    // Code Of Checking Services And SubServices -----End---------
                    // exit();
                    //                     dd($department_ids_final_unique);

                    $inquiry_types = inquirytypes::where('type_id', $request->inquiry_type)->first();
                    // Code of Inserting Team Jobs Start---------
                    $my_team_jobs = new my_team_job();
                    $my_team_jobs->inquiry_id = $inquiry->id_inquiry;
                    if (isset($request->sale_person) && $request->sale_person && $request->sale_person == auth()->user()->id) {
                        $my_team_jobs->taken_by = Auth::id();
                        $my_team_jobs->taken_by_status = 1;
                    }
                    $get_team_job_id = $my_team_jobs->id_my_team_jobs;
                    $my_team_jobs->department_ids = $service_name->service_name;
                    $my_team_jobs->save();
                    session()->flash('success', 'Inquiry Added Successfully!, Assigned to ' . $service_name->service_name . ' Department');
                    // sendNoti('New ' . $service_name->service_name . ' Team Un-Assigned Inquiry', auth()->user()->name, 'team_inquiry', auth()->user()->id, $service_name->id_other_services);
                    // Code of Inserting Team Jobs End---------
                }

                session()->flash('success', 'New Customer Added Successfully!');
                $inquiry_types = inquirytypes::where('type_id', $request->inquiry_type)->first();
                $sale_person = User::where('id', $request->sale_person)->first();
                if ($inquiry) {
                    //                    session()->flash('success', 'Inquiry Added Successfully!');

                    //                    sendNoti('New Inquiry Added By ' . auth()->user()->name, auth()->user()->name, 'create_inquiry');
                    // if (isset($request->sale_person) && $request->sale_person && $request->sale_person == auth()->user()->id) {
                    //     // dd($request->sale_person);
                    //     $my_job_create = new_my_job();
                    //     $my_job_create->inquiry_id = $inquiry->id_inquiry;
                    //     $my_job_create->user_id = auth()->user()->id;
                    //     $my_job_create->team_job_id = $get_team_job_id;
                    //     $my_job_create->assign_by = auth()->user()->id;
                    //     $my_job_create->save();

                    //     if ($request->sale_person == auth()->user()->id) {
                    //         session()->flash('success', 'Inquiry Added Successfully!, Assigned to: ' . auth()->user()->name . '');
                    //         sendNoti('New ' . $inquiry_types->type_name . ' Inquiry', auth()->user()->name, 'self_inquiry', auth()->user()->id);
                    //     }

                    //     return redirect('/create_quotation/' . Crypt::encrypt($inquiry_id));
                    // return redirect('/create_quotation/' . $inquiry_id);
                    // } else if (isset($request->sale_person) && $request->sale_person && $request->sale_person !== auth()->user()->id) {

                    $my_job_create = new my_job();
                    $my_job_create->inquiry_id = $inquiry->id_inquiry;
                    $my_job_create->user_id = $request->sale_person;
                    $my_job_create->team_job_id = $get_team_job_id;
                    $my_job_create->assign_by = auth()->user()->id;
                    $my_job_create->save();

                    session()->flash('success', 'Inquiry Added Successfully!, Assigned to: ' . $sale_person->name . '');
                    if ($request->sale_person !== auth()->user()->id) {
                        // sendNoti('New ' . $inquiry_types->type_name . ' Inquiry', $sale_person->name, 'self_inquiry', $request->sale_person);
                        return redirect('/inquiry');
                    }
                    // } else {
                    //     return redirect('/my_jobs');
                    // }
                } else {
                    Session::flash('error', 'An error has occurred please try again later.');
                    //            session()->flash('error', $th->getMessage());
                    return redirect()->back();
                }
            } else {
                Session::flash('error', 'An error has occurred please try again later.');
                //            session()->flash('error', $th->getMessage());
                return redirect()->back();
            }
        } else {
            // $this->validate($request, [
            //     'inquiry_type' => 'required',
            //     'sales_reference' => 'required',
            //     'travel_date' => 'required'
            // ]);
            $services_count = count($request->services);
            // dd($services_count);
            $data = $request->all();
            // dd($data);
            for ($i = 0; $i < $services_count; $i++) {
                // dd($i);
                $services[] = $data['services'][$i];
                if ($i == 0) {
                    $sub = isset($data['sub_services']) ? implode(',', (array)$data['sub_services']) : '';
                    $sub_services[] = $services[$i] . '/' . $sub;
                } else {
                    $sub = isset($data['sub_services' . $i]) ? implode(',', (array)$data['sub_services' . $i]) : '';
                    $sub_services[] = $services[$i] . '/' . $sub;
                }
            }
            $inquiry = inquiry::forceCreate([
                'customer_id' => $searched_customer_id,
                'campaign_id' => $request->campaign,
                // 'buisness_id' => 1,
                'inquiry_category' => $request->inquiry_category,
                'services' => json_encode($request->services),
                'sub_services' => json_encode($request->sub_services),
                'services_sub_services' => json_encode($sub_services),
                'sales_reference' => $request->sale_reference,
                'inquiry_type' => $request->inquiry_type,
                'travel_date' => $request->travel_date,
                'no_of_infants' => $request->no_of_infants ? $request->no_of_infants : 0,
                'no_of_children' => $request->no_of_children ? $request->no_of_children : 0,
                'no_of_adults' => $request->no_of_adults ? $request->no_of_adults : 0,
                'remarks' => $request->remarks,
                // 'hotel_id' => $request->hotel,
                // 'airline_id' => $request->airline,
                'saleperson' => $request->sp_assign_check == 'on' ? 'un_assign' : $request->sale_person,
                'created_by' => Auth::user()->id,
                // 'remarks' => $request->remarks,
                // 'inquiry_type' => $request->inquiry_type,
                // 'travel_date' => $request->travel_date,
                'status' => 'Open',
                // 'created_by' => 'Super Admin',
                'created_at' => date('Y-m-d H:i:s'),
                'escalation_time_for_assign' => date('Y-m-d H:i:s'),
                'escalation_time_for_open' => date('Y-m-d H:i:s'),
                'priority' => $request->priority,
                'updated_at' => null,
            ]);
            // dd($inquiry);
            $inquiry_id = $inquiry->id_inquiry;
            if ($inquiry) {
                // Code Of Checking Services And SubServices -----Start---------

                $department_query = departments::select(
                    'departments.id_departments',
                    'ds.id_department_services',
                    'dss.id_department_sub_services'
                )
                    ->leftJoin('department_services as ds', 'ds.department_id', '=', 'departments.id_departments')
                    ->leftJoin('department_sub_services as dss', 'dss.departments_id', '=', 'departments.id_departments')
                    ->groupBy('departments.id_departments', 'ds.id_department_services', 'dss.id_department_sub_services')
                    ->get()
                    ->toArray();

                $final_services_ids = null;
                // dd($department_query);

                // foreach ($department_query as $key => $value) {
                //     $department_services = department_service::where('id_department_services', $value['id_department_services'])->first();
                //     // dd(strlen($department_services->service_id));
                //     // dd($department_services);
                //     // dd(count($services));
                //     // dd($services);

                //     if (count($services) == 1) {
                //         // dd($services[0]);
                //         if ($department_services->service_id !== null) {
                //             if ($department_services->service_id == $services[0]) {
                //                 $final_services_ids[] = $department_services->id_department_services;
                //             }
                //         }
                //     } else {
                //         if ($department_services->service_id !== null) {

                //             if ($department_services->service_id == $services[$key]) {
                //                 $final_services_ids[] = $department_services->id_department_services;
                //             }
                //         }
                //     }
                // }
                // $department_ids_final_unique = null;
                // if ($final_services_ids != null) {
                //     foreach ($final_services_ids as $key => $value) {
                //         $department_sub_services = department_sub_service::where('id_department_sub_services', $value)->first();
                //         $decode = json_decode($department_sub_services->sub_services_id);
                //         // dd($key);
                //         if (count($services) == 1) {
                //             $inquiry_sub_services = $sub_services[0];
                //         } else {
                //             $inquiry_sub_services = $sub_services[$key];
                //         }
                //         $decode_inquiry_sub_services = explode('/', $inquiry_sub_services);
                //         $intersect =  array_intersect($decode_inquiry_sub_services, $decode);
                //         $department_ids_final = null;
                //         if ($intersect != null) {
                //             $intersect_final[] = $intersect;
                //             $department_ids_final[] = $department_sub_services->departments_id;
                //         }
                //     }
                //     // dd($department_ids_final);
                //     if ($department_ids_final != null) {
                //         $department_ids_final_unique = array_unique($department_ids_final);
                //     }
                // }

                // Code Of Checking Services And SubServices -----End---------
                // exit();
                //                 dd($department_ids_final_unique);

                $inquiry_types = inquirytypes::where('type_id', $request->inquiry_type)->first();
                // Code of Inserting Team Jobs Start---------
                $my_team_jobs = new my_team_job();
                $get_team_job_id = $my_team_jobs->id_my_team_jobs;
                $my_team_jobs->inquiry_id = $inquiry->id_inquiry;
                if (isset($request->sale_person) && $request->sale_person && $request->sale_person == auth()->user()->id) {
                    $my_team_jobs->taken_by = auth()->user()->id;
                    $my_team_jobs->taken_by_status = 1;
                }
                $my_team_jobs->department_ids = $service_name->service_name;
                $my_team_jobs->save();
                session()->flash('success', 'Inquiry Added Successfully!, Assigned to ' . $service_name->service_name . ' Department');
                // sendNoti('New ' . $service_name->service_name . ' Team Un-Assigned Inquiry', auth()->user()->name, 'team_inquiry', auth()->user()->id, $service_name->id_other_services);

                // Code of Inserting Team Jobs End---------
            }
            $inquiry_types = inquirytypes::where('type_id', $request->inquiry_type)->first();
            $sale_person = User::where('id', $request->sale_person)->first();
            if ($inquiry) {
                if (isset($request->sale_person) && $request->sale_person && $request->sale_person == auth()->user()->id) {
                    // dd($request->sale_person);
                    $my_job_create = new my_job();
                    $my_job_create->inquiry_id = $inquiry->id_inquiry;
                    $my_job_create->user_id = auth()->user()->id;
                    $my_job_create->team_job_id = $get_team_job_id;
                    $my_job_create->assign_by = auth()->user()->id;
                    $my_job_create->save();

                    if ($request->sale_person == auth()->user()->id) {
                        session()->flash('success', 'Inquiry Added Successfully!, Assigned to: ' . auth()->user()->name . '');
                        // sendNoti('New ' . $inquiry_types->type_name . ' Inquiry', auth()->user()->name, 'self_inquiry', auth()->user()->id);
                    }

                    return redirect('inquiry');
                } else if (isset($request->sale_person) && $request->sale_person && $request->sale_person !== auth()->user()->id) {
                    $my_job_create = new my_job();
                    $my_job_create->inquiry_id = $inquiry->id_inquiry;
                    $my_job_create->user_id = $request->sale_person;
                    $my_job_create->team_job_id = $get_team_job_id;
                    $my_job_create->assign_by = auth()->user()->id;
                    $my_job_create->save();

                    session()->flash('success', 'Inquiry Added Successfully!, Assigned to: ' . $sale_person->name . '');
                    if ($request->sale_person !== auth()->user()->id) {
                        // sendNoti('New ' . $inquiry_types->type_name . ' Inquiry', $sale_person->name, 'self_inquiry', $request->sale_person);
                        return redirect('/inquiry');
                    }
                } else {
                    return redirect('/my_teams_jobs');
                }
            } else {
                Session::flash('error', 'An error has occurred please try again later.');
                //            session()->flash('error', $th->getMessage());
                return redirect()->back();
            }
        }
        // dd($request);
        // dd($inquiry);
        return redirect('inquiry');
    }

    public function edit($id)
    {
        $inquiry = Inquiry::with(['customer', 'salesReference', 'salesPerson'])->findOrFail($id);

        // Decode JSON arrays
        $inquiry->services = json_decode($inquiry->services) ?: [];
        $inquiry->sub_services = json_decode($inquiry->sub_services) ?: [];

        $inquiry_types = inquirytypes::all();
        $countries = countries::all();
        $campaigns = \App\campaign::all();
        $services = other_service::where('parent_id', null)->where('status', 'Active')->get();

        // If you want all customers and references for dropdowns, you can still get them
        $customers = customer::all();
        $sales_reference = sales_reference::all();
        $sale_persons = user::select('users.name', 'users.id')->get();

        return view('inquiry.edit', compact(
            'inquiry',
            'inquiry_types',
            'customers',
            'sales_reference',
            'sale_persons',
            'countries',
            'services',
            'campaigns'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $customer = customer::find($inquiry->customer_id);

        $searched_customer_id = $request->searched_customer_id;
        if (!empty($searched_customer_id)) {
            $inquiry->customer_id = $searched_customer_id;
            $customer = customer::find($searched_customer_id);
        }

        // Validate required fields (matching the Create page)
        $rules = [
            'inquiry_type' => 'required',
            'services'     => 'required',
            'travel_date'  => 'required',
            'remarks'      => ['required', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('The ' . $attribute . ' field is required.');
                }
            }],
        ];

        // If customer is present and editable, ensure basic customer details
        if ($request->filled('customer_name')) {
            $rules['customer_name'] = 'required';
        }
        if ($request->filled('customer_cell')) {
            $rules['customer_cell'] = 'required';
        }

        $this->validate($request, $rules);

        // Update Customer Details if customer exists
        if ($customer) {
            if ($request->filled('customer_name')) {
                $customer->customer_name = $request->customer_name;
            }
            if ($request->filled('customer_type')) {
                $customer->customer_type = $request->customer_type;
            }
            if ($request->filled('customer_cell')) {
                $customer->customer_cell = $request->customer_cell;
            }
            if ($request->has('customer_whatsapp')) {
                $customer->whatsapp_number = $request->customer_whatsapp;
            }
            if ($request->has('customer_address')) {
                $customer->customer_address = $request->customer_address;
            }
            if ($request->has('customer_city')) {
                $customer->city = $request->customer_city;
            }
            if ($request->has('customer_email')) {
                $customer->customer_email = $request->customer_email;
            }
            if ($request->has('customer_reference')) {
                $customer->customer_reference = $request->customer_reference;
            }
            if ($request->has('customer_details')) {
                $customer->customer_remarks = $request->customer_details;
            }
            $customer->save();
        }

        // Prepare services and sub_services
        $services = [];
        $sub_services = [];
        $reqServices = $request->input('services', []);
        $services_count = is_array($reqServices) ? count($reqServices) : 0;
        $data = $request->all();

        for ($i = 0; $i < $services_count; $i++) {
            $services[] = $reqServices[$i];
            if ($i == 0) {
                $sub = isset($data['sub_services']) ? (is_array($data['sub_services']) ? implode(',', $data['sub_services']) : $data['sub_services']) : '';
                $sub_services[] = $services[$i] . '/' . $sub;
            } else {
                $sub = isset($data['sub_services' . $i]) ? (is_array($data['sub_services' . $i]) ? implode(',', $data['sub_services' . $i]) : $data['sub_services' . $i]) : '';
                $sub_services[] = $services[$i] . '/' . $sub;
            }
        }

        // Update Inquiry Details
        if ($request->filled('campaign')) {
            $inquiry->campaign_id = $request->campaign;
        }
        if ($request->filled('inquiry_category')) {
            $inquiry->inquiry_category = $request->inquiry_category;
        }
        $inquiry->services = json_encode($services);
        $inquiry->sub_services = json_encode($request->input('sub_services', []));
        if ($request->filled('sale_reference')) {
            $inquiry->sales_reference = $request->sale_reference;
        }
        if ($request->filled('sale_person')) {
            $inquiry->saleperson = ($request->sp_assign_check == 'on') ? 'un_assign' : $request->sale_person;
        }
        $inquiry->inquiry_type = $request->inquiry_type;
        $inquiry->travel_date = $request->travel_date;
        $inquiry->remarks = $request->remarks;
        $inquiry->no_of_infants = $request->no_of_infants ?: 0;
        $inquiry->no_of_children = $request->no_of_children ?: 0;
        $inquiry->no_of_adults = $request->no_of_adults ?: 0;
        $inquiry->services_sub_services = json_encode($sub_services);
        if ($request->filled('priority')) {
            $inquiry->priority = $request->priority;
        }
        $inquiry->save();

        Session::flash('message', 'Inquiry has been updated successfully!');
        return redirect()->route('inquiry.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\inquiry  $inquiry
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();

        // Allow only if role_id is 1 or 8
        if (in_array($user->role_id, [1, 8])) {
            $inquiry = \App\inquiry::find($id);

            if ($inquiry) {
                // Soft delete inquiry (sets deleted_at)
                $inquiry->delete();

                // Delete related followup_remarks & remarks
                followup_remark::where('inquiry_id', $id)->delete();
                remarks::where('inquiry_id', $id)->delete();

                Session::flash('message', 'Inquiry soft deleted and related records removed successfully.');
            } else {
                Session::flash('error', 'Inquiry not found.');
            }
        } else {
            Session::flash('error', 'You do not have permission to delete inquiries.');
        }

        return back();
    }

    public function uploadView()
    {
        return redirect('inquiry');
    }

    public function downloadTemplate()
    {
        $headers = [
            'customer_name',
            'customer_cell',
            'customer_email',
            'customer_type',
            'services',
            'remarks'
        ];

        $filename = 'inquiry_template.csv';
        $handle = fopen($filename, 'w+');
        fputcsv($handle, $headers);
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function uploadCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $data = array_map('str_getcsv', file($path));

        // Extract and clean the header
        $header = array_map('trim', $data[0]);
        unset($data[0]);

        // 🚫 Limit to max 200 rows
        if (count($data) > 200) {
            return redirect()->back()->withErrors(['csv_file' => 'You can upload a maximum of 200 records at a time.']);
        }

        foreach ($data as $row) {
            // Pad or slice row to match header count to prevent array_combine failures
            $rowCount = count($row);
            $headerCount = count($header);
            
            if ($rowCount < $headerCount) {
                $row = array_pad($row, $headerCount, '');
            } elseif ($rowCount > $headerCount) {
                $row = array_slice($row, 0, $headerCount);
            }

            $row = array_combine($header, $row);

            // Create Customer
            $customer = customer::create([
                'customer_name' => $row['customer_name'] ?: 'Unknown',
                'customer_cell' => $row['customer_cell'] ?: 'N/A',
                'customer_email' => $row['customer_email'] ?? null,
                'customer_type' => !empty($row['customer_type']) ? $row['customer_type'] : 'Individual',
                'sale_person' => Auth::id(),
                'created_by' => Auth::id()
            ]);

            $customerId = $customer->id_customers;

            // Determine service ID and subservices
            $serviceId = null;
            $subServices = [];
            $servicesSubServices = null;

            switch (strtolower(trim($row['services']))) {
                case 'umrah':
                    $serviceId = '13';
                    $subServices = ['14', '15', '16', '17'];
                    break;
                case 'domestic':
                    $serviceId = '7';
                    $subServices = ['8', '10', '11'];
                    break;
                case 'international':
                    $serviceId = '18';
                    $subServices = ['19', '20'];
                    break;
            }

            if ($serviceId && count($subServices)) {
                $servicesSubServices = json_encode([$serviceId . '/' . implode(',', $subServices)]);
            }

            // Create Inquiry
            inquiry::create([
                'customer_id' => $customerId,
                'services' => json_encode([$serviceId]),
                'sub_services' => json_encode($subServices),
                'services_sub_services' => $servicesSubServices,
                'remarks' => $row['remarks'],
                'saleperson' => Auth::user()->id,
                'created_by' => Auth::id(),
                'status' => 'Open'
            ]);
        }

        return redirect()->route('csv.upload.view')->with('success', 'CSV imported successfully.');
    }

    public function fetch_data(Request $request)
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
        $remarks = remarks::where('inquiry_id', $request->id)->get();
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
                                                    <h5><b>Inquiry Type</b>: <u>' . $inquiry->inquiry_type . '</u></h5>
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

    public function get_sub_services($inquiry_id)
    {
        // dd($id);
        $sub_services = other_service::where('parent_id', $id)->get();
        $data = "<option value=''>Select Sub Service</option>";
        foreach ($sub_services as $service) {
            $data .= '<option value="' . $service->id_other_services . '">' . $service->service_name . '</option>';
        }
        echo $data;
    }

    public function edit_inquiry_index($inq_id)
    {
        $sales_person = User::get();
        $campaigns = \App\campaign::all();
        $services = other_service::where('parent_id', null)->get();

        // $sale_persons = \App\Models\User::select('users.name', 'users.id')->where('role_id', '=', 6)->get()->toArray();
        $users = User::all();
        foreach ($users as $key => $value) {
            $user_role_id = $value->role_id;
            $all_roles_id[] = [$user_role_id, $value->id];
        }

        foreach ($all_roles_id as $key => $value) {
            $get_roles_permission = role_permission::where('role_id', $value[0])->where('menu_id', 96)->first();
            if ($get_roles_permission) {
                $final_permission[] = $get_roles_permission;
                $final_user_ids[] = $value[1];
            }
        }

        $uniq_user_id = array_unique($final_user_ids);
        $sale_persons = User::whereIn('id', $uniq_user_id)->get();
        $dec_inq_id = Crypt::decrypt($inq_id);
        $get_inquiry = inquiry::where('id_inquiry', $dec_inq_id)->first();

        $decode_services = json_decode($get_inquiry->services_sub_services);
        if (isset($decode_services)) {
            foreach ($decode_services as $key => $value) {
                $explode = explode('/', $value);
                $get_explode_sub_services = $explode[1];
                $services_id[] = $explode[0];
                $explode_sub_services[] = explode(',', $get_explode_sub_services);
            }
        }
        $echo_services_data = '';

        $services_option = '';
        foreach ($services as $key => $service) {
            if (isset($services_id)) {
                foreach ($services_id as $key => $service_id_2) {
                    $true = $service->id_other_services == $service_id_2;
                    if ($true) {
                        $services_option .= '<option selected value="' . $service->id_other_services . '">
            ' . $service->service_name . '
        </option>';
                    } else {
                        $services_option .= '<option  value="' . $service->id_other_services . '">
                    ' . $service->service_name . '
                </option>';
                    }
                }
            }
        }
        // dd($services_option);
        if (isset($services_id)) {
            foreach ($services_id as $key => $value) {
                $echo_services_data .= '<div class="col-lg-5 mg-t-20 mg-lg-t-0">
            <div class="form-group">
                <label class="form-control-label">Services: <span
                        style="color:red;">*</span></label>
                <select name="services[]" id="services" class="form-control"
                    required="required">
                    <option>Select Services </option>
        ' . $services_option . '
                </select>
            </div>
        </div>
        <div class="col-lg-6 mg-t-20 mg-lg-t-0">
            <div class="form-group">
                <label class="form-control-label">Sub Services:</label>
                <select style="width: 100%" name="sub_services[]" id="sub_services"
                    class="js-example-basic-multiple" multiple="multiple">
                    <option>Select Sub Service</option>
                </select>
            </div>
        </div>
        <div class="col-lg-1 mg-t-20 mg-md-t-0">
            {{-- <label class="form-control-label">Add More</label> --}}
            <button onclick="add_more()" class="btn btn-az-primary mt-4" type="button">Add
                More</button>
        </div>';
            }
        }

        // dd($value);
        $get_customer = customer::where('id_customers', $get_inquiry->customer_id)->first();
        $get_campaign = campaign::where('id_campaigns', $get_inquiry->campaign_id)->first();
        // dd($get_inquiry);
        $all_remarks = remarks::where('inquiry_id', $dec_inq_id)->orderBy('id_remarks', 'desc')->get();
        $get_latest_remarks_count = remarks::where('inquiry_id', $dec_inq_id)->max('id_remarks');
        $get_latest_remarks = remarks::where('id_remarks', $get_latest_remarks_count)->first();
        return view('inquiry.edit_inquiry', compact('dec_inq_id', 'all_remarks', 'get_latest_remarks', 'get_inquiry', 'get_customer', 'get_campaign', 'campaigns', 'services', 'sale_persons', 'echo_services_data'));
    }

    public function add_inquiry_remarks(Request $request)
    {
        $request->validate([
            'remarks' => 'required',
            'status' => 'required',
        ]);

        // Convert 'hold_date' to proper MySQL format if provided
        $holdDate = $request->hold_date ? Carbon::createFromFormat('d/m/Y', $request->hold_date)->format('Y-m-d') : null;

        if ($request->hold_date !== null && $request->status == 10) {
            $followupRemark = new followup_remark();
            $followupRemark->user_id = auth()->user()->id;
            $followupRemark->inquiry_id = $request->inquiry_id;
            $followupRemark->parent_id = 0;
            $followupRemark->remarks = 'Inquiry on hold';
            $followupRemark->followup_date = $holdDate;
            $followupRemark->followup_status = 'Open';
            $followupRemark->followup_type = 1;
            $followupRemark->created_by = auth()->user()->id;
            $followupRemark->created_at = now();
            $followupRemark->save();

            $store = new remarks();
            $store->inquiry_id = $request->inquiry_id;
            $store->remarks = $request->remarks;
            $store->hold_date = $holdDate;
            $store->remarks_status = $request->status;
            $store->cancel_reason = $request->reason;
            $store->followup_date = $holdDate;
            $store->created_by = auth()->user()->id;
            $store->created_at = now();
            $store->save();
        } else {
            $store = new remarks();
            $store->inquiry_id = $request->inquiry_id;
            $store->remarks = $request->remarks;
            $store->hold_date = $holdDate;
            $store->remarks_status = $request->status;
            $store->cancel_reason = $request->reason;
            $store->followup_date = $request->followup_date ? Carbon::createFromFormat('d/m/Y', $request->followup_date)->format('Y-m-d') : null;
            $store->created_by = auth()->user()->id;
            $store->created_at = now();
            $store->save();
        }

        $inquiry = inquiry::find($request->inquiry_id);
        if ($inquiry) {
            $statusMap = [
                '1' => 'In-Progress',
                '4' => 'Completed',
                '5' => 'Cancelled',
                '10' => 'Hold',
            ];
            $statusName = $statusMap[$request->status] ?? null;
            if ($statusName) {
                $inquiry->update(['status' => $statusName]);
            }
        }

        $successMessage = $request->status == 10
            ? 'Inquiry Status on Hold - Follow-up Added Successfully'
            : 'Remarks Added Successfully';

        session()->flash('success', $successMessage);
        return redirect()->back();
    }

    public function add_followup_remarks(Request $request)
    {
        $request->validate([
            'inquiry_id' => 'required|exists:inquiry,id_inquiry',
            'followup_status' => 'required',
            'remarks' => 'required',
            'followup_date' => 'nullable',  // use now() if date not coming from form
        ]);

        // check if there is existing follow-up to decide parent/child
        $get_rem = followup_remark::where('id_followup_remarks', $request->id_follow_up_remarks)->first();
        $inquiry = inquiry::find($request->inquiry_id);
        $new_rem = new followup_remark();
        $new_rem->is_first = 1;
        $new_rem->parent_id = 0;
        $new_rem->user_id = auth()->user()->id;
        $new_rem->inquiry_id = $request->inquiry_id;
        $new_rem->remarks = $request->remarks;
        $new_rem->followup_date = $request->followup_date;
        $new_rem->followup_status = $request->followup_status;
        if ($inquiry) {
            // Update inquiry status based on followup_status value
            $statusMap = [
                'Open' => 'Open',
                'In-Progress' => 'In-Progress',
                'Completed' => 'Completed',
                'Cancelled' => 'Cancelled',
                'Quotation' => 'Quotation',
                'Confirmed' => 'Confirmed',
                'Hold' => 'Hold',
            ];
            $statusName = $statusMap[$request->followup_status] ?? null;
            if ($statusName) {
                $inquiry->status = $statusName;
                $inquiry->save();
            }
        }

        $new_rem->followup_type = $request->followup_type ?? 1;  // fallback to 1 if not present
        $new_rem->created_by = auth()->user()->id;
        $new_rem->save();

        return response()->json(['success' => true, 'message' => 'Follow-up added successfully (child).']);
    }

    public function get_follow_up($id)
    {
        $all_remarks = remarks::where('inquiry_id', $id)
            ->orderBy('id_remarks', 'desc')
            ->get();

        $followup_remarks = followup_remark::where('inquiry_id', $id)
            ->orderBy('id_followup_remarks', 'desc')
            ->get();

        $inquiry = \App\inquiry::select('status')->where('id_inquiry', $id)->first();

        return view('inquiry.followup_modal_content', compact('all_remarks', 'followup_remarks', 'id', 'inquiry'))->render();
    }

    public function append_services_edit($inq_id)
    {
        $services = other_service::where('parent_id', null)->get();
        $get_inquiry = inquiry::where('id_inquiry', $inq_id)->first();
        $decode_services = json_decode($get_inquiry->services_sub_services);
        foreach ($decode_services as $key => $value) {
            $explode = explode('/', $value);
            $get_explode_sub_services = $explode[1];
            $services_id[] = $explode[0];
            $explode_sub_services[] = explode(',', $get_explode_sub_services);
        }
        $echo_services_data = '';
        $echo_sub_services_data = '';
        foreach ($explode_sub_services as $key => $value) {
            foreach ($value as $key => $value) {
                $get_sub_services_name = other_service::where('id_other_services', $value)->first();
                $echo_sub_services_data .= '<option selected value="' . $get_sub_services_name->id_other_services . '">
                ' . $get_sub_services_name->service_name . '
            </option>';
            }
        }
        // dd($explode_sub_services);
        $services_option = '';
        foreach ($services as $key => $service) {
            foreach ($services_id as $key => $service_id_2) {
                $true = $service->id_other_services == $service_id_2;
                if ($true) {
                    $services_option .= '<option selected value="' . $service->id_other_services . '">
            ' . $service->service_name . '
        </option>';
                } else {
                    $services_option .= '<option  value="' . $service->id_other_services . '">
                    ' . $service->service_name . '
                </option>';
                }
            }
        }
        // dd($services_option);
        foreach ($services_id as $key => $value) {
            $echo_services_data .= '<div class="col-lg-5 mg-t-20 mg-lg-t-0 rmv' . $key . '">
            <div class="form-group">
                <label class="form-control-label">Services: <span
                        style="color:red;">*</span></label>
                <select name="services[]" id="services" class="form-control"
                    required="required">
                    <option>Select Services </option>
        ' . $services_option . '
                        </select>
                    </div>
                </div>
                <div class="col-lg-6 mg-t-20 mg-lg-t-0 rmv' . $key . '">
                    <div class="form-group">
                        <label class="form-control-label">Sub Services:</label>
                        <select style="width: 100%" name="sub_services[]" id="sub_services' . $key . '"
                            class="js-example-basic-multiple" multiple="multiple">
        ' . $echo_sub_services_data . '
                        </select>
            </div>
        </div>
        <div class="col-lg-1 mg-t-20 mg-md-t-0 rmv' . $key . ' ">
            <button onclick="remove_echo(' . $key . ')" class="btn btn-danger mt-4" type="button">Remove</button>
        </div>';
        }

        return response()->json([
            'services' => $echo_services_data,
        ]);
    }

    public function inquiry_edit_update(Request $request)
    {
        $this->validate($request, [
            'sale_person' => 'required',
            'travel_date' => 'required',
        ]);

        // dd($request);
        $get_inquiry = inquiry::where('id_inquiry', $request->inq_id)->first();
        $get_inquiry->campaign_id = $request->campaign;
        $get_inquiry->inquiry_category = $request->inquiry_category;
        if ($request->services[0] != 'Select Services') {
            // dd($request->services1);
            $services_count = count($request->services);
            // dd($services_count);
            $data = $request->all();
            // dd($data);
            for ($i = 0; $i < $services_count; $i++) {
                // dd($i);
                $services[] = $data['services'][$i];
                // dd($data['services']);
                if ($i == 0) {
                    $sub_services[] = $services[$i] . '/' . implode(',', $data['sub_services']);
                    // dd($services[$i]);
                } else {
                    // dd($request);
                    // echo   $i;

                    $sub_services[] = $services[$i] . '/' . implode(',', $data['sub_services' . $i]);
                }
            }
            // exit();
            $get_inquiry->services_sub_services = json_encode($sub_services);
        }
        $get_inquiry->saleperson = $request->sale_person;
        $get_inquiry->travel_date = $request->travel_date;
        $get_inquiry->no_of_infants = $request->no_of_infants;
        $get_inquiry->no_of_adults = $request->no_of_adults;
        $get_inquiry->no_of_children = $request->no_of_children;
        $get_inquiry->save();

        // dd(json_encode($sub_services));

        session()->flash('success', 'Updated  Successfully');
        return redirect()->back();
    }

    public function follow_up($inq_id)
    {
        // dd($inq_id);

        $dec_inq_id = $inq_id;
        $sales_person = User::get();
        $campaigns = \App\campaign::all();
        $services = other_service::where('parent_id', null)->get();
        // $quotations = quotation::where('inquiry_id', $dec_inq_id)->orderBy('id_quotations', 'desc')->with('get_issuance')->get();
        // $approve_quo = quotation::where('status', 3)->first();
        //         dd($quotations);

        // $payments = payments_account::with('get_quotation', 'get_quotation.get_inquiry', 'get_quotation_details',)->where('quotation_id', $approve_quo?->id_quotations)->orderby('status', 'asc')->groupBy('payment_id')->get();

        // $quotations_not_approved = quotation::where('inquiry_id', $dec_inq_id)->get();
        $remarks_count = remarks::where('inquiry_id', $dec_inq_id)->where('type', null)->count();

        // if ($get_roles_permission) {
        //     $final_permission[] = $get_roles_permission;
        //     $final_user_ids[] = $value[1];
        // }
        // $sale_persons = \App\Models\User::select('users.name', 'users.id')->where('role_id', '=', 6)->get()->toArray();
        $users = User::all();
        foreach ($users as $key => $value) {
            $user_role_id = $value->role_id;
            $all_roles_id[] = [$user_role_id, $value->id];
        }

        foreach ($all_roles_id as $key => $value) {
            $get_roles_permission = role_permission::where('role_id', $value[0])->where('menu_id', 96)->first();
            if ($get_roles_permission) {
                $final_permission[] = $get_roles_permission;
                $final_user_ids[] = $value[1];
            }
        }

        // $uniq_user_id = array_unique($final_user_ids);
        // $sale_persons = User::whereIn('id', $uniq_user_id)->get();

        $get_inquiry = inquiry::where('id_inquiry', $dec_inq_id)->first();

        $decode_services = json_decode($get_inquiry->services_sub_services);
        foreach ($decode_services as $key => $value) {
            $explode = explode('/', $value);
            $get_explode_sub_services = $explode[1];
            $services_id[] = $explode[0];
            $explode_sub_services[] = explode(',', $get_explode_sub_services);
        }
        $echo_services_data = '';

        $services_option = '';
        $latest_followup_status = [];
        foreach ($services_id as $key => $service) {
            $services_inq[] = other_service::where('id_other_services', $service)->first();
        }
        // dd($services_inq);
        // dd($services_option);
        // dd($services);
        $get_customer = customer::where('id_customers', $get_inquiry->customer_id)->first();
        $get_campaign = campaign::where('id_campaigns', $get_inquiry->campaign_id)->first();
        // dd($get_inquiry);
        $all_remarks = remarks::where('inquiry_id', $dec_inq_id)->where('followup_remarks', null)->where('type', null)->orderBy('id_remarks', 'desc')->get();
        $quotation_remarks = remarks::where('inquiry_id', $dec_inq_id)->where('followup_remarks', null)->where('type', 'quotation')->orderBy('id_remarks', 'desc')->get();

        $open_follow_ups = followup_remark::where('inquiry_id', $dec_inq_id)
            ->where(function ($query) {
                $query
                    ->where('followup_status', 'Open')
                    ->orWhere('followup_status', 'Need Further Follow up');
            })
            ->orderBy('created_at', 'DESC')
            ->get();
        // dd($open_follow_ups);
        $need_further_follow_ups = followup_remark::where('inquiry_id', $dec_inq_id)->orderBy('updated_at', 'DESC')->get();
        $closed_follow_ups = followup_remark::where('inquiry_id', $dec_inq_id)->where('followup_status', 'Closed')->orderBy('updated_at', 'DESC')->get();
        //         echo '<pre>'; print_r($need_further_follow_ups);exit;
        $followup_types = follow_up_type::get();
        $get_latest_remarks_count = remarks::where('inquiry_id', $dec_inq_id)->max('id_remarks');
        $get_latest_remarks = remarks::where('id_remarks', $get_latest_remarks_count)->first();
        // $get_issuance = quotation_issuance::where('inquiry_id', $dec_inq_id)->get();
        $get_reject_status = remarks::where('inquiry_id', $dec_inq_id)->where('type', 'quotation')->latest()->where('remarks_status', 'Quotation Rejected')->first();
        //        $get_payment_status = payments_account::where('inquiry_id', $dec_inq_id)->groupBy('inquiry_id')->first();

        return view(
            'inquiry.follow_up',
            compact(
                'get_reject_status',
                'remarks_count',
                'dec_inq_id',
                'need_further_follow_ups',
                'closed_follow_ups',
                'sales_person',
                'quotation_remarks',
                'all_remarks',
                'get_latest_remarks',
                'get_inquiry',
                'get_customer',
                'get_campaign',
                'campaigns',
                'services_inq',
                'echo_services_data',
                'open_follow_ups',
                'followup_types'
            )
        );
    }

    public function add_progress_remark(Request $request)
    {
        $request->validate([
            'remarks' => 'required',
            'inquiry_id' => 'required|integer',
            'progress_type' => 'required|integer',
            'progress_date' => 'required|date',
            'progress_status' => 'required|string',
        ]);

        $remark = new \App\remarks();
        $remark->inquiry_id = $request->inquiry_id;
        $remark->followup_type = $request->progress_type;  // Save ID
        $remark->remarks = $request->remarks;
        $remark->followup_date = $request->progress_date;
        $remark->remarks_status = $request->progress_status;
        $remark->created_by = $request->progress_user ?? auth()->id();

        $remark->save();

        return response()->json(['success' => true]);
    }

    public function get_progress_remarks($id)
    {
        $remarks = \App\remarks::where('inquiry_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $html = '';
        foreach ($remarks as $remark) {
            $user = \App\Models\User::find($remark->created_by);
            $statusBadge = $remark->remarks_status == 'Open'
                ? '<span class="badge bg-warning text-dark">Open</span>'
                : '<span class="badge bg-success">Closed</span>';
            $typeName = optional(\App\follow_up_type::find($remark->followup_type))->type_name ?? '-';
            $html .= '<div class="p-2 mb-2 border rounded bg-light remark-item">
            <div class="fw-semibold">' . e($remark->remarks) . '</div>
            <div class="mt-1">
                ' . $statusBadge . '
                <span class="badge bg-info text-white">' . e($typeName) . '</span>
                <small class="text-muted">• ' . ($remark->followup_date ?? '-') . '</small>
            </div>
            <small class="text-muted">Added ' . $remark->created_at->diffForHumans() . ' by ' . e($user->name ?? 'N/A') . '</small>
        </div>';
        }
        return response()->json(['html' => $html]);
    }

    public function updateSalesPerson(Request $request)
    {
        $request->validate([
            'inquiry_id' => 'required|integer|exists:inquiry,id_inquiry',
            'sales_person_id' => 'required',
        ]);

        $inquiry = inquiry::find($request->inquiry_id);

        if (!$inquiry) {
            return response()->json(['success' => false, 'message' => 'Inquiry not found'], 404);
        }

        $inquiry->saleperson = $request->sales_person_id;
        $inquiry->save();

        return response()->json([
            'success' => true,
            'message' => 'Sales person updated successfully'
        ]);
    }
    
    public function get_more_followups(Request $request)
    {
        $id = $request->id;
        $offset = $request->offset ?? 5;
        $limit = 5;

        $followups = \App\followup_remark::with('createdBy')
            ->where('inquiry_id', $id)
            ->latest()
            ->skip($offset)
            ->take($limit)
            ->get();

        $html = '';
        foreach ($followups as $fr) {
            $date = $fr->followup_date ? date('d-m-Y', strtotime($fr->followup_date)) : '-';
            $html .= '<div class="p-2 mb-2 border-start border-primary shadow-sm bg-white rounded">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-dark"><i class="fa fa-comment-dots text-primary me-1"></i> Follow-up Remarks:</strong>
                    <span class="badge bg-light text-dark border"><i class="fa fa-calendar-alt me-1"></i> Followup: ' . $date . '</span>
                </div>
                <em class="text-muted d-block mb-2" style="word-wrap: break-word; white-space: normal; overflow-wrap: break-word;">' . e($fr->remarks) . '</em>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-secondary"><i class="fa fa-user me-1"></i> ' . e($fr->createdBy->name ?? 'Unknown') . '</small>
                    <small class="text-secondary"><i class="fa fa-clock me-1"></i> ' . date('d-m-Y H:i', strtotime($fr->created_at)) . '</small>
                </div>
            </div>';
        }

        return response()->json([
            'html' => $html,
            'has_more' => $followups->count() === $limit
        ]);
    }
}
