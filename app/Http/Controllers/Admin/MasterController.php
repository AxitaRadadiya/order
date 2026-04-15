<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        return view('admin.master.index', compact('countries', 'states', 'cities'));
    }

    // placeholder endpoints could be added later for AJAX partials
}
