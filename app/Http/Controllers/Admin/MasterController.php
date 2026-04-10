<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function index()
    {
        return view('admin.master.index');
    }

    // placeholder endpoints could be added later for AJAX partials
}
