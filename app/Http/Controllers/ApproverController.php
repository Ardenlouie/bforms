<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Category;
use App\Models\Form;
use App\Models\GatePass;
use App\Models\ProductSample;
use App\Models\AllForm;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;

use App\Http\Requests\AllFormUpdateRequest;
use App\Http\Requests\AllFormCheckRequest;

use App\Notifications\SubmitFormNotification;
use App\Notifications\ApproveFormNotification;
use App\Notifications\DeclineFormNotification;

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
                $query->whereJsonContains('approver', $user_id)
                    ->orWhere('endorser', $user_id)
                    ->orWhere('admin_id', $user_id)
                    ->orWhere('processor', $user_id);
            })
            ->where('status', '!=', 'draft')
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

    public function approve($id, AllFormUpdateRequest $request)
    {
        $all_forms = AllForm::findOrFail(decrypt($id)); 

        $user = Auth::user();

        if($all_forms->admin_id == $user->id && $request->status == 'endorsement'){
            $date_confirmed = date('Y-m-d');

            $all_forms->update([
                'date_confirmed' => $date_confirmed,
            ]);
            $all_forms->endorsed->notify(new SubmitFormNotification($all_forms));
    
        } elseif ($all_forms->admin_id == $user->id && $request->status == 'declined') {

            $all_forms->update([
                'remarks' => $request->remarks,
            ]);

            $all_forms->user->notify(new DeclineFormNotification($all_forms));

        }

        if($all_forms->endorser == $user->id && $request->status == 'approval'){
            $date_endorsed = date('Y-m-d');

            $all_forms->update([
                'date_endorsed' => $date_endorsed,
            ]);
            $all_forms->approved->notify(new SubmitFormNotification($all_forms));
    
        } elseif ($all_forms->endorser == $user->id && $request->status == 'declined') {

            $all_forms->update([
                'remarks' => $request->remarks,
                'date_confirmed' => null,
            ]);

            $all_forms->user->notify(new DeclineFormNotification($all_forms));

        }

        if(in_array($user->id, $all_forms->approver ?? []) && $request->status == 'approved'){
            $date_approved = date('Y-m-d');

            $all_forms->update([
                'date_approved' => $date_approved,
                'signed_id' => $user->id,
            ]);
            $all_forms->user->notify(new ApproveFormNotification($all_forms));

           

        } elseif ($all_forms->approver == $user->id && $request->status == 'declined') {

            $all_forms->update([
                'remarks' => $request->remarks,
                'date_endorsed' => null,
            ]);

            $all_forms->user->notify(new DeclineFormNotification($all_forms));
            
        } elseif ($all_forms->approver == $user->id && $request->status == 'processing') {

            $all_forms->update([
                'processor' => $request->processor,
            ]);

            $all_forms->processed->notify(new SubmitFormNotification($all_forms));
            
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

            return redirect()->route('approver.show', encrypt($all_forms->id))->with([
                'message_success' => $form_name.' ['.$control_number.'] was declined'
            ]);
        } elseif ($request->status == 'processing'){
            activity('assigned')
            ->performedOn($all_forms)
            ->log(':causer.name has assigned '.$form_name.' ['.$control_number.'] for processing');

            return redirect()->route('approver.show', encrypt($all_forms->id))->with([
                'message_success' => $form_name.' ['.$control_number.'] was assigned'
            ]);
        } else {
            activity('approved')
            ->performedOn($all_forms)
            ->log(':causer.name has approve '.$form_name.' ['.$control_number.']');

            return redirect()->route('approver.show', encrypt($all_forms->id))->with([
                'message_success' => $form_name.' ['.$control_number.'] was approved'
            ]);
        }
    }

    public function check($id, AllFormCheckRequest $request) 
    {
        $all_forms = AllForm::findOrFail(decrypt($id)); 

        $all_forms->update([
            'status' => $request->status,
        ]);

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        if($all_forms->form_id == 9){
            $gate = GatePass::where('id', $all_forms->model_id)->first();
            $all_forms = AllForm::where('model_id', $gate->psrf_form_id)
            ->where('model_type', 'App\Models\ProductSample')->first(); 
            
            $all_forms->update([
                'status' => 'checked',
            ]);
        }

        activity('checked')
            ->performedOn($all_forms)
            ->log('Security has check '.$form_name.' ['.$control_number.']');

        return redirect()->route('home')->with([
            'message_success' => $form_name.' ['.$control_number.'] was checked'
        ]);
    }
}
