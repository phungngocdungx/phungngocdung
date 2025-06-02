<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index(){
        return view('index');
    }

    public function pJM(){
        return view('dashboard/project-management');
    }

    public function product(){
        return view('apps/e-commerce/admin/product');
    }

    public function addProduct(){
        return view('apps/e-commerce/admin/add-product');
    }

    public function showEmail(){
        return view('apps/email/index');
    }
}
