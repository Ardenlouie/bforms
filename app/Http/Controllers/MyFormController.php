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

use App\Http\Requests\PSRFUpdateRequest;
use App\Http\Requests\PSSTUpdateRequest;
use App\Http\Requests\GateUpdateRequest;
use App\Http\Requests\RFPUpdateRequest;
use App\Http\Requests\RCAUpdateRequest;
use App\Http\Requests\LCAUpdateRequest;
use App\Http\Requests\PCAUpdateRequest;
use App\Http\Requests\PCLUpdateRequest;
use App\Http\Requests\AllFormEditRequest;
use Illuminate\Http\Request;

use App\Notifications\SubmitFormNotification;
use App\Notifications\ApproveFormNotification;
use App\Notifications\DeclineFormNotification;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use App\Http\Traits\SettingTrait;
use App\Helpers\FileSavingHelper;

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

        $folderPath = 'uploads/gate-pass-images/to-release/' . $forms->model->id;
        $directory = public_path($folderPath); 
        
        if (!File::exists($directory)) {
            return view('pages.my-forms.show', [
                'images' => [],
                'folderPath' => $folderPath,
                'forms' => $forms,
                'user' => $user,
            ]);
        }

        $files = File::files($directory);
        
        $images = [];
        foreach ($files as $file) {
            $images[] = $file->getFilename();
        }

        return view('pages.my-forms.show')->with([
            'forms' => $forms,
            'user' => $user,
            'images' => $images,
            'folderPath' => $folderPath,
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
            'program_date' => $request->program_date,
        ]);

        DB::table('psrf_form_items')->where('psrf_form_id', $all_forms->model->id)->delete();

        if(!empty($psrf_item)){
            foreach ($psrf_item['items'] as $key => $items){
                $psrf_items = new ProductSampleItem([
                    'psrf_form_id' => $all_forms->model->id,
                    'item_code' => $items['sku'],
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $psrf_items->save();
            }
        }

        
        $all_forms->update([
            'status' => $request->status,
        ]);

        if($all_forms->status == 'endorsement') {
            if($all_forms->model->control_number == NULL){
           
                $all_forms->model->update([
                    'control_number' => $psrf_item['control_number'],
                    'date_submitted' => $request->date_submitted,

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
        ]);

        DB::table('psst_form_items')->where('psst_form_id', $all_forms->model->id)->delete();

        if(!empty($psst_item)){
            foreach ($psst_item['items'] as $key => $items){
                $psst_items = new ProductTransferItem([
                    'psst_form_id' => $all_forms->model->id,
                    'item_code' => $items['sku'],
                    'item_description' => $items['desc'],
                    'uom' =>  $items['uom'],
                    'quantity' =>  $items['qty'],
                    'remarks' =>  $items['remarks'],
                ]);
                $psst_items->save();
            }
        }

        
        $all_forms->update([
            'status' => $request->status,
        ]);

        if($all_forms->status == 'endorsement') {
            if($all_forms->model->control_number == NULL){
                

                $all_forms->model->update([
                    'control_number' => $psst_item['control_number'],
                    'date_submitted' => $request->date_submitted,

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
        ]);

        DB::table('gate_pass_items')->where('gate_pass_id', $all_forms->model->id)->delete();

        if(!empty($gate_item)){
            foreach ($gate_item['items'] as $key => $items){
                $gate_items = new GatePassItem([
                    'gate_pass_id' => $all_forms->model->id,
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
            
            $all_forms->model->update([
                'path' => 'uploads/gate-pass-images/' . $imageName,
                'image' => $imageName,
            ]);
        }

        
        $all_forms->update([
            'status' => $request->status,
        ]);

        if($all_forms->status == 'approval') {
            if($all_forms->model->control_number == NULL){

                $all_forms->model->update([
                    'control_number' => $gate_item['control_number'],
                    'date_submitted' => $request->date_submitted,

                ]);

            }

            $approvers = User::whereIn('id', $all_forms->approver ?? [])->get();

            if ($approvers->isNotEmpty()) {
                Notification::send($approvers, new SubmitFormNotification($all_forms));
            }

            // $all_forms->approved->notify(new SubmitFormNotification($all_forms));
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

        $rfp_item = Session::get('rfp_item');

        $all_forms->model->update([
            'form_id' => $all_forms->form->id,
            'company_id' => $request->company_id,
            'payable' => $request->payable,
            'amount' => $request->amount,
            'cost_center' => $request->cost_center,
            'department_id' => $request->department_id,
            'purpose' => $request->purpose,
            'instructions' => $request->instructions,
            'currency' => $request->currency,
        ]);

        if(!empty($request->file_name)) {
            $request->validate([
                'file_name' => 'required|mimes:pdf|max:5120',
            ]);

            $path = NULL;
            $nameWithExtension = $request->file_name->getClientOriginalName();

            $path = FileSavingHelper::saveFile($request->file_name, $all_forms->model->id, 'rfp-attachments');

            $all_forms->model->update([
                'path' => $path,
                'file_name' => $nameWithExtension,
            ]);
        }

        $all_forms->update([
            'status' => $request->status,
        ]);
        


        if($all_forms->status == 'approval') {
            if($all_forms->model->control_number == NULL){

                $all_forms->model->update([
                    'control_number' => $rfp_item['control_number'],
                    'date_submitted' => $request->date_submitted,

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

    public function update_rca($id, RCAUpdateRequest $request)
    {
        $user = Auth::user();
        $all_forms = AllForm::findOrFail(decrypt($id));
        $date_code = date('Y');

        $rca_item = Session::get('rca_item');

        $all_forms->model->update([
            'form_id' => $all_forms->form->id,
            'company_id' => $request->company_id,
            'name' => $request->name,
            'total_amount' => $rca_item['total_amount'],
            'cost_center' => $request->cost_center,
            'purpose' => $request->purpose,
            'travel' => $request->travel,
            'rca_date' => $request->rca_date,
            'itenerary' => $request->itenerary,
            'location' => $request->location,
        ]);

        DB::table('rca_form_items')->where('rca_form_id', $all_forms->model->id)->delete();

        if(!empty($rca_item)){
            foreach ($rca_item['items'] as $key => $items){
                $rca_items = new RequestCashItem([
                    'rca_form_id' => $all_forms->model->id,
                    'item_description' => $items['desc'],
                    'amount' =>  $items['amount'],
                    'days' =>  $items['days'],
                    'remarks' =>  $items['remarks'],
                ]);
                $rca_items->save();
            }
        }

        $all_forms->update([
            'status' => $request->status,
        ]);


        if($all_forms->status == 'confirmation') {
            if($all_forms->model->control_number == NULL){

                $all_forms->model->update([
                    'control_number' => $rca_item['control_number'],
                    'date_submitted' => $request->date_submitted,

                ]);
            }
            $all_forms->admin->notify(new SubmitFormNotification($all_forms));
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

    public function update_lca($id, LCAUpdateRequest $request)
    {
        $user = Auth::user();
        $all_forms = AllForm::findOrFail(decrypt($id));
        $date_code = date('Y');

        $lca_item = Session::get('lca_item');

        $all_forms->model->update([
            'form_id' => $all_forms->form->id,
            'company_id' => $request->company_id,
            'rca_form_id' => $request->rca_form_id,
            'total_amount' => $lca_item['total_amount'],
            'balance' => $lca_item['balance'],
        ]);

        DB::table('lca_form_items')->where('lca_form_id', $all_forms->model->id)->delete();

        if(!empty($lca_item)){
            foreach ($lca_item['items'] as $key => $items){
                $lca_items = new LiquidCashItem([
                    'lca_form_id' => $all_forms->model->id,
                    'date' =>  $items['date'],
                    'item_description' => $items['desc'],
                    'area' =>  $items['area'],
                    'amount' =>  $items['amount'],
                ]);
                $lca_items->save();
            }
        }

        if(!empty($request->file_name)) {
            $request->validate([
                'file_name' => 'required|mimes:pdf|max:5120',
            ]);

            $path = NULL;
            $nameWithExtension = $request->file_name->getClientOriginalName();

            $path = FileSavingHelper::saveFile($request->file_name, $all_forms->model->id, 'lca-receipts');

            $all_forms->model->update([
                'path' => $path,
                'file_name' => $nameWithExtension,
            ]);
        }

        $all_forms->update([
            'status' => $request->status,
        ]);


        if($all_forms->status == 'endorsement') {
            if($all_forms->model->control_number == NULL){

                $all_forms->model->update([
                    'control_number' => $lca_item['control_number'],
                    'date_submitted' => $request->date_submitted,

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

    public function update_pca($id, PCAUpdateRequest $request)
    {
        $user = Auth::user();
        $all_forms = AllForm::findOrFail(decrypt($id));
        $date_code = date('Y');

        $pca_item = Session::get('pca_item');

        $all_forms->model->update([
            'form_id' => $all_forms->form->id,
            'company_id' => $request->company_id,
            'name' => $request->name,
            'total_amount' => $pca_item['total_amount'],
            'cost_center' => $request->cost_center,
        ]);

        DB::table('pca_form_items')->where('pca_form_id', $all_forms->model->id)->delete();

        if(!empty($pca_item)){
            foreach ($pca_item['items'] as $key => $items){
                $pca_items = new PettyCashItem([
                    'pca_form_id' => $all_forms->model->id,
                    'item_description' => $items['desc'],
                    'amount' =>  $items['amount'],
                ]);
                $pca_items->save();
            }
        }

        $all_forms->update([
            'status' => $request->status,
        ]);


        if($all_forms->status == 'approval') {
            if($all_forms->model->control_number == NULL){

                $all_forms->model->update([
                    'control_number' => $pca_item['control_number'],
                    'date_submitted' => $request->date_submitted,

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
    
    public function update_pcl($id, PCLUpdateRequest $request)
    {
        $user = Auth::user();
        $all_forms = AllForm::findOrFail(decrypt($id));
        $date_code = date('Y');

        $pcl_item = Session::get('pcl_item');

        $all_forms->model->update([
            'form_id' => $all_forms->form->id,
            'company_id' => $request->company_id,
            'pca_form_id' => $request->pca_form_id,
            'total_amount' => $pcl_item['total_amount'],
            'balance' => $pcl_item['balance'],
        ]);

        DB::table('pcl_form_items')->where('pcl_form_id', $all_forms->model->id)->delete();

        if(!empty($pcl_item)){
            foreach ($pcl_item['items'] as $key => $items){
                $pcl_items = new PettyLiquidItem([
                    'pcl_form_id' => $all_forms->model->id,
                    'item_description' => $items['desc'],
                    'amount' =>  $items['amount'],
                ]);
                $pcl_items->save();
            }
        }

        if(!empty($request->file_name)) {
            $request->validate([
                'file_name' => 'required|mimes:pdf|max:5120',
            ]);

            $path = NULL;
            $nameWithExtension = $request->file_name->getClientOriginalName();

            $path = FileSavingHelper::saveFile($request->file_name, $all_forms->model->id, 'pcl-receipts');

            $all_forms->model->update([
                'path' => $path,
                'file_name' => $nameWithExtension,
            ]);
        }

        $all_forms->update([
            'status' => $request->status,
        ]);


        if($all_forms->status == 'approval') {
            if($all_forms->model->control_number == NULL){

                $all_forms->model->update([
                    'control_number' => $pcl_item['control_number'],
                    'date_submitted' => $request->date_submitted,

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
