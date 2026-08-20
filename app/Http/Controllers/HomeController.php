<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\TestNotification;
use App\Models\AllForm;
use App\Models\Form;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function dashboard()
    {
        // 1. Total Count of all morph records
        $totalFormsCount = AllForm::count();

        // 2. Status Counts
        $pendingFormsCount   = AllForm::whereIn('status', ['pending', 'draft', 'endorsement', 'approval', 'confirm', 'confirming', 'confirmed'])->count();
        $approvedFormsCount  = AllForm::where('status', 'approved')->count();
        $checkedFormsCount  = AllForm::whereIn('status', ['checked', 'partially_released'])->count();
        $receivedFormsCount  = AllForm::where('status', 'received')->count();
        $liquidatedFormsCount  = AllForm::where('status', 'liquidated')->count();
        $cancelledFormsCount = AllForm::whereIn('status', ['cancelled', 'declined'])->count();

        // 3. Count forms grouped by form definition/type
        $formsBreakdown = Form::select('forms.id', 'forms.name', 'forms.prefix')
            ->selectRaw('COUNT(all_forms.id) as count')
            ->leftJoin('all_forms', 'forms.id', '=', 'all_forms.form_id')
            ->groupBy('forms.id', 'forms.name', 'forms.prefix')
            ->orderBy('count', 'DESC')
            ->get();

            // dd($formsBreakdown);

        return view('dashboard', compact(
            'totalFormsCount',
            'pendingFormsCount',
            'approvedFormsCount',
            'cancelledFormsCount',
            'checkedFormsCount',
            'receivedFormsCount',
            'liquidatedFormsCount',
            'formsBreakdown'
        ));
    }

    public function scanning()
    {

        
        return view('pages.security.index')->with([

        ]);
    }

    
}
