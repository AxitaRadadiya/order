<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index()
    {
        return redirect()->route('master.index', ['tab' => 'state']);
    }

    public function create()
    {
        return redirect()->route('master.index', ['tab' => 'state']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:states,name',
            'country_id' => 'required|exists:countries,id',
        ]);

        State::create([
            'name'       => $request->name,
            'country_id' => $request->country_id,
        ]);

        return redirect()->route('master.index', ['tab' => 'state'])
            ->with('success', 'State created successfully.');
    }

    public function show(State $state)
    {
        return redirect()->route('master.index', ['tab' => 'state']);
    }

    public function edit(State $state)
    {
        return redirect()->route('master.index', ['tab' => 'state']);
    }

    public function update(Request $request, State $state)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:states,name,' . $state->id,
            'country_id' => 'required|exists:countries,id',
        ]);

        $state->update([
            'name'       => $request->name,
            'country_id' => $request->country_id,
        ]);

        return redirect()->route('master.index', ['tab' => 'state'])
            ->with('success', 'State updated successfully.');
    }

    public function destroy(State $state)
    {
        $state->delete();

        return redirect()->route('master.index', ['tab' => 'state'])
            ->with('success', 'State deleted successfully.');
    }

    public function list(Request $request)
    {
        // Column index map: 0=Sr No.(id), 1=Country, 2=Name, 3=Action
        $columns = [
            0 => 'id',
            1 => 'country_id',
            2 => 'name',
            3 => 'name', // action fallback
        ];

        $totalData   = State::count();
        $limit       = $request->input('length');
        $start       = $request->input('start');
        $columnIndex = $request->input('order.0.column', 0);
        $order       = $columns[$columnIndex] ?? 'id';
        $dir         = $request->input('order.0.dir', 'asc');
        $search      = $request->input('search.value');

        $query = State::with('country');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('country', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $totalFiltered = $query->count();

        $states = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        $i    = $start + 1;

        foreach ($states as $state) {
            $actions = '<div class="btn-group">';

            if (auth()->user()) {
                $actions .= '<a href="#" '
                    . 'data-id="' . $state->id . '" '
                    . 'data-name="' . e($state->name) . '" '
                    . 'data-country_id="' . $state->country_id . '" '
                    . 'class="btn btn-sm btn-info edit-state-date-modal" title="Edit">'
                    . '<i class="fa fa-edit"></i></a>';
            }

            if (auth()->user()) {
                $actions .= '
                    <form action="' . route('state.destroy', $state->id) . '" method="POST" class="deleteForm d-inline">
                        ' . csrf_field() . '
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="deleteButton btn btn-sm btn-danger border-0" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>';
            }

            $actions .= '</div>';

            $data[] = [
                'id'         => $i,
                'country'    => e(optional($state->country)->name ?? '—'),
                'name'       => e($state->name),
                'action'     => $actions,
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