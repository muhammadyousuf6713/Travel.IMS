<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = DB::table('roles')->get()->toArray();
        return view('roles.index')->with(compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Roles';
        $menu = 'role';
        $submenu = 'role';
        $page = 'add';

        return view('roles.create')->with(compact('title', 'menu', 'submenu', 'page'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'role' => 'required|string|max:191|unique:roles,name',
        ], [
            'role.unique' => 'A role with this name already exists. Please choose a different name.',
        ]);

        $roleName = trim($request->role);

        Role::create([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        return redirect(url('roles'))
            ->with('alert', "Role '{$roleName}' created successfully!")
            ->with('alert-class', 'success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Roles';
        $menu = 'role';
        $submenu = 'role';
        $page = 'edit';
        $role = DB::table('roles')->where('id', $id)->first();

        if (!$role) {
            return redirect(url('roles'))
                ->with('alert', 'Role not found')
                ->with('alert-class', 'danger');
        }

        return view('roles.edit')->with(compact('title', 'menu', 'submenu', 'page', 'role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'role' => 'required|string|max:191|unique:roles,name,' . $id,
        ], [
            'role.unique' => 'A role with this name already exists. Please choose a different name.',
        ]);

        $role = Role::findOrFail($id);
        $roleName = trim($request->role);

        $role->update([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        return redirect(url('roles'))
            ->with('alert', "Role updated successfully to '{$roleName}'!")
            ->with('alert-class', 'success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $role = Role::find($id);

        if (!$role) {
            return back()->with('alert', 'Role not found')->with('alert-class', 'danger');
        }

        if (in_array(strtolower($role->name), ['super admin', 'admin', 'administrator'])) {
            return back()->with('alert', 'System core roles cannot be deleted.')->with('alert-class', 'danger');
        }

        $role->delete();
        return back()->with('alert', 'Role has been deleted successfully')->with('alert-class', 'success');
    }
}
