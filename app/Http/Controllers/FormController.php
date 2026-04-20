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
use App\Models\RequestCash;
use App\Models\RequestCashItem;
use App\Models\LiquidCash;
use App\Models\LiquidCashItem;
use App\Models\PettyCash;
use App\Models\PettyCashItem;
use App\Models\PettyLiquid;
use App\Models\PettyLiquidItem;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;

use App\Http\Requests\FormAddRequest;
use App\Http\Requests\FormEditRequest;
use App\Http\Requests\PSRFStoreRequest;
use App\Http\Requests\PSSTStoreRequest;
use App\Http\Requests\GateStoreRequest;
use App\Http\Requests\RFPStoreRequest;
use App\Http\Requests\RCAStoreRequest;
use App\Http\Requests\LCAStoreRequest;
use App\Http\Requests\PCAStoreRequest;
use App\Http\Requests\PCLStoreRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\SettingTrait;

use App\Notifications\SubmitFormNotification;
use App\Notifications\ApproveFormNotification;
use App\Notifications\DeclineFormNotification;

use App\Helpers\FileSavingHelper;
use Auth;

class FormController extends Controller
{
    use SettingTrait;

    public function index(Request $request)
    {
        $search = trim($request->get('search'));
        
        $forms = Form::orderBy('id', 'ASC')
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

        $departments = Department::all();
        $departments_arr = [];
        foreach($departments as $department) {
            $departments_arr[encrypt($department->id)] = $department->name;
        }

        return view('pages.forms.create')->with([
            'categories' => $categories_arr,
            'users' => $users_arr,
            'departments' => $departments_arr,
        ]);
    }

