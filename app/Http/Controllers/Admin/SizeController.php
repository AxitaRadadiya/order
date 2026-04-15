<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Models\Group;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        return redirect()->route('item-master.index', ['tab' => 'size']);
    }

    public function create()
    {
        return redirect()->route('item-master.index', ['tab' => 'size']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name',
        ]);

        Size::create(['name' => $validated['name']]);

        return redirect()->route('item-master.index', ['tab' => 'size'])->with('success', 'Size created successfully.');
    }

    public function show(Size $size)
    {
        return redirect()->route('item-master.index', ['tab' => 'size']);
    }

    public function edit(Size $size)
    {
        return redirect()->route('item-master.index', ['tab' => 'size']);
    }

    public function update(Request $request, Size $size)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $size->update(['name' => $validated['name']]);

        return redirect()->route('item-master.index', ['tab' => 'size'])->with('success', 'Size updated successfully.');
    }

    public function destroy(Size $size)
    {
        $size->delete();
        return redirect()->route('item-master.index', ['tab' => 'size'])->with('success', 'Size deleted successfully.');
    }

    public function list(Request $request)
    {
        $totalData = Size::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');

        $query = Size::query();
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
                $actions .= '<a href="#" data-id="'.$row->id.'" data-name="'.e($row->name).'" class="btn btn-sm btn-info edit-size-date-modal"><i class="fa fa-edit"></i></a>';
            }
            if (auth()->user()) {
                $actions .= '<form action="'.route('size.destroy', $row->id).'" method="POST" class="deleteForm d-inline">'.csrf_field().'<input type="hidden" name="_method" value="DELETE"><button type="submit" class="deleteButton btn btn-sm btn-danger border-0"><i class="fa fa-trash"></i></button></form>';
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
