<?php

namespace App\Http\Controllers;

use App\Models\ExpenseAccount;
use Illuminate\Http\Request;

use App\Http\Requests\ExpenseAccountAddRequest;
use App\Http\Requests\ExpenseAccountEditRequest;

use App\Http\Traits\SettingTrait;

class ExpenseAccountController extends Controller
{
    use SettingTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $search = trim($request->get('search'));
        
        $expense_accounts = ExpenseAccount::orderBy('id', 'ASC')
            ->when(!empty($search), function($query) use($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->paginate($this->getDataPerPage())
            ->appends(request()->query());

        return view('pages.expense_accounts.index')->with([
            'search' => $search,
            'expense_accounts' => $expense_accounts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {


        return view('pages.expense_accounts.create')->with([

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseAccountAddRequest $request) {

        $expense_account = new ExpenseAccount([
            'ledger_code' => $request->ledger_code,
            'name' => $request->name,
        ]);
        $expense_account->save();

        // logs
        activity('created')
            ->performedOn($expense_account)
            ->log(':causer.name has created expense account :subject.name');

        return redirect()->route('expense_account.index')->with([
            'message_success' => __('Expense Account '.$expense_account->name.' was created')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseAccount $expenseAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) {
        $expense_account = ExpenseAccount::findOrFail(decrypt($id));

        return view('pages.expense_accounts.edit')->with([
            'expense_account' => $expense_account,

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExpenseAccountEditRequest $request, $id) {
        $expense_account = ExpenseAccount::findOrFail(decrypt($id));

        $changes_arr['old'] = $expense_account->getOriginal();

        $expense_account->update([
            'ledger_code' => $request->ledger_code,
            'name' => $request->name,

        ]);
        $expense_account->save();

        $changes_arr['changes'] = $expense_account->getChanges();

        // logs
        activity('updated')
            ->performedOn($expense_account)
            ->withProperties($changes_arr)
            ->log(':causer.name has updated expense account :subject.name');

        return back()->with([
            'message_success' => __('Expense Account '.$expense_account->name.' was updated')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseAccount $expenseAccount)
    {
        //
    }
}
