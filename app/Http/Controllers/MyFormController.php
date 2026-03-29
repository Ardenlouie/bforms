<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Category;
use App\Models\Form;
use App\Models\AllForm;
use App\Models\ProductSample;
use App\Models\ProductTransfer;
use App\Models\GatePass;
use App\Models\RequestPayment;
use App\Models\ProductSampleItem;
use App\Models\ProductTransferItem;
use App\Models\GatePassItem;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;

use App\Http\Requests\PSRFUpdateRequest;
use App\Http\Requests\PSSTUpdateRequest;
use App\Http\Requests\GateUpdateRequest;
use App\Http\Requests\RFPUpdateRequest;
use App\Http\Requests\AllFormEditRequest;
use Illuminate\Http\Request;

use App\Notifications\SubmitFormNotification;
use App\Notifications\ApproveFormNotification;
use App\Notifications\DeclineFormNotification;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Traits\SettingTrait;
use Auth;

class MyFormController extends Controller
{
    use SettingTrait;

    public function index(Request $request) {
        $search = trim($request->get('search') ?? '');
        $user_id = Auth::user()->id;

        $status = $request->query('status');

        $my_forms = AllForm::orderBy('created_at', 'DESC')
            ->where('user_id', $user_id)
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
            return view('pages.my-forms.partials', compact('my_forms'))->render();
        }


