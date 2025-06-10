<?php

namespace App\Http\Controllers\Web\Accounts;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManageController extends Controller
{
    public function index(Request $request)
    {
        $managers = User::role('manage')->get();

        // dd($managers);
        return view('apps.account.manage.index', compact('managers'));
    }
}
