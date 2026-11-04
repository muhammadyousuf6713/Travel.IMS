<?php

namespace App\Http\Controllers;

use App\inquirytypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class InquirytypesController extends Controller
{
    public function index()
    {
        $inquiry_types = inquirytypes::all();
        return view('inquiry_types.index', compact('inquiry_types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inquiry_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);
        $vendor = new inquirytypes();
        $vendor->type_name = trim($request->type_name);
        $vendor->type_desc = $request->type_desc ?? $request->type_name;
        $vendor->business_id = '1';
        $vendor->save();

        session()->flash('success', 'Inquiry Type Added Successfully');
        return redirect('inquiry-type');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $edit_vendor = inquirytypes::where('type_id', $id)->firstOrFail();
        return view('inquiry_types.edit', compact('edit_vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        $inquiry_type = inquirytypes::where('type_id', $id)->firstOrFail();
        $inquiry_type->type_name = trim($request->type_name);
        if ($request->filled('type_desc')) {
            $inquiry_type->type_desc = $request->type_desc;
        }
        $inquiry_type->save();

        session()->flash('success', 'Inquiry Type Updated Successfully');
        return redirect('inquiry-type');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $type = inquirytypes::where('type_id', $id)->first();
        if (!$type) {
            session()->flash('error', 'Inquiry Type not found.');
            return redirect('inquiry-type');
        }

        // Protect live data: never delete a type that is still linked to inquiries
        $inUse = DB::table('inquiry')->where('inquiry_type', $id)->count();
        if ($inUse > 0) {
            session()->flash('error', "Cannot delete '{$type->type_name}' because it is linked to {$inUse} inquiry record(s). You can edit its name instead.");
            return redirect('inquiry-type');
        }

        $type->delete();
        session()->flash('success', 'Inquiry Type deleted successfully');
        return redirect('inquiry-type');
    }
}
