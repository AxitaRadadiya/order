<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxMaster;

class TaxMasterController extends Controller
{
    public function index()
    {
        $taxMasters = TaxMaster::all();
        return redirect()->route('item-master.index', ['tab' => 'tax'])->with('taxMasters', $taxMasters);
    }

    public function create()
    {
        return redirect()->route('item-master.index', ['tab' => 'tax']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tax_name' => 'required|string|max:50',
            'tax_percentage' => 'required|numeric|min:0|max:100',
        ]);

        TaxMaster::create($request->only('tax_name', 'tax_percentage'));

        return redirect()->route('item-master.index', ['tab' => 'tax'])->with('success', 'Tax Master created successfully.');
    }

    public function edit(TaxMaster $taxMaster)
    {
        return redirect()->route('item-master.index', ['tab' => 'tax'])->with('taxMaster', $taxMaster);
    }

    public function update(Request $request, TaxMaster $taxMaster)
    {
        $request->validate([
            'tax_name' => 'required|string|max:50',
            'tax_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $taxMaster->update($request->only('tax_name', 'tax_percentage'));

        return redirect()->route('item-master.index', ['tab' => 'tax'])->with('success', 'Tax Master updated successfully.');
    }

    public function destroy(TaxMaster $taxMaster)
    {
        $taxMaster->delete();
        return redirect()->route('item-master.index', ['tab' => 'tax'])->with('success', 'Tax Master deleted successfully.');
    }

    public function list(Request $request)
    {
        $totalData = TaxMaster::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');

        $query = TaxMaster::query();
        if (!empty($search)) {
            $query->where('tax_name', 'like', "%{$search}%")
                ->orWhere('tax_percentage', 'like', "%{$search}%");
        }

        $totalFiltered = $query->count();

        $rows = $query->offset($start)
                    ->limit($limit)
                    ->orderBy('tax_percentage', 'asc')
                    ->get();

        $data = [];
        $i = $start + 1;

        foreach ($rows as $row) {
            $data[] = [
                'id' => $i,
                'tax_name' => e($row->tax_name),
                'tax_percentage' => $row->tax_percentage . '%',
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