    public function edit($id) 
    {
        $form = Form::findOrFail(decrypt($id));

        $users = User::all();
        $users_arr = [];
        $user_selected_id = '';
        $beva_selected_id = '';
        foreach($users as $user) {
            $encrypted_id = encrypt($user->id);
            if($form->approver_id == $user->id) {
                $user_selected_id = $encrypted_id;
            }

            if($form->beva_approver_id == $user->id) {
                $beva_selected_id = $encrypted_id;
            }

            $users_arr[$encrypted_id] = $user->name;
        }

        $departments = Department::all();
        $departments_arr = [];
        $department_selected_id = '';
        foreach($departments as $department) {
            $encrypted_id = encrypt($department->id);
            if($form->department_id == $department->id) {
                $department_selected_id = $encrypted_id;
            }

            $departments_arr[$encrypted_id] = $department->name;
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
            'departments' => $departments_arr,
            'user_selected_id' => $user_selected_id,
            'beva_selected_id' => $beva_selected_id,
            'category_selected_id' => $category_selected_id,
            'department_selected_id' => $department_selected_id,

        ]);
    }

    public function liquid($id) {
        $all_form = AllForm::findOrFail(decrypt($id));

        if($all_form->form->prefix == 'rca'){
            $form = Form::where('prefix', 'lca')->first();
        } elseif($all_form->form->prefix == 'pca'){
            $form = Form::where('prefix', 'pcl')->first();
        } elseif($all_form->form->prefix == 'psrf'){
            $form = Form::where('prefix', 'pgp')->first();
        } 

        $prefix = strtolower($form->prefix);

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

        return view('pages.forms.liquid')->with([
            'all_form' => $all_form,
            'form' => $form,
            'companies' => $companies_arr,
            'departments' => $departments_arr,
            'users' => $users_arr,
            'prefix' => $prefix,

        ]);
    }

    public function show($id) 
    {
        $form = Form::findOrFail(decrypt($id));
        
        return view('pages.forms.show')->with([
            'form' => $form
        ]);
    }

    public function product_api(Request $request)
    {
        $search = $request->query('search');

        // $product_api = Http::withToken('UaHxtws9LHZ47QG21lBXjQgka3Fe93H5xV1Y6HBQDN4=')
        //     ->get('http://192.168.11.240/refreshable/public/api/invMaster');

        // $products_collect = collect($product_api->json());

        // $results = $products_collect
        //     ->when($search, function ($collection) use ($search) {

        //         return $collection->filter(function ($item) use ($search) {
        //             return false !== stripos($item['Stock Description'], $search) || 
        //                 false !== stripos($item['StockCode'], $search);
        //         });
        //     })
        //     ->take(5)
        //     ->map(function ($item) {
        //         return [
        //             'id'   => $item['StockCode'], 
        //             'text' => $item['StockCode'] . ' - ' . $item['Stock Description'], 
        //         ];
        //     })
        //     ->values();

        $products = Product::select('id', 'description', 'stock_code as text', 'size') // Added uom if you have it
            ->when($search, function ($query) use ($search) {
                $query->where('stock_code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->limit(5) 
            ->get()
            ->map(function ($item) {
                return [
                    'id'          => $item->text,
                    'text'        => $item->text,
                    'description' => $item->description . ' - ' . $item->size, 
                ];
            });


        return response()->json(['results' => $products]);
    }

    public function security($id) 
    {
        $all_form = AllForm::findOrFail(decrypt($id));

        return view('security')->with([
            'all_form' => $all_form
        ]);
    }

    public function store(FormAddRequest $request) 
    {

        $form = new Form([
            'prefix' => $request->prefix,
            'name' => $request->name,
            'category_id' => decrypt($request->category_id) ?? null,
            'approver_id' => decrypt($request->approver_id) ?? null,
            'beva_approver_id' => decrypt($request->beva_approver_id) ?? null,
            'department_id' => decrypt($request->department_id) ?? null,
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

    public function update(FormEditRequest $request, $id) 
    {
        $form = Form::findOrFail(decrypt($id));

        $changes_arr['old'] = $form->getOriginal();

        $form->update([
            'prefix' => $request->prefix,
            'name' => $request->name,
            'category_id' => decrypt($request->category_id),
            'approver_id' => decrypt($request->approver_id),
            'beva_approver_id' => decrypt($request->beva_approver_id),
            'department_id' => decrypt($request->department_id),
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

    public function createForm($id, Request $request)
    {
        $form = Form::findOrFail(decrypt($id));
        $requestor = Auth::user();
        
        $prefix = strtolower($form->prefix);

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
            'prefix' => $prefix,
        ]);
    }

    public function printPDF($id) 
    {
        $forms = AllForm::findOrFail(decrypt($id)); 
        $prefix = $forms->form->prefix;

        $pdf = PDF::loadView('pages.forms.pdfs.'.$prefix, [
            'forms' => $forms,
        ]);

        return $pdf->stream();
    }

    public function store_psrf($id, PSRFStoreRequest $request)
    {
        $user = Auth::user();
        $form = Form::findOrFail(decrypt($id));
        $date_submitted = date('Y-m-d');
 
        $psrf_item = Session::get('psrf_item');

        $request->validate([
            'psrf_item.*.qty' => 'required|numeric|min:1',
        ], [
            'psrf_item.*.qty.min' => 'Quantity must be at least 1.',
        ]);

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

               
                $psrf_items = new ProductSampleItem([
                    'psrf_form_id' => $psrf->id,
                    'item_code' => $items['sku'],
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $psrf_items->save();
            }
        }
        
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $psrf->id,
            'model_type' => 'App\Models\ProductSample',
            'endorser' => $user->department->head_id,
            'approver' => [$form->approver_id],
            'status' => $request->status,
        ]);

        $all_forms->save();


        if($all_forms->status == 'endorsement') {
            $psrf->update([
                'control_number' => $psrf_item['control_number'],
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
                $psst_items = new ProductTransferItem([
                    'psst_form_id' => $psst->id,
                    'item_code' => $items['sku'],
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $psst_items->save();
            }
        }
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $psst->id,
            'model_type' => 'App\Models\ProductTransfer',
            'endorser' => $user->department->head_id,
            'approver' => [$form->approver_id],
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'endorsement') {
            $psst->update([
                'control_number' => $psst_item['control_number'],
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

        $gate_item = Session::get('gate_item');

        $gate = new GatePass([
            'form_id' => $form->id,
            'company_id' => $request->company_id,
            'purpose' => $request->purpose,
            'received_by' => $request->received_by,
            'psrf_form_id' => $request->psrf_form_id ?? null,
        ]);

        $gate->save();

        if(!empty($gate_item)){
            foreach ($gate_item['items'] as $key => $items){
                $gate_items = new GatePassItem([
                    'gate_pass_id' => $gate->id,
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'quantity_release' =>  $items['qty'],
                    'balance' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $gate_items->save();
            }
        }

        if($request->filled('image')) {
            $image = $request->image;
            
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            
            $imageName = 'capture_' . time() . '.png';
            
            Storage::disk('uploads')->put('gate-pass-images/' . $imageName, base64_decode($image));
            
            $gate->update([
                'path' => 'uploads/gate-pass-images/' . $imageName,
                'image' => $imageName,
            ]);
        }

        $approver = $form->department->approver_ids;
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $gate->id,
            'model_type' => 'App\Models\GatePass',
            'approver' => $approver,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'approval') {
            $gate->update([
                'control_number' => $gate_item['control_number'],
                'date_submitted' => $date_submitted,
            ]);

            $approvers = User::whereIn('id', $all_forms->approver ?? [])->get();

            if ($approvers->isNotEmpty()) {
                Notification::send($approvers, new SubmitFormNotification($all_forms));
            }

            // $all_forms->approved->notify(new SubmitFormNotification($all_forms));
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

        $rfp_item = Session::get('rfp_item');
  
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

        if(!empty($request->file_name)) {
            $request->validate([
                'file_name' => 'required|mimes:pdf|max:5120',
            ]);

            $path = NULL;
            $nameWithExtension = $request->file_name->getClientOriginalName();

            $path = FileSavingHelper::saveFile($request->file_name, $rfp->id, 'rfp-attachments');

            $rfp->update([
                'path' => $path,
                'file_name' => $nameWithExtension,
            ]);
        }
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $rfp->id,
            'model_type' => 'App\Models\RequestPayment',
            'approver' => $user->head_approver_id,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'approval') {
            $rfp->update([
                'control_number' => $rfp_item['control_number'],
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

    public function store_rca($id, RCAStoreRequest $request)
    {
        $user = Auth::user();
        $form = Form::findOrFail(decrypt($id));
        $date_submitted = date('Y-m-d');
        $date_code = date('Y');

        $rca_item = Session::get('rca_item');

        $rca = new RequestCash([
            'form_id' => $form->id,
            'company_id' => $request->company_id,
            'name' => $request->name,
            'department_id' => $user->department_id,
            'purpose' => $request->purpose,
            'travel' => $request->travel,
            'cost_center' => $request->cost_center,
            'rca_date' => $request->rca_date,
            'itenerary' => $request->itenerary,
            'location' => $request->location,
            'total_amount' => $rca_item['total_amount'],
        ]);

        $rca->save();

        if(!empty($rca_item)){
            foreach ($rca_item['items'] as $key => $items){
                $rca_items = new RequestCashItem([
                    'rca_form_id' => $rca->id,
                    'item_description' => $items['desc'],
                    'amount' =>  $items['amount'],
                    'days' =>  $items['days'],
                    'remarks' =>  $items['remarks'],
                ]);
                $rca_items->save();
            }
        }
        
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $rca->id,
            'model_type' => 'App\Models\RequestCash',
            'admin_id' => $user->department->admin_id,
            'endorser' => $user->head_approver_id,
            'approver' => $form->approver_id,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'confirmation') {
            $rca->update([
                'control_number' => $rca_item['control_number'],
                'date_submitted' => $date_submitted,
            ]);
            $all_forms->admin->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('create')
        ->performedOn($rca)
        ->log(':causer.name has created '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was created'
        ]);
    }

    public function store_lca($id, LCAStoreRequest $request)
    {
        $user = Auth::user();
        $form = Form::findOrFail(decrypt($id));
        $date_submitted = date('Y-m-d');
        $date_code = date('Y');

        $lca_item = Session::get('lca_item');

        $lca = new LiquidCash([
            'form_id' => $form->id,
            'company_id' => $request->company_id,
            'rca_form_id' => $request->rca_form_id,
            'total_amount' => $lca_item['total_amount'],
            'balance' => $lca_item['balance'],
        ]);

        $lca->save();

        if(!empty($request->file_name)) {
            $request->validate([
                'file_name' => 'required|mimes:pdf|max:5120',
            ]);

            $path = NULL;
            $nameWithExtension = $request->file_name->getClientOriginalName();

            $path = FileSavingHelper::saveFile($request->file_name, $lca->id, 'lca-receipts');

            $lca->update([
                'path' => $path,
                'file_name' => $nameWithExtension,
            ]);
        }

        if(!empty($lca_item)){
            foreach ($lca_item['items'] as $key => $items){
                $lca_items = new LiquidCashItem([
                    'lca_form_id' => $lca->id,
                    'date' =>  $items['date'],
                    'item_description' => $items['desc'],
                    'area' =>  $items['area'],
                    'amount' =>  $items['amount'],
                ]);
                $lca_items->save();
            }
        }
        
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $lca->id,
            'model_type' => 'App\Models\LiquidCash',
            'endorser' => $user->head_approver_id,
            'approver' => $form->approver_id,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'endorsement') {
            $lca->update([
                'control_number' => $lca_item['control_number'],
                'date_submitted' => $date_submitted,
            ]);
            $all_forms->endorsed->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('create')
        ->performedOn($lca)
        ->log(':causer.name has created '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was created'
        ]);
    }

    public function store_pca($id, PCAStoreRequest $request)
    {
        $user = Auth::user();
        $form = Form::findOrFail(decrypt($id));
        $date_submitted = date('Y-m-d');
        $date_code = date('Y');

        $pca_item = Session::get('pca_item');

        $pca = new PettyCash([
            'form_id' => $form->id,
            'company_id' => $request->company_id,
            'name' => $request->name,
            'cost_center' => $request->cost_center,
            'total_amount' => $pca_item['total_amount'],
        ]);

        $pca->save();

        if(!empty($pca_item)){
            foreach ($pca_item['items'] as $key => $items){
                $pca_items = new PettyCashItem([
                    'pca_form_id' => $pca->id,
                    'item_description' => $items['desc'],
                    'amount' =>  $items['amount'],
                ]);
                $pca_items->save();
            }
        }
        
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $pca->id,
            'model_type' => 'App\Models\PettyCash',
            'approver' => $form->approver_id,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'approval') {
            $pca->update([
                'control_number' => $pca_item['control_number'],
                'date_submitted' => $date_submitted,
            ]);
            $all_forms->approved->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('create')
        ->performedOn($pca)
        ->log(':causer.name has created '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was created'
        ]);
    }

    public function store_pcl($id, PCLStoreRequest $request)
    {
        $user = Auth::user();
        $form = Form::findOrFail(decrypt($id));
        $date_submitted = date('Y-m-d');
        $date_code = date('Y');

        $pcl_item = Session::get('pcl_item');

        $pcl = new PettyLiquid([
            'form_id' => $form->id,
            'company_id' => $request->company_id,
            'pca_form_id' => $request->pca_form_id,
            'total_amount' => $pcl_item['total_amount'],
            'balance' => $pcl_item['balance'],
        ]);

        $pcl->save();

        if(!empty($request->file_name)) {
            $request->validate([
                'file_name' => 'required|mimes:pdf|max:5120',
            ]);

            $path = NULL;
            $nameWithExtension = $request->file_name->getClientOriginalName();

            $path = FileSavingHelper::saveFile($request->file_name, $pcl->id, 'pcl-receipts');

            $pcl->update([
                'path' => $path,
                'file_name' => $nameWithExtension,
            ]);
        }

        if(!empty($pcl_item)){
            foreach ($pcl_item['items'] as $key => $items){
                $pcl_items = new PettyLiquidItem([
                    'pcl_form_id' => $pcl->id,
                    'item_description' => $items['desc'],
                    'amount' =>  $items['amount'],
                ]);
                $pcl_items->save();
            }
        }
        
        
        $all_forms = new AllForm([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'model_id' => $pcl->id,
            'model_type' => 'App\Models\PettyLiquid',
            'approver' => $form->approver_id,
            'status' => $request->status,
        ]);

        $all_forms->save();

        if($all_forms->status == 'approval') {
            $pcl->update([
                'control_number' => $pcl_item['control_number'],
                'date_submitted' => $date_submitted,
            ]);
            $all_forms->approved->notify(new SubmitFormNotification($all_forms));
        }

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        
        activity('create')
        ->performedOn($pcl)
        ->log(':causer.name has created '.$form_name.' ['.$control_number.']');

        return redirect()->route('myforms.index')->with([
            'message_success' => $form_name.' ['.$control_number.'] was created'
        ]);
    }

    

}
