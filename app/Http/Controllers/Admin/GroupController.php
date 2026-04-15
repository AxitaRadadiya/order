<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Category;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        return redirect()->route('item-master.index', ['tab' => 'group']);
    }

    public function create()
    {
        return redirect()->route('item-master.index', ['tab' => 'group']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
        ]);

        Group::create(['name' => $validated['name']]);

        return redirect()->route('item-master.index', ['tab' => 'group'])->with('success', 'Group created successfully.');
    }

    public function show(Group $group)
    {
        return redirect()->route('item-master.index', ['tab' => 'group']);
    }

    public function edit(Group $group)
    {
        return redirect()->route('item-master.index', ['tab' => 'group']);
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
        ]);

        $group->update(['name' => $validated['name']]);

        return redirect()->route('item-master.index', ['tab' => 'group'])->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('item-master.index', ['tab' => 'group'])->with('success', 'Group deleted successfully.');
    }

    public function list(Request $request)
    {
        $totalData = Group::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');

        $query = Group::query();
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
                $actions .= '<a href="#" data-id="'.$row->id.'" data-name="'.e($row->name).'" class="btn btn-sm btn-info edit-group-date-modal"><i class="fa fa-edit"></i></a>';
            }
            if (auth()->user()) {
                $actions .= '<form action="'.route('group.destroy', $row->id).'" method="POST" class="deleteForm d-inline">'.csrf_field().'<input type="hidden" name="_method" value="DELETE"><button type="submit" class="deleteButton btn btn-sm btn-danger border-0"><i class="fa fa-trash"></i></button></form>';
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
