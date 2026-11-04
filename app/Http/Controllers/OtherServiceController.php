<?php

namespace App\Http\Controllers;

use App\other_service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class OtherServiceController extends Controller
{
    public function index()
    {
        $other_services = other_service::where('parent_id', null)->get();
        return view('other_services.index', compact('other_services'));
    }

    public function create()
    {
        $services = other_service::whereNull('parent_id')->get();
        return view('other_services.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required',
            'status' => 'required',
        ]);

        $store = new other_service();
        $store->service_name = $request->service_name;
        $store->parent_id = $request->services;
        $store->description = $request->description;
        $store->status = ($request->status == 1) ? 'Active' : 'In-Active';
        $store->created_by = auth()->user()->id;
        $store->save();

        session()->flash('success', 'Service Added Successfully');
        return redirect('other_services');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $dec_id = is_numeric($id) ? $id : Crypt::decrypt($id);
        $services = other_service::whereNull('parent_id')->where('id_other_services', '!=', $dec_id)->get();
        $other_services = other_service::where('id_other_services', $dec_id)->firstOrFail();
        return view('other_services.edit', compact('other_services', 'services'));
    }

    public function update(Request $request, $id)
    {
        $dec_id = is_numeric($id) ? $id : Crypt::decrypt($id);
        $store = other_service::where('id_other_services', $dec_id)->firstOrFail();
        $store->service_name = $request->service_name;
        $store->parent_id = $request->services;
        $store->description = $request->description;
        $store->status = ($request->status == 1) ? 'Active' : 'In-Active';
        $store->created_by = auth()->user()->id;
        $store->save();

        session()->flash('success', 'Service Updated Successfully');
        return redirect('other_services');
    }

    public function destroy($id)
    {
        $dec_id = is_numeric($id) ? $id : Crypt::decrypt($id);
        $service = other_service::where('id_other_services', $dec_id)->first();
        if ($service) {
            $service->delete();
            session()->flash('success', 'Service Deleted Successfully');
        } else {
            session()->flash('error', 'Service not found');
        }
        return redirect('other_services');
    }
}
