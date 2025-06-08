<?php

namespace App\Http\Controllers\Web\Accounts;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $admins = User::role('admin')->get();

        // dd($admins);
        return view('apps.account.admin.index', compact('admins'));
    }
}
