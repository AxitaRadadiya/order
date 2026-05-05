<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SetController extends Controller
{
    public function index()
    {
        return redirect()->route('item-master.index', ['tab' => 'set']);
    }

    public function create()
    {
        return redirect()->route('item-master.index', ['tab' => 'set']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sets,name'],
            'size_ids' => ['required', 'array', 'min:1'],
            'size_ids.*' => ['integer', 'exists:sizes,id'],
        ]);

        $set = Set::create(['name' => $validated['name']]);
        $set->sizes()->sync($validated['size_ids']);

        return redirect()->route('item-master.index', ['tab' => 'set'])->with('success', 'Set created successfully.');
    }

    public function show(Set $set)
    {
        return redirect()->route('item-master.index', ['tab' => 'set']);
    }

    public function edit(Set $set)
    {
        return redirect()->route('item-master.index', ['tab' => 'set']);
    }

    public function update(Request $request, Set $set)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('sets', 'name')->ignore($set->id)],
            'size_ids' => ['required', 'array', 'min:1'],
            'size_ids.*' => ['integer', 'exists:sizes,id'],
        ]);

        $set->update(['name' => $validated['name']]);
        $set->sizes()->sync($validated['size_ids']);

        return redirect()->route('item-master.index', ['tab' => 'set'])->with('success', 'Set updated successfully.');
    }

    public function destroy(Set $set)
    {
        $set->delete();

        return redirect()->route('item-master.index', ['tab' => 'set'])->with('success', 'Set deleted successfully.');
    }

    public function list(Request $request)
    {
        $totalData = Set::count();
        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $search = $request->input('search.value');

        $query = Set::with('sizes');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('sizes', function ($sizeQuery) use ($search) {
                        $sizeQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalFiltered = $query->count();
        $rows = $query->offset($start)->limit($limit)->orderBy('name')->get();

        $data = [];
        $i = $start + 1;
        foreach ($rows as $row) {
            $sizeIds = $row->sizes->pluck('id')->implode(',');
            $sizeNames = $row->sizes->pluck('name')->implode(', ');

            $actions = '<div class="btn-group">';
            if (auth()->user()) {
                $actions .= '<a href="#" data-id="'.$row->id.'" data-name="'.e($row->name).'" data-size-ids="'.e($sizeIds).'" class="btn btn-sm btn-info edit-set-date-modal"><i class="fa fa-edit"></i></a>';
            }
            if (auth()->user()) {
                $actions .= '<form action="'.route('set.destroy', $row->id).'" method="POST" class="deleteForm d-inline">'.csrf_field().'<input type="hidden" name="_method" value="DELETE"><button type="submit" class="deleteButton btn btn-sm btn-danger border-0"><i class="fa fa-trash"></i></button></form>';
            }
            $actions .= '</div>';

            $data[] = [
                'id' => $i,
                'name' => e($row->name),
                'sizes' => e($sizeNames),
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
