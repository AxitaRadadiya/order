<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Setting;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        $terms = Setting::getValue('terms_and_conditions', '');

        return view('admin.master.index', compact('countries', 'states', 'cities', 'terms'));
    }

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'terms' => ['nullable', 'string'],
        ]);

        Setting::setValue('terms_and_conditions', $data['terms'] ?? '');

        return redirect()->route('master.index', ['tab' => 'terms'])
            ->with('success', 'Terms & Conditions saved successfully.');
    }

    // placeholder endpoints could be added later for AJAX partials
}
