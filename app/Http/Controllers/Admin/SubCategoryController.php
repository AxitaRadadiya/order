<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        return redirect()->route('item-master.index', ['tab' => 'sub-category']);
    }

    public function create()
    {
        return redirect()->route('item-master.index', ['tab' => 'sub-category']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_categories,name',
            'code' => 'nullable|string|max:50',
            'category_id' => 'required|exists:categories,id',
        ]);

        SubCategory::create($validated);

        return redirect()->route('item-master.index', ['tab' => 'sub-category'])->with('success', 'SubCategory created successfully.');
    }

    public function show(SubCategory $subCategory)
    {
        return redirect()->route('item-master.index', ['tab' => 'sub-category']);
    }

    public function edit(SubCategory $subCategory)
    {
        return redirect()->route('item-master.index', ['tab' => 'sub-category']);
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_categories,name,' . $subCategory->id,
            'code' => 'nullable|string|max:50',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subCategory->update($validated);

        return redirect()->route('item-master.index', ['tab' => 'sub-category'])->with('success', 'SubCategory updated successfully.');
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();
        return redirect()->route('item-master.index', ['tab' => 'sub-category'])->with('success', 'SubCategory deleted successfully.');
    }

    public function list(Request $request)
    {
        $totalData = SubCategory::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');

        $query = SubCategory::query();
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
                $actions .= '<a href="#" data-id="'.$row->id.'" data-name="'.e($row->name).'" class="btn btn-sm btn-info edit-sub-category-date-modal"><i class="fa fa-edit"></i></a>';
            }
            if (auth()->user()) {
                $actions .= '<form action="'.route('sub-category.destroy', $row->id).'" method="POST" class="deleteForm d-inline">'.csrf_field().'<input type="hidden" name="_method" value="DELETE"><button type="submit" class="deleteButton btn btn-sm btn-danger border-0"><i class="fa fa-trash"></i></button></form>';
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
