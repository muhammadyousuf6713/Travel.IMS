<?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use App\Notifications;
// use App\roles;
// use App\role_user;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Crypt;

// class UsersController extends Controller
// {
//     /**
//      * Create a new controller instance.
//      *
//      * @return void
//      */
//     protected $role_id;

//     public function index()
//     {

//         $users = User::select('users.id', 'users.name', 'users.status', 'users.created_at', 'users.updated_at', DB::raw('roles.name as role_name'))
//             //->join('branches','branches.branch_id','users.branch_id')
//             // ->join('role_user', 'role_user.user_id', 'users.id', 'left')
//             ->join('roles', 'roles.id_roles', 'users.role_id', 'left')->get()->toArray();
//         //        echo '<pre>'; print_r($users);exit;
//         // dd($users);
//         return view('Users.index')->with(compact('users'));
//     }

//     public function create()
//     {
//         $title = 'Users';
//         $menu = 'Users';
//         $submenu = 'create';

//         $roles = roles::all();
//         //        echo '<pre>'; print_r($roles);exit;
//         return view('Users.create', compact('roles', 'title', 'menu', 'submenu'));
//     }

//     public function store(Request $request)
//     {

//         $this->validate($request, [
//             'name' => 'required',
//             'email' => 'required|email|unique:users',
//             'password' => 'required|string|min:8|confirmed'
//         ]);

//         //$this->validate($request, $validate);

//         $user = new User(); // User::firstOrCreate(['name' => $request->name, 'email' => $request->email]);

//         if ($request->password) {
//             $user->password = Hash::make($request->password);
//         }

//         $user->name = $request->name;
//         $user->business_id = 1;
//         $user->email = $request->email;
//         //$user->branch_id = $request->branch_id;
//         $user->role_id = $request->role_id;
//         //dd($user); exit;
//         //$user->password = bcrypt($request->password);

//         if ($user->save()) {

//             $role_user = new role_user();
//             $role_user->role_id = $request->role_id;
//             $role_user->user_id = $user->id;
//             $role_user->save();
//             //$role = DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $request->role_id]);//role id will come from Form input
//             session()->flash('success', 'User has been added');
//         } else {
//             session()->flash('danger', 'User has not been added');
//         }
//         return redirect(url('user-management'));
//     }

//     public function edit($id)
//     {
//         $dec_id = \Crypt::decrypt($id);
//         $users = User::find($dec_id);

//         $roles = roles::all();

//         //print_r($plot); exit;
//         return view('Users.edit')->with(compact('users', 'roles'));
//     }

//     public function update(Request $request)
//     {
//         $dec_id = \Crypt::decrypt($request->u_id);
//         $validator = [
//             'name' => 'required',
//             'email' => 'required|email|unique:users,email,' . $dec_id . ',id'
//         ];
//         if ($request->password) {
//             $validator['password'] = 'required|string|min:8|confirmed';
//         }
//         $this->validate($request, $validator);

//         //$this->validate($request, $validate);

//         $user = User::find($dec_id);

//         if ($request->password) {
//             $user->password = Hash::make($request->password);
//         }

//         $user->name = $request->name;
//         $user->email = $request->email;
//         $user->status = $request->status;
//         $user->role_id = $request->role_id;

//         if ($user->save()) {
//             $role = DB::table('role_user')->where('user_id', $dec_id)->update(['user_id' => $user->id, 'role_id' => $request->role_id]); //role id will come from Form input
//             session()->flash('success', 'User has been updated');
//         } else {
//             session()->flash('danger', 'User has not been updated');
//         }
//         return redirect(url('user-management'));
//     }