        return view('pages.my-forms.index')->with([
            'my_forms' => $my_forms,
            'search' => $search,
        ]);
    }

    public function show($id) {
        $forms = AllForm::findOrFail(decrypt($id));
        $user = Auth::user();

        return view('pages.my-forms.show')->with([
            'forms' => $forms,
            'user' => $user,
        ]);
    }

    public function edit($id) {
        $all_form = AllForm::findOrFail(decrypt($id));

        $form = Form::findOrFail($all_form->form->id);

        $users = User::all();
        $users_arr = [];
        foreach($users as $user) {
            $users_arr[$user->id] = $user->name;
        }

        $companies = Company::all();
        $companies_arr = [];
        foreach($companies as $company) {
            $companies_arr[$company->id] = $company->name;
        }

        $departments = Department::all();
        $departments_arr = [];
        foreach($departments as $department) {
            $departments_arr[$department->id] = $department->name;
        }

        return view('pages.my-forms.edit')->with([
            'all_form' => $all_form,
            'form' => $form,
            'companies' => $companies_arr,
            'departments' => $departments_arr,
            'users' => $users_arr,

        ]);
    }

    public function update_psrf($id, PSRFUpdateRequest $request)
    {
        $user = Auth::user();
        $all_forms = AllForm::findOrFail(decrypt($id));

        $psrf_item = Session::get('psrf_item');

        $all_forms->model->update([
            'control_number' => $request->control_number,
            'company_id' => $request->company_id,
            'recipient' => $request->recipient,
            'activity_name' => $request->activity_name,
            'objective' => $request->objective,
            'special_instructions' => $request->special_instructions,
            'date_submitted' => $request->date_submitted,
            'program_date' => $request->program_date,
            'date_submitted' => $request->date_submitted,
        ]);

        DB::table('psrf_form_items')->where('psrf_form_id', $all_forms->model->id)->delete();

        if(!empty($psrf_item)){
            foreach ($psrf_item['items'] as $key => $items){
                $psrf_item = new ProductSampleItem([
                    'psrf_form_id' => $all_forms->model->id,
                    'item_code' => $items['sku'],
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $psrf_item->save();
            }
        }

        
        $all_forms->update([
            'status' => $request->status,
        ]);

        if($all_forms->status == 'endorsement') {
            if($all_forms->model->control_number == NULL){
                $psrf_number = 'PSRF-0'.$request->company_id.'-001';
                $psrf_form = ProductSample::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $request->company_id)->first();
                if(!empty($psrf_form->control_number)) {
                    $psrf_number_arr = explode('-', $psrf_form->control_number);
                    $last = end($psrf_number_arr);
                    array_pop($psrf_number_arr);
                    $prev_company_id = end($psrf_number_arr);
                    array_pop($psrf_number_arr);
                    if('0'.$request->company_id == $prev_company_id) { 
                        $number = (int)$last + 1;
                    } else { 
                        $number = 1;
                    }
                    for($i = strlen($number);$i <= 2; $i++) {
                        $number = '0'.$number;
                    }
                    array_push($psrf_number_arr, '0'.$request->company_id);
                    array_push($psrf_number_arr, $number);
                    $psrf_number = implode('-', $psrf_number_arr);
                }
                $all_forms->model->update([
                    'control_number' => $psrf_number,
                ]);
            }
            $all_forms->endorsed->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('update')
        ->performedOn($all_forms)
        ->log(':causer.name has updated '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was updated'
        ]);
    }

    public function update_psst($id, PSSTUpdateRequest $request)
    {
        $user = Auth::user();
        $all_forms = AllForm::findOrFail(decrypt($id));
        
        $psst_item = Session::get('psst_item');

        $all_forms->model->update([
            'company_id' => $request->company_id,
            'point_origin' => $request->point_origin,
            'objective' => $request->objective,
            'delivery_instructions' => $request->delivery_instructions,
            'delivery_date' => $request->delivery_date,
            'date_submitted' => $request->date_submitted,
        ]);

        DB::table('psst_form_items')->where('psst_form_id', $all_forms->model->id)->delete();

        if(!empty($psst_item)){
            foreach ($psst_item['items'] as $key => $items){
                $psst_item = new ProductTransferItem([
                    'psst_form_id' => $all_forms->model->id,
                    'item_code' => $items['sku'],
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $psst_item->save();
            }
        }

        
        $all_forms->update([
            'status' => $request->status,
        ]);

        if($all_forms->status == 'endorsement') {
            if($all_forms->model->control_number == NULL){
                $psst_number = 'PSST-0'.$request->company_id.'-001';
                $psst_form = ProductTransfer::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $request->company_id)->first();
                if(!empty($psst_form->control_number)) {
            
                    $psst_number_arr = explode('-', $psst_form->control_number);
                    $last = end($psst_number_arr);
                    array_pop($psst_number_arr);
                    $prev_company_id = end($psst_number_arr);
                    array_pop($psst_number_arr);
                    if('0'.$request->company_id == $prev_company_id) { 
                        $number = (int)$last + 1;
                    } else { 
                        $number = 1;
                    }
                    for($i = strlen($number);$i <= 2; $i++) {
                        $number = '0'.$number;
                    }
                    array_push($psst_number_arr, '0'.$request->company_id);
                    array_push($psst_number_arr, $number);
                    $psst_number = implode('-', $psst_number_arr);
                }

                $all_forms->model->update([
                    'control_number' => $psst_number,
                ]);
            }
            $all_forms->endorsed->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('update')
        ->performedOn($all_forms)
        ->log(':causer.name has updated '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was updated'
        ]);
    }

    public function update_gate($id, GateUpdateRequest $request)
    {
        $user = Auth::user();
        $all_forms = AllForm::findOrFail(decrypt($id));
        $date_code = date('Y');

        $gate_item = Session::get('gate_item');

        $all_forms->model->update([
            'form_id' => $all_forms->form->id,
            'company_id' => $request->company_id,
            'purpose' => $request->purpose,
            'received_by' => $request->received_by,
            'date_submitted' => $request->date_submitted,
        ]);

        DB::table('gate_pass_items')->where('gate_pass_id', $all_forms->model->id)->delete();

        if(!empty($gate_item)){
            foreach ($gate_item['items'] as $key => $items){
                $gate_item = new GatePassItem([
                    'gate_pass_id' => $all_forms->model->id,
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $gate_item->save();
            }
        }

        
        $all_forms->update([
            'status' => $request->status,
        ]);

        if($all_forms->status == 'approval') {
            if($all_forms->model->control_number == NULL){

                $gate_number = 'GP-0'.$request->company_id.'-'.$date_code.'-001';
                $gate_pass = GatePass::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $request->company_id)->first();
                if(!empty($gate_pass->control_number)) {
            
                    $gate_number_arr = explode('-', $gate_pass->control_number);
                    $last = end($gate_number_arr);
                    array_pop($gate_number_arr);
                    $prev_year = end($rfp_number_arr);
                    array_pop($gate_number_arr);
                    $prev_company_id = end($gate_number_arr);
                    array_pop($gate_number_arr);
                    if('0'.$request->company_id == $prev_company_id && $prev_year == $date_code) { 
                        $number = (int)$last + 1;
                    } else { 
                        $number = 1;
                    }
                    for($i = strlen($number);$i <= 2; $i++) {
                        $number = '0'.$number;
                    }
                    array_push($gate_number_arr, '0'.$request->company_id);
                    array_push($gate_number_arr, $number);
                    $gate_number = implode('-', $gate_number_arr);
                }
                $all_forms->model->update([
                    'control_number' => $gate_number,
                ]);

            }
            $all_forms->approved->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('update')
        ->performedOn($all_forms)
        ->log(':causer.name has updated '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was updated'
        ]);
    }

    public function update_rfp($id, RFPUpdateRequest $request)
    {
        $user = Auth::user();
        $all_forms = AllForm::findOrFail(decrypt($id));
        $date_code = date('Y');

        $all_forms->model->update([
            'form_id' => $all_forms->form->id,
            'company_id' => $request->company_id,
            'user_id' => $user->id,
            'department_id' => $request->department_id,
            'payable' => $request->payable,
            'amount' => $request->amount,
            'cost_center' => $request->cost_center,
            'purpose' => $request->purpose,
            'instructions' => $request->instructions,
            'date_submitted' => $request->date_submitted,
            'currency' => $request->currency,
        ]);


        $all_forms->update([
            'status' => $request->status,
            'approver' => $request->approver,
        ]);


        if($all_forms->status == 'approval') {
            if($all_forms->model->control_number == NULL){

                $rfp_number = 'RFP-0'.$request->company_id.'-'.$date_code.'-001';
                $rfp_form = RequestPayment::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $request->company_id)->first();
                
                if(!empty($rfp_form->control_number)) {
                    $rfp_number_arr = explode('-', $rfp_form->control_number);
                    $last = end($rfp_number_arr);
                    array_pop($rfp_number_arr);
                    $prev_year = end($rfp_number_arr);
                    array_pop($rfp_number_arr);
                    $prev_company_id = end($rfp_number_arr);
                    array_pop($rfp_number_arr);
                    if('0'.$request->company_id == $prev_company_id && $prev_year == $date_code) { 
                        $number = (int)$last + 1;
                    } else { 
                        $number = 1;
                    }
                    for($i = strlen($number);$i <= 2; $i++) {
                        $number = '0'.$number;
                    }
                    array_push($rfp_number_arr, '0'.$request->company_id, $date_code);
                    array_push($rfp_number_arr, $number);
                    $rfp_number = implode('-', $rfp_number_arr);
                }
                $all_forms->model->update([
                    'control_number' => $rfp_number,
                ]);
            }
            $all_forms->approved->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('update')
        ->performedOn($all_forms)
        ->log(':causer.name has updated '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was updated'
        ]);
    }
    
}
