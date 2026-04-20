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
        
        $departments = Department::orderBy('id', 'ASC')
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
            'admin_id' => decrypt($request->admin_id),
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
        $users = User::where('department_id', $department->id)->get();
        $users_arr = [];
        $user_selected_id = '';
        $admin_selected_id = '';
        $approvers_selected_ids = [];
        foreach($users as $user) {
            $encrypted_id = encrypt($user->id);
            if($department->head_id == $user->id) {
                $user_selected_id = $encrypted_id;
            }

            if($department->admin_id == $user->id) {
                $admin_selected_id = $encrypted_id;
            }

            if(in_array($user->id, $department->approver_ids ?? [])) {
                $approvers_selected_ids[] = $encrypted_id;
            }

            $users_arr[$encrypted_id] = $user->name;

        }


        return view('pages.departments.edit')->with([
            'department' => $department,
            'users' => $users_arr,
            'user_selected_id' => $user_selected_id,
            'admin_selected_id' => $admin_selected_id,
            'approvers_selected_ids' => $approvers_selected_ids,

        ]);
    }

    public function update(DepartmentEditRequest $request, $id) {
        $department = Department::findOrFail(decrypt($id));

        $validated = $request->validate([
            'approver_ids' => 'required|array',
        ]);

        $changes_arr['old'] = $department->getOriginal();

        $decryptedIds = array_map(function($id) {
            return decrypt($id);
        }, $request->approver_ids);

        $department->update([
            'prefix' => $request->prefix,
            'name' => $request->name,
            'head_id' => decrypt($request->head_id),
            'admin_id' => decrypt($request->admin_id),
            'approver_ids' => $decryptedIds
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
