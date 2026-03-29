<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;

use App\Http\Requests\DepartmentAddRequest;
use App\Http\Requests\DepartmentEditRequest;

use App\Http\Traits\SettingTrait;

class DepartmentController extends Controller
{
    use SettingTrait;

    public function index(Request $request) {
        $search = trim($request->get('search'));
        
        $departments = Department::orderBy('created_at', 'DESC')
            ->when(!empty($search), function($query) use($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->paginate($this->getDataPerPage())
            ->appends(request()->query());

        return view('pages.departments.index')->with([
            'search' => $search,
            'departments' => $departments
        ]);
    }

    public function create() {
        $users = User::all();
        $users_arr = [];
        foreach($users as $user) {
            $users_arr[encrypt($user->id)] = $user->name;
        }

        return view('pages.departments.create')->with([
            'users' => $users_arr
        ]);
    }

    public function store(DepartmentAddRequest $request) {

        $department = new Department([
            'prefix' => $request->prefix,
            'name' => $request->name,
            'head_id' => decrypt($request->head_id),
        ]);
        $department->save();

        // logs
        activity('created')
            ->performedOn($department)
            ->log(':causer.name has created department :subject.name');

        return redirect()->route('department.index')->with([
            'message_success' => __('Department '.$department->name.' was created')
        ]);
    }

    public function show($id) {
        $department = Department::findOrFail(decrypt($id));

        return view('pages.departments.show')->with([
            'department' => $department
        ]);
    }

    public function edit($id) {
        $department = Department::findOrFail(decrypt($id));
        $users = User::all();
        $users_arr = [];
        $user_selected_id = '';
        foreach($users as $user) {
            $encrypted_id = encrypt($user->id);
            if($department->head_id == $user->id) {
                $user_selected_id = $encrypted_id;
            }

            $users_arr[$encrypted_id] = $user->name;
        }


        return view('pages.departments.edit')->with([
            'department' => $department,
            'users' => $users_arr,
            'user_selected_id' => $user_selected_id,

        ]);
    }

    public function update(DepartmentEditRequest $request, $id) {
        $department = Department::findOrFail(decrypt($id));

        $changes_arr['old'] = $department->getOriginal();

        $department->update([
            'prefix' => $request->prefix,
            'name' => $request->name,
            'head_id' => decrypt($request->head_id),
        ]);
        $department->save();

        $changes_arr['changes'] = $department->getChanges();

        // logs
        activity('updated')
            ->performedOn($department)
            ->withProperties($changes_arr)
            ->log(':causer.name has updated department :subject.name');

        return back()->with([
            'message_success' => __('Department '.$department->name.' was updated')
        ]);
    }

}
