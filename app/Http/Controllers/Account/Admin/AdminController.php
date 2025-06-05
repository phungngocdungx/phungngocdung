<?php

namespace App\Http\Controllers\Account\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $admins = User::role('admin')->get();

        // dd($admins);
        return view('apps.account.admin.index', compact('admins'));
    }
}
