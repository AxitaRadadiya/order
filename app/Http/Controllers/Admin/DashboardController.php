<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\LeadSource;
use App\Models\ProformaInvoice;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
       

        return view('admin.dashboard'
        );
    }
}
