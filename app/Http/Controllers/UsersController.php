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
        $users = User::with('roles')->where('id', '!=', auth()->user()->id)->get();
        return view('users.index')->with(compact('users'));
    }

    public function create()
    {
        $title = 'Users';
        $menu = 'Users';
        $submenu = 'create';

        $roles = roles::all();
        //        echo '<pre>'; print_r($roles);exit;
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
        // $dec_id = \Crypt::decrypt($id);
        $users = User::find($id);

        $roles = roles::all();

        // print_r($plot); exit;
        return view('users.edit')->with(compact('users', 'roles'));
    }

    public function update(Request $request)
    {
        // $dec_id = \Crypt::decrypt($request->u_id);
        $dec_id = \Crypt::decrypt($request->u_id);
        $validator = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $dec_id . ',id'
        ];
        if ($request->password) {
            $validator['password'] = 'required|string|min:8|confirmed';
        }
        $this->validate($request, $validator);
        // $this->validate($request, $validate);

        $user = User::find($dec_id);

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->role_id = $request->role_id;

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
            $user->delete();
            session()->flash('success', 'User deleted successfully');
        } else {
            session()->flash('error', 'User not found');
        }
        return redirect(url('user-management'));
    }
}
