<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AllForm;
use App\Models\User;

use App\Notifications\TestNotification;
use App\Notifications\SubmitFormNotification;
use App\Notifications\FollowUpFormNotification;

use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    public function index(Request $request) {
        $search = trim($request->get('search'));

        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'DESC')
            ->when(!empty($search), function($query) use($search) {
                $query->where('data', 'LIKE', '%'.$search.'%');
            })
            ->paginate(10)
            ->onEachSide(1);

        return view('notifications')->with([
            'notifications' => $notifications,
            'search' => $search
        ]);
    }

    public function testNotification() {
        // auth()->user()->notify(new SubmitFormNotification());
        $all_forms = AllForm::where('id', 49)->first();
            
        $approved = [12, 13];

        $approvers = User::whereIn('id', $approved ?? [])->get();

        if ($approvers->isNotEmpty()) {
            Notification::send($approvers, new SubmitFormNotification($all_forms));
        }

        return back();
    }

    public function followUp($id) {
        $all_forms = AllForm::findOrFail(decrypt($id));

        if($all_forms->status == 'endorsement'){

            // $all_forms->endorsed->notify(new FollowUpFormNotification($all_forms));

            $endorsers = User::whereIn('id', $all_forms->endorser ?? [])->get();

            if ($endorsers->isNotEmpty()) {
                Notification::send($endorsers, new FollowUpFormNotification($all_forms));
            }

        } elseif($all_forms->status == 'approval'){

            $approvers = User::whereIn('id', $all_forms->approver ?? [])->get();

            if ($approvers->isNotEmpty()) {
                Notification::send($approvers, new FollowUpFormNotification($all_forms));
            }
        } elseif($all_forms->status == 'confirming'){

            $brands = User::whereIn('id', $all_forms->bm_signs ?? [])->get();

            if ($brands->isNotEmpty()) {
                Notification::send($brands, new FollowUpFormNotification($all_forms));
            }
        } elseif($all_forms->status == 'confirmed'){

            $group_brands = User::whereIn('id', $all_forms->gbm_signs ?? [])->get();

            if ($group_brands->isNotEmpty()) {
                Notification::send($group_brands, new FollowUpFormNotification($all_forms));
            }
        } 

        activity('follow-up')
            ->performedOn($all_forms)
            ->log(':causer.name has follow up '.$all_forms->form->name.' ['.$all_forms->model->control_number.']');

        return back()->with([
            'message_success' => $all_forms->form->name.' ['.$all_forms->model->control_number.'] next approver/s has been notified.'
        ]);
    }
}
