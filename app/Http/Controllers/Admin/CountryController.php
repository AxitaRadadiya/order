<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        return redirect()->route('master.index', ['tab' => 'country']);
    }

    public function create()
    {
        return redirect()->route('master.index', ['tab' => 'country']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
        ]);

        Country::create($validated);

        return redirect()->route('master.index', ['tab' => 'country'])
            ->with('success', 'Country created successfully.');
    }

    public function show(Country $country)
    {
        return redirect()->route('master.index', ['tab' => 'country']);
    }

    public function edit(Country $country)
    {
        return redirect()->route('master.index', ['tab' => 'country']);
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
        ]);

        $country->update($validated);

        return redirect()->route('master.index', ['tab' => 'country'])
            ->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('master.index', ['tab' => 'country'])
            ->with('success', 'Country deleted successfully.');
    }

    public function list(Request $request)
    {
        // Column index map (matches DataTable columns array: id, name, action)
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'name', // action column — fallback sort by name
        ];

        $totalData     = Country::count();
        $limit         = $request->input('length');
        $start         = $request->input('start');
        $columnIndex   = $request->input('order.0.column', 0);
        $order         = $columns[$columnIndex] ?? 'id';
        $dir           = $request->input('order.0.dir', 'asc');
        $search        = $request->input('search.value');

        $query = Country::query();

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $totalFiltered = $query->count();

        $countries = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        $i    = $start + 1;

        foreach ($countries as $country) {
            $actions = '<div class="btn-group">';

            if (auth()->user()) {
                $actions .= '<a href="#" data-id="' . $country->id . '" data-name="' . e($country->name) . '" 
                               class="btn btn-sm btn-info edit-country-date-modal" title="Edit">
                               <i class="fa fa-edit"></i>
                             </a>';
            }

            if (auth()->user()) {
                $actions .= '
                    <form action="' . route('country.destroy', $country->id) . '" method="POST" class="deleteForm d-inline">
                        ' . csrf_field() . '
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="deleteButton btn btn-sm btn-danger border-0" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>';
            }

            $actions .= '</div>';

            $data[] = [
                'id'     => $i,
                'name'   => e($country->name),
                'action' => $actions,
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
}