//     public function profile_index()
//     {
//         return view('Users.profile');
//     }
// }

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications;
use App\role_user;
use App\roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // protected $role_id;
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //            $this->middleware(function ($request, $next) {
    //                $this->role_id = Auth::user()->role_id;
    //             //    $slug_filter = preg_replace('/[0-9]+/', '', $request->path());
    //             //    $slug_filter = preg_replace('/[0-9]+/', '', $request->path());
    //             $ex = explode('/',$request->path());
    //             if(count($ex)>=3){
    //                 $sliced = array_slice($ex, 0, -1);

    //             }else{
    //                 $sliced = $ex;
    //             }

    //             $string = implode("/", $sliced);
    //     //                 dd($string);
    //                if (checkConstructor($this->role_id, count($ex)>=3 ? $string.'/': $string) == 1) {
    //                    return $next($request);
    //                }else if(strpos($request->path(), 'store') !== false){
    //                    return $next($request);
    //                }else if(strpos($request->path(), 'update') !== false){
    //                    return $next($request);
    //                } else {
    //                    abort(404);
    //                }
    //            });
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::with(['roles.permissions', 'permissions'])
            ->where('id', '!=', auth()->user()->id)
            ->get();

        // Eager count inquiries by salesperson to avoid N+1 queries
        $inquiryCounts = \App\inquiry::selectRaw("saleperson, count(*) as total, sum(case when status in ('Confirmed', 'Completed') then 1 else 0 end) as won")
            ->groupBy('saleperson')
            ->get()
            ->keyBy('saleperson');

        foreach ($users as $u) {
            $u->total_inquiries = (int) ($inquiryCounts[$u->id]->total ?? 0);
            $u->won_inquiries = (int) ($inquiryCounts[$u->id]->won ?? 0);
        }

        return view('users.index')->with(compact('users'));
    }

    public function create()
    {
        $title = 'Users';
        $menu = 'Users';
        $submenu = 'create';

        // Security Guard: Super-Admin role is restricted and cannot be selected/assigned
        $roles = roles::where('name', 'NOT LIKE', '%super%')
            ->where('id', '!=', 1)
            ->get();

        return view('users.create', compact('roles', 'title', 'menu', 'submenu'));
    }

    // public function store(Request $request)
    // {

    //     $this->validate($request, [
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|string|min:8|confirmed'
    //     ]);

    //     //$this->validate($request, $validate);

    //     $user = new User(); // User::firstOrCreate(['name' => $request->name, 'email' => $request->email]);

    //     if ($request->password) {
    //         $user->password = Hash::make($request->password);
    //     }

    //     $user->name = $request->name;
    //     $user->business_id = 1;
    //     $user->email = $request->email;
    //     //$user->branch_id = $request->branch_id;
    //     $user->role_id = $request->role_id;
    //     //dd($user); exit;
    //     //$user->password = bcrypt($request->password);

    //     if ($user->save()) {
    //         $role_user = new role_user();
    //         $role_user->role_id = $request->role_id;
    //         $role_user->user_id = $user->id;
    //         $role_user->save();
    //         //$role = DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $request->role_id]);//role id will come from Form input
    //         session()->flash('success', 'User has been added');
    //     } else {
    //         session()->flash('danger', 'User has not been added');
    //     }
    //     return redirect(url('user-management'));
    // }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required',
        ]);

        // Security Guard: Forbid assigning the Super-Admin role to any newly created user
        $targetRole = roles::find($request->role_id);
        if (!$targetRole || (int)$targetRole->id === 1 || stripos($targetRole->name, 'super') !== false) {
            session()->flash('error', 'Security Violation: The Super-Admin role is protected and cannot be assigned.');
            return redirect()->back()->withInput();
        }

        $user = new User();
        $user->name = $request->name;
        $user->role_id = $request->role_id;
        $user->business_id = 1;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        // $user->save();
        // session()->flash('success', 'User has been added with the assigned role.');

        // dd($user);
        if ($user->save()) {
            // Assign role using Spatie
            $role = roles::find($request->role_id);
            if ($role) {
                $user->assignRole($role->name);
            }
            session()->flash('success', 'User has been added with the assigned role.');
        } else {
            session()->flash('error', 'User has not been added.');
        }

        return redirect(url('user-management'));
    }

    public function edit($id)
    {
        $users = User::find($id);
        if (!$users) {
            abort(404);
        }

        $currentIsSuperAdmin = auth()->user() && (
            (int)(auth()->user()->role_id ?? 0) === 1 ||
            (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole(['Super-Admin', 'Super Admin', 'super-admin']))
        );

        $targetIsSuperAdmin = ((int) ($users->role_id ?? 0) === 1 || 
            (method_exists($users, 'hasRole') && $users->hasRole(['Super-Admin', 'Super Admin', 'super-admin'])));

        // Security Guard: Non-superadmin cannot edit a Super-Admin user
        if ($targetIsSuperAdmin && !$currentIsSuperAdmin) {
            abort(403, 'Unauthorized: Super-Admin accounts are protected and cannot be modified.');
        }

        // Security Guard: Super-Admin role is restricted and cannot be selected/assigned
        $roles = roles::where('name', 'NOT LIKE', '%super%')
            ->where('id', '!=', 1)
            ->get();

        return view('users.edit')->with(compact('users', 'roles'));
    }

    public function update(Request $request)
    {
        $dec_id = \Crypt::decrypt($request->u_id);
        $validator = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $dec_id . ',id'
        ];
        if ($request->password) {
            $validator['password'] = 'required|string|min:8|confirmed';
        }
        $this->validate($request, $validator);

        $user = User::find($dec_id);
        if (!$user) {
            abort(404);
        }

        $currentIsSuperAdmin = auth()->user() && (
            (int)(auth()->user()->role_id ?? 0) === 1 ||
            (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole(['Super-Admin', 'Super Admin', 'super-admin']))
        );

        $targetIsSuperAdmin = ((int) ($user->role_id ?? 0) === 1 || 
            (method_exists($user, 'hasRole') && $user->hasRole(['Super-Admin', 'Super Admin', 'super-admin'])));

        // Security Guard: Non-superadmin cannot modify a Super-Admin account
        if ($targetIsSuperAdmin && !$currentIsSuperAdmin) {
            abort(403, 'Unauthorized: Super-Admin accounts are protected and cannot be modified.');
        }

        // Security Guard: Forbid assigning the Super-Admin role to any user
        $selectedRole = roles::find($request->role_id);
        if ($selectedRole && ((int)$selectedRole->id === 1 || stripos($selectedRole->name, 'super') !== false)) {
            if (!$targetIsSuperAdmin || !$currentIsSuperAdmin) {
                session()->flash('error', 'Security Violation: The Super-Admin role cannot be assigned.');
                return redirect()->back();
            }
        }

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->role_id = $request->role_id;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('location')) $user->location = $request->location;
        if ($request->has('about_me')) $user->about_me = $request->about_me;

        if ($request->hasFile('profile_picture')) {
            $dir = public_path('uploads/avatars');
            if (!\Illuminate\Support\Facades\File::exists($dir)) {
                \Illuminate\Support\Facades\File::makeDirectory($dir, 0755, true);
            }
            if (!empty($user->profile_picture) && \Illuminate\Support\Facades\File::exists(public_path($user->profile_picture))) {
                \Illuminate\Support\Facades\File::delete(public_path($user->profile_picture));
            }
            $file = $request->file('profile_picture');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $user->profile_picture = 'uploads/avatars/' . $fileName;
        } elseif ($request->boolean('remove_picture')) {
            if (!empty($user->profile_picture) && \Illuminate\Support\Facades\File::exists(public_path($user->profile_picture))) {
                \Illuminate\Support\Facades\File::delete(public_path($user->profile_picture));
            }
            $user->profile_picture = null;
        }

        if ($user->save()) {
            // Assign role using Spatie
            $role = roles::find($request->role_id);
            if ($role) {
                $user->syncRoles($role->name);
            }
            // $role = DB::table('role_user')->where('user_id', $dec_id)->update(['user_id' => $user->id, 'role_id' => $request->role_id]); //role id will come from Form input
            session()->flash('success', 'User has been updated');
        } else {
            session()->flash('error', 'User has not been updated');
        }
        return redirect(url('user-management'));
    }

    public function profile_index()
    {
        return view('users.profile');
    }

    public function destroy($id)
    {
        if ((int) $id === (int) auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return redirect(url('user-management'));
        }
        $user = User::find($id);
        if ($user) {
            $isSuperAdmin = ((int) ($user->role_id ?? 0) === 1 || 
                (method_exists($user, 'hasRole') && $user->hasRole(['Super-Admin', 'Super Admin', 'super-admin'])) ||
                (method_exists($user, 'can') && $user->can('Super-Admin')));

            if ($isSuperAdmin) {
                session()->flash('error', 'Security Violation: Super-Admin accounts are protected and cannot be deleted.');
                return redirect(url('user-management'));
            }

            $user->delete();
            session()->flash('success', 'User deleted successfully');
        } else {
            session()->flash('error', 'User not found');
        }
        return redirect(url('user-management'));
    }

    /**
     * Impersonate a user to experience the CRM with their exact allotted permissions.
     */
    public function impersonate($id)
    {
        $currentUser = Auth::user();

        $isAuthorized = $currentUser->hasRole(['Admin', 'Super-Admin', 'admin', 'super-admin'])
            || $currentUser->role_id == 1
            || $currentUser->can('Users Management list')
            || $currentUser->can('Super-Admin');

        if (!$isAuthorized) {
            abort(403, 'Unauthorized to login as another user.');
        }

        $targetUser = User::findOrFail($id);

        if ((int) $targetUser->id === (int) $currentUser->id) {
            return redirect()->back()->with('error', 'You are already logged into this account.');
        }

        // Security Guard: Super-Admin accounts can NEVER be impersonated!
        $isTargetSuperAdmin = ((int) ($targetUser->role_id ?? 0) === 1 || 
            (method_exists($targetUser, 'hasRole') && $targetUser->hasRole(['Super-Admin', 'Super Admin', 'super-admin'])) ||
            (method_exists($targetUser, 'can') && $targetUser->can('Super-Admin')));

        if ($isTargetSuperAdmin) {
            return redirect()->back()->with('error', 'Security Violation: Super-Admin accounts are protected and cannot be impersonated.');
        }

        // Store parent account ID in session
        if (!Session::has('impersonated_by')) {
            Session::put('impersonated_by', $currentUser->id);
            Session::put('impersonated_by_name', $currentUser->name);
        }

        Auth::loginUsingId($targetUser->id);

        return redirect('/dashboard')->with('success', 'Logged in as ' . $targetUser->name . ' with allotted permissions. Use "Back to Parent Account" in profile menu to return.');
    }

    /**
     * Switch back to the original parent admin account.
     */
    public function leaveImpersonate()
    {
        if (!Session::has('impersonated_by')) {
            return redirect('/dashboard');
        }

        $originalUserId = Session::pull('impersonated_by');
        Session::forget('impersonated_by_name');

        $originalUser = User::find($originalUserId);
        if ($originalUser) {
            Auth::loginUsingId($originalUser->id);
            return redirect('/user-management')->with('success', 'Switched back to your parent account (' . $originalUser->name . ').');
        }

        return redirect('/dashboard');
    }
}
