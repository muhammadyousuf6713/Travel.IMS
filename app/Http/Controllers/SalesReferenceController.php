<?php

namespace App\Http\Controllers;

use App\sales_reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SalesReferenceController extends Controller
{
    public function index()
    {
        $sales_reference = sales_reference::all();
        return view('sales_reference.index', compact('sales_reference'));
    }

    public function create()
    {
        return view('sales_reference.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);
        $store = new sales_reference();
        $store->type_name = trim($request->type_name);
        $store->business_id = '1';
        $store->save();

        session()->flash('success', 'Sales Reference Added Successfully');
        return redirect('sales-reference');
    }

    public function edit($id)
    {
        $edit_vendor = sales_reference::where('type_id', $id)->firstOrFail();
        return view('sales_reference.edit', compact('edit_vendor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        try {
            $update = sales_reference::findOrFail($id);
            $update->type_name = trim($request->type_name);
            $update->save();

            session()->flash('success', 'Sales Reference Updated Successfully');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }

        return redirect('sales-reference');
    }

    public function destroy($id)
    {
        try {
            $ref = sales_reference::findOrFail($id);
            $inUse = DB::table('inquiry')->where('sales_reference', $id)->count();
            if ($inUse > 0) {
                Session::flash('error', "Cannot delete '{$ref->type_name}' because it is linked to {$inUse} inquiry record(s).");
                return redirect('sales-reference');
            }
            $ref->delete();
            Session::flash('success', 'Sales Reference Removed Successfully');
        } catch (\Exception $e) {
            Session::flash('error', 'Error deleting Sales Reference: ' . $e->getMessage());
        }
        return redirect('sales-reference');
    }
}
