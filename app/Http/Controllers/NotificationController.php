<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AllForm;

use App\Notifications\TestNotification;
use App\Notifications\SubmitFormNotification;
use App\Notifications\FollowUpFormNotification;

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
        auth()->user()->notify(new SubmitFormNotification());

        return back();
    }

    public function followUp($id) {
        $all_forms = AllForm::findOrFail(decrypt($id));

        if($all_forms->status == 'endorsement'){

            $all_forms->endorsed->notify(new FollowUpFormNotification($all_forms));

        } elseif($all_forms->status == 'approval'){

            $all_forms->approved->notify(new FollowUpFormNotification($all_forms));

        }

        return back();
    }
}
