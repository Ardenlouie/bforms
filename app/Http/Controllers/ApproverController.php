<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Category;
use App\Models\Form;
use App\Models\AllForm;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Traits\SettingTrait;
use Auth;

class ApproverController extends Controller
{
    use SettingTrait;

    public function index(Request $request) {
        $search = trim($request->get('search') ?? '');
        $user_id = Auth::user()->id;

        $status = $request->query('status');
                    
        $approvals = AllForm::orderBy('created_at', 'DESC')
            ->where(function ($query) use ($user_id) {
                $query->where('approver', $user_id)
                    ->orWhere('endorser', $user_id);
            })
            ->where('status', '!=', 'draft')
            ->whereHasMorph(
                'model',
                ['App\Models\ProductSample', 'App\Models\RequestPayment', 'App\Models\ProductTransfer',
                'App\Models\GatePass'], 
                function ($query, $type) use($search) {
                    $query->where('control_number', 'like', '%'.$search.'%')
                        ->orWhere('form_id', 'like', '%'.$search.'%');
                }
            )
            ->when($status, function($q) use ($status) {
                $q->where('status', $status);
            })
            ->paginate($this->getDataPerPage())->onEachSide(1);

        if ($request->ajax()) {
            return view('pages.approvers.partials')->with([
                'approvals' => $approvals,
                'user_id' => $user_id,
            ])->render();
        }


        return view('pages.approvers.index')->with([
            'approvals' => $approvals,
            'search' => $search,
            'user_id' => $user_id,
        ]);
    }

    public function show($id) {
        $forms = AllForm::findOrFail(decrypt($id));
        $user = Auth::user();

        return view('pages.approvers.show')->with([
            'forms' => $forms,
            'user' => $user,
        ]);
    }
}
