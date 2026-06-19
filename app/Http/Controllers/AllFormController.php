<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Category;
use App\Models\Form;
use App\Models\AllForm;
use App\Models\Company;
use App\Models\Product;

use App\Exports\GatePassExport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Http\Traits\SettingTrait;
use Auth;

class AllFormController extends Controller
{
    use SettingTrait;

    public function index(Request $request) {
        $search = trim($request->get('search') ?? '');
        $user_id = Auth::user()->id;

        $form_id = $request->query('form_id');

        $forms = Form::all();
        $forms_arr = [];
        foreach($forms as $form) {
            $forms_arr[$form->id] = $form->prefix.' ('.$form->name.')';
        }

        $status = $request->query('status');
                    
        $all_forms = AllForm::orderBy('created_at', 'DESC')
            ->whereHasMorph(
                'model',
                ['App\Models\ProductSample', 'App\Models\RequestPayment', 'App\Models\ProductTransfer',
                'App\Models\GatePass', 'App\Models\RequestCash', 'App\Models\LiquidCash', 
                'App\Models\PettyCash', 'App\Models\PettyLiquid'], 
                function ($query, $type) use($search) {
                    $query->where('control_number', 'like', '%'.$search.'%')
                        ->orWhere('form_id', 'like', '%'.$search.'%');
                }
            )
            ->when($status, function($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($form_id, function($q) use ($form_id) {
                $q->where('form_id', $form_id);
            })
            ->paginate($this->getDataPerPage())->onEachSide(1);

        if ($request->ajax()) {
            return view('pages.all-forms.partials')->with([
                'all_forms' => $all_forms,
                'user_id' => $user_id,
                'forms' => $forms_arr,
            ])->render();
        }


        return view('pages.all-forms.index')->with([
            'all_forms' => $all_forms,
            'search' => $search,
            'user_id' => $user_id,
            'forms' => $forms_arr,
        ]);
    }

    public function show($id) {
        $forms = AllForm::findOrFail(decrypt($id));
        $user = Auth::user();

        return view('pages.all-forms.show')->with([
            'forms' => $forms,
            'user' => $user,
        ]);
    }

    public function export_gate()
    {
        $filePath = public_path('refreshables/gate_pass.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, 'Gate Pass Refreshable.xlsx');
    }

    public function export_psrf()
    {
        $filePath = public_path('refreshables/psrf.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, 'PSRF Refreshable.xlsx');
    }

    public function export_psst()
    {
        $filePath = public_path('refreshables/psst.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, 'PSST Refreshable.xlsx');
    }
}
