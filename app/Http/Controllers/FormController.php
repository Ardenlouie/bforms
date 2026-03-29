<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Category;
use App\Models\Form;
use App\Models\AllForm;
use App\Models\ProductSample;
use App\Models\ProductSampleItem;
use App\Models\ProductTransfer;
use App\Models\ProductTransferItem;
use App\Models\GatePass;
use App\Models\GatePassItem;
use App\Models\RequestPayment;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;

use App\Http\Requests\FormAddRequest;
use App\Http\Requests\FormEditRequest;
use App\Http\Requests\PSRFStoreRequest;
use App\Http\Requests\PSSTStoreRequest;
use App\Http\Requests\GateStoreRequest;
use App\Http\Requests\RFPStoreRequest;
use App\Http\Requests\AllFormUpdateRequest;
use App\Http\Requests\AllFormCheckRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\SettingTrait;
use App\Notifications\SubmitFormNotification;
use App\Notifications\ApproveFormNotification;
use App\Notifications\DeclineFormNotification;

use Auth;

class FormController extends Controller
{
    use SettingTrait;

    public function index(Request $request)
    {
        $search = trim($request->get('search'));
        
        $forms = Form::orderBy('created_at', 'DESC')
            ->when(!empty($search), function($query) use($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->paginate($this->getDataPerPage())
            ->appends(request()->query());

        return view('pages.forms.index')->with([
            'search' => $search,
            'forms' => $forms
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        $categories_arr = [];
        foreach($categories as $category) {
            $categories_arr[encrypt($category->id)] = $category->name;
        }

        $users = User::all();
        $users_arr = [];
        foreach($users as $user) {
            $users_arr[encrypt($user->id)] = $user->name;
        }

        return view('pages.forms.create')->with([
            'categories' => $categories_arr,
            'users' => $users_arr,
        ]);
    }

    public function edit($id) {
        $form = Form::findOrFail(decrypt($id));

        $users = User::all();
        $users_arr = [];
        $user_selected_id = '';
        foreach($users as $user) {
            $encrypted_id = encrypt($user->id);
            if($form->approver_id == $user->id) {
                $user_selected_id = $encrypted_id;
            }

            $users_arr[$encrypted_id] = $user->name;
        }

        $categories = Category::all();
        $categories_arr = [];
        $category_selected_id = '';
        foreach($categories as $category) {
            $encrypted_id = encrypt($category->id);
            if($form->category_id == $category->id) {
                $category_selected_id = $encrypted_id;
            }

            $categories_arr[$encrypted_id] = $category->name;
        }


        return view('pages.forms.edit')->with([
            'form' => $form,
            'users' => $users_arr,
            'categories' => $categories_arr,
            'user_selected_id' => $user_selected_id,
            'category_selected_id' => $category_selected_id,

        ]);
    }

    public function show($id) {
        $form = Form::findOrFail(decrypt($id));

        return view('pages.forms.show')->with([
            'form' => $form
        ]);
    }

    public function security($id) {
        $all_form = AllForm::findOrFail(decrypt($id));

        return view('security')->with([
            'all_form' => $all_form
        ]);
    }

    public function store(FormAddRequest $request) {

        $form = new Form([
            'prefix' => $request->prefix,
            'name' => $request->name,
            'category_id' => decrypt($request->category_id),
            'approver_id' => decrypt($request->approver_id),
            'department_id' => 1,
        ]);
        $form->save();

        // logs
        activity('created')
            ->performedOn($form)
            ->log(':causer.name has created form :subject.name');

        return redirect()->route('form.index')->with([
            'message_success' => __('Form '.$form->name.' was created')
        ]);
    }

    public function update(FormEditRequest $request, $id) {
        $form = Form::findOrFail(decrypt($id));

        $changes_arr['old'] = $form->getOriginal();

        $form->update([
            'prefix' => $request->prefix,
            'name' => $request->name,
            'category_id' => decrypt($request->category_id),
            'approver_id' => decrypt($request->approver_id),
        ]);
        $form->save();

        $changes_arr['changes'] = $form->getChanges();

        // logs
        activity('updated')
            ->performedOn($form)
            ->withProperties($changes_arr)
            ->log(':causer.name has updated form :subject.name');

        return back()->with([
            'message_success' => __('Form '.$form->name.' was updated')
        ]);
    }

    public function createForm($id)
    {
        $form = Form::findOrFail(decrypt($id));
        $requestor = Auth::user();

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

        return view('pages.forms.createForm',)->with([
            'form' => $form,
            'requestor' => $requestor,
            'users' => $users_arr,
            'companies' => $companies_arr,
            'departments' => $departments_arr,
        ]);
    }


    public function store_psrf($id, PSRFStoreRequest $request)
    {
        $user = Auth::user();
        $form = Form::findOrFail(decrypt($id));
        $date_submitted = date('Y-m-d');

        $psrf_number = 'PSRF-0'.$request->company_id.'-001';
        $psrf_form = ProductSample::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $request->company_id)->first();
        if(!empty($psrf_form)) {
    
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
        
        $psrf_item = Session::get('psrf_item');

        $psrf = new ProductSample([
            'form_id' => $form->id,
            'company_id' => $request->company_id,
            'recipient' => $request->recipient,
            'activity_name' => $request->activity_name,
            'objective' => $request->objective,
            'special_instructions' => $request->special_instructions,
            'program_date' => $request->program_date,
        ]);

      
        $psrf->save();

        if(!empty($psrf_item)){
            foreach ($psrf_item['items'] as $key => $items){
                $psrf_item = new ProductSampleItem([
                    'psrf_form_id' => $psrf->id,
                    'item_code' => $items['sku'],
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $psrf_item->save();
            }
        }
        
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $psrf->id,
            'model_type' => 'App\Models\ProductSample',
            'endorser' => $user->department->head_id,
            'approver' => $form->approver_id,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'endorsement') {
            $psrf->update([
                'control_number' => $psrf_number,
                'date_submitted' => $date_submitted,
            ]);
            $all_forms->endorsed->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('create')
        ->performedOn($psrf)
        ->log(':causer.name has created '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was created'
        ]);
    }

    public function store_psst($id, PSSTStoreRequest $request)
    {
        $user = Auth::user();
        $form = Form::findOrFail(decrypt($id));
        $date_submitted = date('Y-m-d');

        $psst_number = 'PSST-0'.$request->company_id.'-001';
        $psst_form = ProductTransfer::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $request->company_id)->first();
        if(!empty($psst_form)) {
    
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
        
        $psst_item = Session::get('psst_item');

        $psst = new ProductTransfer([
            'form_id' => $form->id,
            'company_id' => $request->company_id,
            'point_origin' => $request->point_origin,
            'delivery_date' => $request->delivery_date,
            'objective' => $request->objective,
            'delivery_instructions' => $request->delivery_instructions,
        ]);

        $psst->save();

        if(!empty($psst_item)){
            foreach ($psst_item['items'] as $key => $items){
                $psst_item = new ProductTransferItem([
                    'psst_form_id' => $psst->id,
                    'item_code' => $items['sku'],
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $psst_item->save();
            }
        }
        
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $psst->id,
            'model_type' => 'App\Models\ProductTransfer',
            'endorser' => $user->department->head_id,
            'approver' => $form->approver_id,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'endorsement') {
            $psst->update([
                'control_number' => $psst_number,
                'date_submitted' => $date_submitted,
            ]);
            $all_forms->endorsed->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('create')
        ->performedOn($psst)
        ->log(':causer.name has created '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was created'
        ]);
    }

    public function store_gate($id, GateStoreRequest $request)
    {
        $user = Auth::user();
        $form = Form::findOrFail(decrypt($id));
        $date_submitted = date('Y-m-d');
        $date_code = date('Y');

        $gate_number = 'GP-0'.$request->company_id.'-'.$date_code.'-001';
        $gate_pass = GatePass::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $request->company_id)->first();
        if(!empty($gate_pass)) {
    
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
        
        $gate_item = Session::get('gate_item');

        $gate = new GatePass([
            'form_id' => $form->id,
            'company_id' => $request->company_id,
            'purpose' => $request->purpose,
            'received_by' => $request->received_by,
        ]);

        $gate->save();

        if(!empty($gate_item)){
            foreach ($gate_item['items'] as $key => $items){
                $gate_item = new GatePassItem([
                    'gate_pass_id' => $gate->id,
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $gate_item->save();
            }
        }
        
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $gate->id,
            'model_type' => 'App\Models\GatePass',
            'approver' => $form->approver_id,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'approval') {
            $gate->update([
                'control_number' => $gate_number,
                'date_submitted' => $date_submitted,
            ]);
            $all_forms->approved->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('create')
        ->performedOn($gate)
        ->log(':causer.name has created '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was created'
        ]);
    }

    public function store_rfp($id, RFPStoreRequest $request)
    {
        $user = Auth::user();
        $form = Form::findOrFail(decrypt($id));
        $date_submitted = date('Y-m-d');
        $date_code = date('Y');

        $rfp_number = 'RFP-0'.$request->company_id.'-'.$date_code.'-001';
        $rfp_form = RequestPayment::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $request->company_id)->first();
        
        if(!empty($rfp_form)) {
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
        
        $rfp = new RequestPayment([
            'form_id' => $form->id,
            'company_id' => $request->company_id,
            'department_id' => $request->department_id,
            'payable' => $request->payable,
            'amount' => $request->amount,
            'cost_center' => $request->cost_center,
            'purpose' => $request->purpose,
            'instructions' => $request->instructions,
            'currency' => $request->currency,
        ]);

        $rfp->save();
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $rfp->id,
            'model_type' => 'App\Models\RequestPayment',
            'approver' => $request->approver,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'approval') {
            $rfp->update([
                'control_number' => $rfp_number,
                'date_submitted' => $date_submitted,
            ]);
            $all_forms->approved->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;
        
        activity('create')
        ->performedOn($rfp)
        ->log(':causer.name has created '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was created'
        ]);
    }

    public function approve($id, AllFormUpdateRequest $request)
    {
        $all_forms = AllForm::findOrFail(decrypt($id)); 

        $user = Auth::user();

        if($all_forms->endorser == $user->id && $request->status == 'approval'){
            $date_endorsed = date('Y-m-d');

            $all_forms->update([
                'date_endorsed' => $date_endorsed,
            ]);
            $all_forms->approved->notify(new SubmitFormNotification($all_forms));
    
        } elseif ($all_forms->endorser == $user->id && $request->status == 'declined') {

            $all_forms->update([
                'remarks' => $request->remarks,
            ]);

            $all_forms->user->notify(new DeclineFormNotification($all_forms));

        }

        if($all_forms->approver == $user->id && $request->status == 'approved'){
            $date_approved = date('Y-m-d');

            $all_forms->update([
                'date_approved' => $date_approved,
            ]);
            $all_forms->user->notify(new ApproveFormNotification($all_forms));

        } elseif ($all_forms->approver == $user->id && $request->status == 'declined') {

            $all_forms->update([
                'remarks' => $request->remarks,
            ]);

            $all_forms->user->notify(new DeclineFormNotification($all_forms));
            
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;
        
        $all_forms->update([
            'status' => $request->status,
        ]);
        
        if ($request->status == 'declined'){
            activity('declined')
            ->performedOn($all_forms)
            ->log(':causer.name has decline '.$form_name.' ['.$control_number.']');

            return redirect()->route('approver.index')->with([
                'message_success' => $form_name.' ['.$control_number.'] was declined'
            ]);
        } else {
            activity('approved')
            ->performedOn($all_forms)
            ->log(':causer.name has approve '.$form_name.' ['.$control_number.']');

            return redirect()->route('approver.index')->with([
                'message_success' => $form_name.' ['.$control_number.'] was approved'
            ]);
        }
    }

    public function check($id, AllFormCheckRequest $request) {
        $all_forms = AllForm::findOrFail(decrypt($id)); 

        $all_forms->update([
            'status' => $request->status,
        ]);

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        activity('checked')
            ->performedOn($all_forms)
            ->log('Security has check '.$form_name.' ['.$control_number.']');

        return redirect()->route('home')->with([
            'message_success' => $form_name.' ['.$control_number.'] was checked'
        ]);
    }

    public function printPDF($id) {
        $forms = AllForm::findOrFail(decrypt($id)); 
        $prefix = $forms->form->prefix;

        $pdf = PDF::loadView('pages.forms.pdfs.'.$prefix, [
            'forms' => $forms,
        ]);

        return $pdf->stream();
    }

}
