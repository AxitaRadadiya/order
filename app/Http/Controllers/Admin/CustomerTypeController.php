<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerType;
use Illuminate\Http\Request;

class CustomerTypeController extends Controller
{
    public function index()
    {
        return redirect()->route('master.index', ['tab' => 'customerType']);
    }

    public function create()
    {
        return redirect()->route('master.index', ['tab' => 'customerType']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customer_types,name',
        ]);

        CustomerType::create(['name' => $validated['name']]);

        return redirect()->route('master.index', ['tab' => 'customerType'])->with('success', 'Customer Type created successfully.');
    }

    public function show(CustomerType $customerType)
    {
        return redirect()->route('master.index', ['tab' => 'customerType']);
    }

    public function edit(CustomerType $customerType)
    {
        return redirect()->route('master.index', ['tab' => 'customerType']);
    }

    public function update(Request $request, CustomerType $customerType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customer_types,name,' . $customerType->id,
        ]);

        $customerType->update(['name' => $validated['name']]);

        return redirect()->route('master.index', ['tab' => 'customerType'])->with('success', 'Customer Type updated successfully.');
    }

    public function destroy(CustomerType $customerType)
    {
        $customerType->delete();
        return redirect()->route('master.index', ['tab' => 'customerType'])->with('success', 'Customer Type deleted successfully.');
    }

    public function list(Request $request)
    {
        $totalData = CustomerType::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');

        $query = CustomerType::query();
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
              $actions .= '<a href="#" data-id="'.$row->id.'" data-name="'.e($row->name).'" class="btn btn-sm btn-info edit-customer-type-modal"><i class="fa fa-edit"></i></a>';
            }
            if (auth()->user()) {
                $actions .= '<form action="'.route('customer-type.destroy', $row->id).'" method="POST" class="deleteForm d-inline">'.csrf_field().'<input type="hidden" name="_method" value="DELETE"><button type="submit" class="deleteButton btn btn-sm btn-danger border-0"><i class="fa fa-trash"></i></button></form>';
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
