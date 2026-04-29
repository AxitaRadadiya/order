<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        return redirect()->route('item-master.index', ['tab' => 'color']);
    }

    public function create()
    {
        return redirect()->route('item-master.index', ['tab' => 'color']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
        ]);

        Color::create(['name' => $validated['name']]);

        return redirect()->route('item-master.index', ['tab' => 'color'])->with('success', 'Color created successfully.');
    }

    public function show(Color $color)
    {
        return redirect()->route('item-master.index', ['tab' => 'color']);
    }

    public function edit(Color $color)
    {
        return redirect()->route('item-master.index', ['tab' => 'color']);
    }

    public function update(Request $request, Color $color)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:colors,name,' . $color->id,
        ]);

        $color->update(['name' => $validated['name']]);

        return redirect()->route('item-master.index', ['tab' => 'color'])->with('success', 'Color updated successfully.');
    }

    public function destroy(Color $color)
    {
        $color->delete();
        return redirect()->route('item-master.index', ['tab' => 'color'])->with('success', 'Color deleted successfully.');
    }

    public function list(Request $request)
    {
        $totalData = Color::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');

        $query = Color::query();
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $totalFiltered = $query->count();

        $rows = $query->offset($start)->limit($limit)->orderBy('name')->get();

        $data = [];
        $i = $start + 1;
        foreach ($rows as $row) {
            $actions = '<div class="btn-group">';
            if (auth()->user()) {
                $actions .= '<a href="#" data-id="'.$row->id.'" data-name="'.e($row->name).'" class="btn btn-sm btn-info edit-color-date-modal"><i class="fa fa-edit"></i></a>';
            }
            if (auth()->user()) {
                $actions .= '<form action="'.route('color.destroy', $row->id).'" method="POST" class="deleteForm d-inline">'.csrf_field().'<input type="hidden" name="_method" value="DELETE"><button type="submit" class="deleteButton btn btn-sm btn-danger border-0"><i class="fa fa-trash"></i></button></form>';
            }
            $actions .= '</div>';

            $data[] = [
                'id' => $i,
                'name' => e($row->name),
                'action' => $actions,
            ];
            $i++;
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }
}
