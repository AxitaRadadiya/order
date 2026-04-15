<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Group;
use App\Models\Item;
use App\Models\Size;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $items = Item::with(['category', 'group'])->orderBy('id', 'desc')->paginate(15);

        return view('admin.items.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.items.create', [
            'categories' => Category::orderBy('name')->get(),
            'groups' => Group::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->pluck('name')->toArray(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:items,sku',
            'sub_category' => 'nullable|string|max:255',
            'sub_group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'group_id' => 'nullable|exists:groups,id',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable|in:0,1',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $validated['status'] = (int) ($validated['status'] ?? 1);

        Item::create($validated);

        return redirect()->route('items.index')->withSuccess('Item created successfully.');
    }

    public function show(Item $item): View
    {
        $item->load(['category', 'group']);
        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        return view('admin.items.edit', [
            'item' => $item,
            'categories' => Category::orderBy('name')->get(),
            'groups' => Group::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->pluck('name')->toArray(),
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:items,sku,' . $item->id,
            'sub_category' => 'nullable|string|max:255',
            'sub_group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'group_id' => 'nullable|exists:groups,id',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable|in:0,1',
        ]);

        if ($request->hasFile('image')) {
            // delete old
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $validated['status'] = (int) ($validated['status'] ?? 1);

        $item->update($validated);

        return redirect()->route('items.index')->withSuccess('Item updated successfully.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return redirect()->route('items.index')->withSuccess('Item deleted.');
    }
    public function itemList(Request $request)
    {
        $query = Item::with(['category', 'group']);

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $search = $request->input('search.value');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $items = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];

        foreach ($items as $idx => $item) {
            $viewUrl = route('items.show', $item->id);
            $editUrl = route('items.edit', $item->id);
            $deleteUrl = route('items.destroy', $item->id);

            $actionHtml = '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a> ';
            $actionHtml .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a> ';
            $actionHtml .= '<form method="POST" action="' . $deleteUrl . '" style="display:inline-block;margin:0;padding:0;">';
            $actionHtml .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
            $actionHtml .= '<input type="hidden" name="_method" value="DELETE">';
            $actionHtml .= '<button type="submit" class="btn btn-sm btn-danger deleteButton" title="Delete"><i class="fas fa-trash"></i></button>';
            $actionHtml .= '</form>';

            $data[] = [
                'id' => $start + $idx + 1,
                'name' => $item->name,
                'sku' => $item->sku,
                'category' => optional($item->category)->name,
                'sub_category' => $item->sub_category,
                'group' => optional($item->group)->name,
                'sub_group' => $item->sub_group,
                'price' => $item->price,
                'status' => $item->status ? 'Active' : 'Inactive',
                'action' => $actionHtml,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

}
