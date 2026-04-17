<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\SubGroup;
use Illuminate\Http\Request;

class SubGroupController extends Controller
{
    public function index()
    {
        return redirect()->route('item-master.index', ['tab' => 'sub-group']);
    }

    public function create()
    {
        return redirect()->route('item-master.index', ['tab' => 'sub-group']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_groups,name',
            'code' => 'nullable|string|max:50',
            'group_id' => 'required|exists:groups,id',
        ]);

        SubGroup::create($validated);

        return redirect()->route('item-master.index', ['tab' => 'sub-group'])->with('success', 'SubGroup created successfully.');
    }

    public function show(SubGroup $subGroup)
    {
        return redirect()->route('item-master.index', ['tab' => 'sub-group']);
    }

    public function edit(SubGroup $subGroup)
    {
        return redirect()->route('item-master.index', ['tab' => 'sub-group']);
    }

    public function update(Request $request, SubGroup $subGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_groups,name,' . $subGroup->id,
            'code' => 'nullable|string|max:50',
            'group_id' => 'required|exists:groups,id',
        ]);

        $subGroup->update($validated);

        return redirect()->route('item-master.index', ['tab' => 'sub-group'])->with('success', 'SubGroup updated successfully.');
    }

    public function destroy(SubGroup $subGroup)
    {
        $subGroup->delete();
        return redirect()->route('item-master.index', ['tab' => 'sub-group'])->with('success', 'SubGroup deleted successfully.');
    }

    public function list(Request $request)
    {
        $totalData = SubGroup::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');

        $query = SubGroup::query();
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
                $actions .= '<a href="#" data-id="'.$row->id.'" data-name="'.e($row->name).'" class="btn btn-sm btn-info edit-sub-group-date-modal"><i class="fa fa-edit"></i></a>';
            }
            if (auth()->user()) {
                $actions .= '<form action="'.route('sub-group.destroy', $row->id).'" method="POST" class="deleteForm d-inline">'.csrf_field().'<input type="hidden" name="_method" value="DELETE"><button type="submit" class="deleteButton btn btn-sm btn-danger border-0"><i class="fa fa-trash"></i></button></form>';
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
