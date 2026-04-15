<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        return redirect()->route('master.index', ['tab' => 'city']);
    }

    public function create()
    {
        return redirect()->route('master.index', ['tab' => 'city']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:cities,name',
            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
        ]);

        City::create([
            'name'       => $request->name,
            'country_id' => $request->country_id,
            'state_id'   => $request->state_id,
        ]);

        return redirect()->route('master.index', ['tab' => 'city'])
            ->with('success', 'City created successfully.');
    }

    public function show(City $city)
    {
        return redirect()->route('master.index', ['tab' => 'city']);
    }

    public function edit(City $city)
    {
        return redirect()->route('master.index', ['tab' => 'city']);
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:cities,name,' . $city->id,
            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
        ]);

        $city->update([
            'name'       => $request->name,
            'country_id' => $request->country_id,
            'state_id'   => $request->state_id,
        ]);

        return redirect()->route('master.index', ['tab' => 'city'])
            ->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('master.index', ['tab' => 'city'])
            ->with('success', 'City deleted successfully.');
    }

    public function list(Request $request)
    {
        // Column index map: 0=Sr No., 1=Country, 2=State, 3=Name, 4=Action
        $columns = [
            0 => 'id',
            1 => 'country_id',
            2 => 'state_id',
            3 => 'name',
            4 => 'name', // action fallback
        ];

        $totalData   = City::count();
        $limit       = $request->input('length');
        $start       = $request->input('start');
        $columnIndex = $request->input('order.0.column', 0);
        $order       = $columns[$columnIndex] ?? 'id';
        $dir         = $request->input('order.0.dir', 'asc');
        $search      = $request->input('search.value');

        $query = City::with(['country', 'state']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('country', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('state',   fn($q3) => $q3->where('name', 'like', "%{$search}%"));
            });
        }

        $totalFiltered = $query->count();

        $cities = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        $i    = $start + 1;

        foreach ($cities as $city) {
            $actions = '<div class="btn-group">';

            if (auth()->user()) {
                $actions .= '<a href="#" '
                    . 'data-id="' . $city->id . '" '
                    . 'data-name="' . e($city->name) . '" '
                    . 'data-country_id="' . $city->country_id . '" '
                    . 'data-state_id="' . $city->state_id . '" '
                    . 'class="btn btn-sm btn-info edit-city-date-modal" title="Edit">'
                    . '<i class="fa fa-edit"></i></a>';
            }

            if (auth()->user()) {
                $actions .= '
                    <form action="' . route('city.destroy', $city->id) . '" method="POST" class="deleteForm d-inline">
                        ' . csrf_field() . '
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="deleteButton btn btn-sm btn-danger border-0" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>';
            }

            $actions .= '</div>';

            $data[] = [
                'id'      => $i,
                'country' => e(optional($city->country)->name ?? '—'),
                'state'   => e(optional($city->state)->name ?? '—'),
                'name'    => e($city->name),
                'action'  => $actions,
            ];

            $i++;
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data'            => $data,
        ]);
    }

    /**
     * AJAX: return states filtered by country_id (used in City modal dropdown)
     */
    public function getStatesByCountry(Request $request)
    {
        $states = State::where('country_id', $request->country_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($states);
    }
}