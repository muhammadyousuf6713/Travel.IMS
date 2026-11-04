<?php

namespace App\Http\Controllers;

use App\other_service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = other_service::whereNull('parent_id')->get();
        return view('service.index', compact('service'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('service.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
        ]);

        $store = new other_service();
        $store->service_name = $request->service_name;
        $store->save();

        session()->flash('success', 'New Service Added Successfully.');
        return redirect('services');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $edit_vendor = other_service::where('id_other_services', $id)->firstOrFail();
        return view('service.edit', compact('edit_vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
        ]);

        $update = other_service::findOrFail($id);
        $update->service_name = $request->service_name;
        $update->save();

        session()->flash('success', 'Service Updated Successfully.');
        return redirect('services');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $service = other_service::findOrFail($id);
            $childrenCount = other_service::where('parent_id', $service->id_other_services)->count();
            if ($childrenCount > 0) {
                Session::flash('error', 'Cannot delete "' . $service->service_name . '" because it has ' . $childrenCount . ' sub-service(s) linked to it.');
                return redirect('services');
            }
            $service->delete();
            Session::flash('success', 'Service deleted successfully.');
        } catch (\Throwable $e) {
            Session::flash('error', 'Error deleting Service: ' . $e->getMessage());
        }
        return redirect('services');
    }
}
