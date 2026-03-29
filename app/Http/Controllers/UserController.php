<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Position;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;

use App\Http\Requests\UserAddRequest;
use App\Http\Requests\USerEditRequest;

use Illuminate\Support\Facades\Hash;

use App\Http\Traits\SettingTrait;

class UserController extends Controller
{
    use SettingTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search'));

        $users = User::orderBy('created_at', 'DESC')
            ->when(!empty($search), function($query) use($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            })
            ->paginate($this->getDataPerPage())
            ->appends(request()->query());

        return view('pages.users.index')->with([
            'search' => $search,
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        $companies_arr = [];
        foreach($companies as $company) {
            $companies_arr[encrypt($company->id)] = $company->name;
        }
        
        $departments = Department::all();
        $departments_arr = [];
        foreach($departments as $department) {
            $departments_arr[encrypt($department->id)] = $department->name;
        }

        $positions = Position::all();
        $positions_arr = [];
        foreach($positions as $position) {
            $positions_arr[encrypt($position->id)] = $position->position;
        }

        $roles = Role::orderBy('name', 'ASC')
            ->get();

        return view('pages.users.create')->with([
            'companies' => $companies_arr,
            'positions' => $positions_arr,
            'departments' => $departments_arr,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserAddRequest $request)
    {
        $email_arr = explode('@', $request->email);
        $password = Hash::make(reset($email_arr).'123!');

        $user = new User([
            'company_id' => decrypt($request->company_id),
            'department_id' => decrypt($request->department_id),
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'cost_center' => $request->cost_center,

        ]);
        $user->save();

        $role_ids = explode(',', $request->role_ids);
        $user->assignRole($role_ids);

        // logs
        activity('created')
            ->performedOn($user)
            ->log(':causer.name has created user :subject.name');

        return redirect()->route('user.index')->with([
            'message_success' => __('adminlte::users.user_create_success')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail(decrypt($id));

        return view('pages.users.show')->with([
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail(decrypt($id));

        $companies = Company::all();
        $companies_arr = [];
        $company_selected_id = '';
        foreach($companies as $company) {
            $encrypted_id = encrypt($company->id);
            if($user->company_id == $company->id) {
                $company_selected_id = $encrypted_id;
            }

            $companies_arr[$encrypted_id] = $company->name;
        }

        $departments = Department::all();
        $departments_arr = [];
        $department_selected_id = '';
        foreach($departments as $department) {
            $encrypted_id = encrypt($department->id);
            if($user->department_id == $department->id) {
                $department_selected_id = $encrypted_id;
            }

            $departments_arr[$encrypted_id] = $department->name;
        }

        $positions = Position::all();
        $positions_arr = [];
        $position_selected_id = '';
        foreach($positions as $position) {
            $encrypted_id = encrypt($position->id);
            if($user->position_id == $position->id) {
                $position_selected_id = $encrypted_id;
            }

            $positions_arr[$encrypted_id] = $position->position;
        }

        $roles = Role::orderBy('name', 'ASC')
            ->get();

        $user_roles = $user->roles->pluck('name')->toArray();

        return view('pages.users.edit')->with([
            'user' => $user,
            'companies' => $companies_arr,
            'positions' => $positions_arr,
            'departments' => $departments_arr,
            'roles' => $roles,
            'company_selected_id' => $company_selected_id,
            'position_selected_id' => $position_selected_id,
            'department_selected_id' => $department_selected_id,
            'user_roles' => $user_roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserEditRequest $request, $id)
    {
        $user = User::findOrFail(decrypt($id));
        $user_roles = $user->roles->pluck('name')->toArray();

        $changes_arr['old'] = $user->getOriginal();
        $changes_arr['old']['arr'] = $user->roles->pluck('name');

        $user->update([
            'company_id' => decrypt($request->company_id),
            'department_id' => decrypt($request->department_id),
            'name' => $request->name,
            'email' => $request->email,
            'cost_center' => $request->cost_center,
        ]);
        
        $role_ids = explode(',', $request->role_ids);
        $user->syncRoles($role_ids);

        $changes_arr['changes'] = $user->getChanges();
        $changes_arr['changes']['arr'] = $user->roles->pluck('name');

        // logs
        activity('updated')
            ->performedOn($user)
            ->withProperties($changes_arr)
            ->log(':causer.name has updated user :subject.name');

        return back()->with([
            'message_success' => __('adminlte::users.user_update_success')
        ]);
    }

    public function profile($id) {
        $user = User::findOrFail(decrypt($id));

        return view('profile')->with([
            'user' => $user
        ]);
    }

    public function getUsers(Request $request)
    {
        $search = $request->search;

        $users = User::select('id', 'name as text')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->limit(5) // Limit results for performance
            ->get();

        return response()->json(['results' => $users]);
    }

    public function getCostCenters(Request $request)
    {
        $search = $request->search;

        $users = User::select('id', 'name as text')->where('cost_center', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->limit(5) // Limit results for performance
            ->get();

        return response()->json(['results' => $users]);
    }
}
