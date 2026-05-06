<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Group;
use App\Models\Item;
use App\Models\Size;
use App\Models\SubCategory;
use App\Models\SubGroup;
use App\Models\Color;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        return view('admin.items.index');
    }

    public function create(): View
    {
        return view('admin.items.create', [
            'categories'     => Category::orderBy('name')->get(),
            'groups'         => Group::orderBy('name')->get(),
            'sub_categories' => SubCategory::orderBy('name')->get(),
            'sub_groups'     => SubGroup::orderBy('name')->get(),
            'colors'         => Color::orderBy('name')->get(),
            'sizes'          => Size::orderBy('name')->pluck('name')->toArray(),
            'generatedItemCode' => Item::generateSequentialCode(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'article_number'   => 'nullable|string|max:100|unique:items,article_number',
            'item_code'        => 'nullable|string|max:100|unique:items,item_code',
            'sub_category'     => 'nullable|exists:sub_categories,id',
            'sub_group'        => 'nullable|exists:sub_groups,id',
            'colors'           => 'nullable|array',
            'colors.*'         => 'nullable|exists:colors,id',
            'sizes'            => 'nullable|array',
            'description'      => 'nullable|string',
            'category_id'      => 'nullable|exists:categories,id',
            'group_id'         => 'nullable|exists:groups,id',
            'unit'             => 'nullable|string|max:50',
            'price'            => 'required|numeric|min:0',
            'tax_percent'      => 'nullable|numeric|min:0',
            // images[] — up to 5 files, jpg/png only, max 2 MB each
            'images'           => 'nullable|array|max:5',
            'images.*'         => 'image|mimes:jpg,jpeg,png|max:2048',
            'status'           => 'nullable|in:0,1',
            'show_item_on_web' => 'nullable|in:0,1',
        ]);

        // Handle multiple images (up to 5)
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('items', 'public');
            }
        }
        if (!empty($imagePaths)) {
            // Store as JSON array; keep first image in legacy `image` column too
            // determine primary image requested by client
            $primary = $request->input('primary_image');
            $primaryPath = null;

            if ($primary && str_starts_with($primary, 'new-')) {
                $idx = (int) Str::after($primary, 'new-');
                if (isset($imagePaths[$idx])) {
                    $primaryPath = $imagePaths[$idx];
                }
            }

            // fallback to first uploaded image
            $primaryPath = $primaryPath ?? $imagePaths[0];

            $validated['image'] = $primaryPath;
            // Only include `images` if the column exists (migration may not have run)
            if (Schema::hasColumn('items', 'images')) {
                $validated['images'] = $imagePaths;   // cast to array in model
            }
        }

        // Auto-generate item_code when not provided
        if (empty($validated['item_code'])) {
            $validated['item_code'] = Item::generateSequentialCode();
        }

        $validated['status']           = (int) ($validated['status'] ?? 1);
        $validated['show_item_on_web'] = (int) ($validated['show_item_on_web'] ?? 1);

        // Extract colors (pivot) before creating — no `colors` column on items table
        $colors = $validated['colors'] ?? null;
        unset($validated['colors']);

        $item = Item::create($validated);

        if (!empty($colors)) {
            $item->colors()->sync($colors);
        }

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
            'item'           => $item,
            'categories'     => Category::orderBy('name')->get(),
            'groups'         => Group::orderBy('name')->get(),
            'sub_categories' => SubCategory::orderBy('name')->get(),
            'sub_groups'     => SubGroup::orderBy('name')->get(),
            'colors'         => Color::orderBy('name')->get(),
            'sizes'          => Size::orderBy('name')->pluck('name')->toArray(),
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'article_number'   => 'nullable|string|max:100|unique:items,article_number,' . $item->id,
            'item_code'        => 'nullable|string|max:100|unique:items,item_code,' . $item->id,
            'sub_category'     => 'nullable|exists:sub_categories,id',
            'sub_group'        => 'nullable|exists:sub_groups,id',
            'colors'           => 'nullable|array',
            'colors.*'         => 'nullable|exists:colors,id',
            'sizes'            => 'nullable|array',
            'description'      => 'nullable|string',
            'category_id'      => 'nullable|exists:categories,id',
            'group_id'         => 'nullable|exists:groups,id',
            'unit'             => 'nullable|string|max:50',
            'price'            => 'required|numeric|min:0',
            'tax_percent'      => 'nullable|numeric|min:0',
            // images[] — up to 5 files, jpg/png only, max 2 MB each
            'images'           => 'nullable|array|max:5',
            'images.*'         => 'image|mimes:jpg,jpeg,png|max:2048',
            'status'           => 'nullable|in:0,1',
            'show_item_on_web' => 'nullable|in:0,1',
        ]);

        // Handle new image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            // Delete old images only if user uploaded new ones and intends to replace
            $oldImages = is_array($item->images) ? $item->images : ($item->image ? [$item->image] : []);
            foreach ($oldImages as $old) {
                Storage::disk('public')->delete($old);
            }

            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('items', 'public');
            }

            // determine primary image
            $primary = $request->input('primary_image');
            $primaryPath = null;
            if ($primary && str_starts_with($primary, 'new-')) {
                $idx = (int) Str::after($primary, 'new-');
                if (isset($imagePaths[$idx])) {
                    $primaryPath = $imagePaths[$idx];
                }
            }

            $primaryPath = $primaryPath ?? ($imagePaths[0] ?? null);

            if ($primaryPath) {
                $validated['image'] = $primaryPath;
            }

            if (Schema::hasColumn('items', 'images')) {
                $validated['images'] = $imagePaths;
            }
        } else {
            // no new uploads — allow selecting primary among existing images
            $primary = $request->input('primary_image');
            if ($primary && !str_starts_with($primary, 'new-')) {
                $validated['image'] = $primary;
            }
        }

        // Extract colors (pivot) before updating — no `colors` column on items table
        $colors = $validated['colors'] ?? null;
        unset($validated['colors']);

        $validated['status']           = (int) ($validated['status'] ?? 1);
        $validated['show_item_on_web'] = (int) ($validated['show_item_on_web'] ?? 1);

        $item->update($validated);

        if (is_array($colors)) {
            $item->colors()->sync($colors);
        }

        return redirect()->route('items.index')->withSuccess('Item updated successfully.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $oldImages = is_array($item->images) ? $item->images : ($item->image ? [$item->image] : []);
        foreach ($oldImages as $old) {
            Storage::disk('public')->delete($old);
        }

        $item->delete();

        return redirect()->route('items.index')->withSuccess('Item deleted.');
    }

    public function catalog()
     {
         $items = Item::with(['category', 'group', 'colors'])
                      ->where('status', 1)
                      ->where('show_item_on_web', 1)
                      ->latest()
                      ->paginate(12);
         return view('admin.catalog.index', compact('items')); 
    }
    public function showCatalog(Item $item)
    {
        return view('admin.catalog.show', compact('item'));
    }   

    public function itemList(Request $request)
    {
        // Eager-load relationships so ->category->name etc. always resolves
        $query = Item::with(['category', 'group', 'colors']);

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);
        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('article_number', 'like', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $items = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];

        foreach ($items as $idx => $item) {
            $viewUrl   = route('items.show', $item->id);
            $editUrl   = route('items.edit', $item->id);
            $deleteUrl = route('items.destroy', $item->id);

            // Status badge with colour coding
            $statusBadge = $item->status
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>';

            // Sizes: stored as array, display comma-separated
            $sizesDisplay = '';
            if (!empty($item->sizes)) {
                $arr = is_array($item->sizes) ? $item->sizes : explode(',', $item->sizes);
                $sizesDisplay = implode(', ', array_map('trim', $arr));
            }

            $actions = '<div class="btn-group" style="position: relative; left: 10px;">
                <button type="button" class="btn btn-sm btn-info " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Actions">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu action-dropdown" role="menu">';

            $actions .= '<a class="dropdown-item" href="' . $viewUrl . '">View</a>';
            $actions .= '<a class="dropdown-item" href="' . $editUrl . '">Edit</a>';
            $actions .= '
                <form method="POST" action="' . $deleteUrl . '" style="display:inline;">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="dropdown-item deleteButton">Delete</button>
                </form>
            ';
            $actions .= '</div></div>';

            $data[] = [
                'id'             => $start + $idx + 1,
                'name'           => $item->name,
                'article_number' => $item->article_number ?? '-',
                'category'       => optional($item->category)->name ?? '-',
                'group'          => optional($item->group)->name   ?? '-',
                'sizes'          => $sizesDisplay ?: '-',
                'price'          => number_format((float) $item->price, 2),
                'status'         => $statusBadge,
                'action'         => $actions,
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    public function activeItemList(Request $request)
    {
        $query = Item::with(['category', 'group', 'colors'])
                     ->where('status', 1)
                     ->where('show_item_on_web', 1); // only items marked to show on web

        $items = $query->orderBy('name')->get()->map(function ($item) {
            $sizes = [];
            if (!empty($item->sizes)) {
                $sizes = is_array($item->sizes) ? $item->sizes : explode(',', $item->sizes);
                $sizes = array_map('trim', $sizes);
            }

            return [
                'id'             => $item->id,
                'name'           => $item->name,
                'article_number' => $item->article_number,
                'item_code'      => $item->item_code,
                'category'       => optional($item->category)->name,
                'group'          => optional($item->group)->name,
                'colors'         => $item->colors->pluck('name')->toArray(),
                'sizes'          => $sizes,
                'price'          => (float) $item->price,
                'tax_percent'    => (float) $item->tax_percent,
                'image_url'      => $item->image_url,
                'description'    => $item->description,
            ];
        });

        return response()->json(['data' => $items]);
    }
}