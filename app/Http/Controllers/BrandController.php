<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\User;

use App\Http\Requests\BrandAddRequest;
use App\Http\Requests\BrandEditRequest;

use App\Http\Traits\SettingTrait;

class BrandController extends Controller
{
    use SettingTrait;

    public function index(Request $request) {
        $search = trim($request->get('search'));
        
        $brands = Brand::orderBy('id', 'ASC')
            ->when(!empty($search), function($query) use($search) {
                $query->where('brand', 'like', '%'.$search.'%');
            })
            ->paginate($this->getDataPerPage())
            ->appends(request()->query());

        return view('pages.brands.index')->with([
            'search' => $search,
            'brands' => $brands
        ]);
    }

    public function create() {
        $users = User::all();
        $users_arr = [];
        foreach($users as $user) {
            $users_arr[encrypt($user->id)] = $user->name;
        }

        return view('pages.brands.create')->with([
            'users' => $users_arr
        ]);
    }

    public function store(BrandAddRequest $request) {

        $brand = new Brand([
            'brand' => $request->brand,
            'bm_id' => decrypt($request->bm_id),
            'gbm_id' => decrypt($request->gbm_id),
        ]);
        $brand->save();

        // logs
        activity('created')
            ->performedOn($brand)
            ->log(':causer.name has created brand :subject.name');

        return redirect()->route('brand.index')->with([
            'message_success' => __('Brand '.$brand->name.' was created')
        ]);
    }

    public function show($id) {
        $brand = Brand::findOrFail(decrypt($id));

        return view('pages.brands.show')->with([
            'brand' => $brand
        ]);
    }

    public function edit($id) {
        $brand = Brand::findOrFail(decrypt($id));
        $users = User::where('department_id', 5)->get();
        $users_arr = [];
        $user_selected_id = '';
        $gbm_selected_id = '';

        foreach($users as $user) {
            $encrypted_id = encrypt($user->id);

            if($brand->bm_id == $user->id) {
                $user_selected_id = $encrypted_id;
            }

            if($brand->gbm_id == $user->id) {
                $gbm_selected_id = $encrypted_id;
            }

            $users_arr[$encrypted_id] = $user->name;

        }

        return view('pages.brands.edit')->with([
            'brand' => $brand,
            'users' => $users_arr,
            'user_selected_id' => $user_selected_id,
            'gbm_selected_id' => $gbm_selected_id,

        ]);
    }

    public function update(BrandEditRequest $request, $id) {
        $brand = Brand::findOrFail(decrypt($id));

        $changes_arr['old'] = $brand->getOriginal();

        $brand->update([
            'brand' => $request->brand,
            'bm_id' => decrypt($request->bm_id),
            'gbm_id' => decrypt($request->gbm_id),
        ]);
        $brand->save();

        $changes_arr['changes'] = $brand->getChanges();

        // logs
        activity('updated')
            ->performedOn($brand)
            ->withProperties($changes_arr)
            ->log(':causer.name has updated brand :subject.name');

        return back()->with([
            'message_success' => __('Brand '.$brand->brand.' was updated')
        ]);
    }
}
