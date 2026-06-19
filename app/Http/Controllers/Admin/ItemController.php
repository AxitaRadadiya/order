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
use App\Models\TaxMaster; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\DB;
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
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('item-create')) {
            abort(403);
        }
        return view('admin.items.create', [
            'categories'     => Category::orderBy('name')->get(),
            'groups'         => Group::orderBy('name')->get(),
            'sub_categories' => SubCategory::orderBy('name')->get(),
            'sub_groups'     => SubGroup::orderBy('name')->get(),
            'colors'         => Color::orderBy('name')->get(),
            'sizes'          => Size::orderBy('name')->get(),
            'generatedItemCode' => Item::generateSequentialCode(),
            'taxes'          => TaxMaster::orderBy('tax_percentage')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('item-create')) {
            abort(403);
        }
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'article_number'   => 'nullable|string|max:100|unique:items,article_number',
            'item_code'        => 'nullable|string|max:100|unique:items,item_code',
            'sub_category'     => 'nullable|exists:sub_categories,id',
            'sub_group'        => 'nullable|exists:sub_groups,id',
            // 'colors'           => 'nullable|array',
            // 'colors.*'         => 'nullable|exists:colors,id',
            // 'sizes'            => 'nullable|array',
            'variants' => 'required|array|min:1',
            'variants.*.color_id' => 'required|exists:colors,id',
            'variants.*.size_id' => 'required|exists:sizes,id',
            'variants.*.quantity' => 'required|integer|min:0',
            'description'      => 'nullable|string',
            'category_id'      => 'nullable|exists:categories,id',
            'group_id'         => 'nullable|exists:groups,id',
            'unit'             => 'nullable|string|max:50',
            'price'            => 'required|numeric|min:0',
            'tax_id'           => 'nullable|exists:tax_masters,id',
            'video_link'       => 'nullable|url',
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

        // Keep tax_id and tax_percent synchronized based on selected tax
        $tax = !empty($validated['tax_id']) ? TaxMaster::find($validated['tax_id']) : null;
        $validated['tax_id'] = $validated['tax_id'] ?? null;
        $validated['tax_percent'] = $tax ? $tax->tax_percentage : 0;

        $validated['status']           = (int) ($validated['status'] ?? 1);
        $validated['show_item_on_web'] = (int) ($validated['show_item_on_web'] ?? 1);

        // Extract colors (pivot) before creating — no `colors` column on items table
        // $colors = $validated['colors'] ?? null;
        // unset($validated['colors']);

        $variants = $validated['variants'] ?? [];

        $combinations = [];

        foreach ($variants as $variant) {

            $key = $variant['color_id'] . '_' . $variant['size_id'];

            if (in_array($key, $combinations)) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'variants' => 'Duplicate Color + Size combination is not allowed.'
                    ]);
            }

            $combinations[] = $key;
        }

        $variants = $validated['variants'] ?? [];
        unset($validated['variants']);

        $userId = auth()->id();

        // Create item + variants + inventory logs atomically
        DB::transaction(function () use ($validated, $variants, $userId, &$item) {
            $item = Item::create($validated);

            foreach ($variants as $variant) {
                $createdVariant = $item->variants()->create([
                    'color_id' => $variant['color_id'],
                    'size_id' => $variant['size_id'],
                    'quantity' => $variant['quantity'],
                ]);

                $createdVariant->inventoryLogs()->create([
                    'item_variant_id' => $createdVariant->id,
                    'order_master_id' => null,
                    'type' => 'restock',
                    'qty' => $variant['quantity'],
                    'note' => 'Initial stock',
                    'created_by' => $userId,
                ]);
            }
        });

        return redirect()->route('items.index')->withSuccess('Item created successfully.');
    }

    public function show(Item $item, Request $request): View
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('item-view')) {
            abort(403);
        }
        $item->load([
            'variants.inventoryLogs',
            'category',
            'group',
            'subCategory',
            'subGroup',
            'tax',
        ]);

        $totalProduction = 0;
        $totalSold = 0;
        $totalStock = 0;
        foreach($item->variants as $variant) {
            $totalProduction += $variant->total_production;
            $totalSold += $variant->total_sold;
            $totalStock += $variant->current_stock;
        }

        $variants = $this->getVariantsPaginator($item->id, $request);

        return view('admin.items.show', compact('item', 'variants', 'totalProduction', 'totalSold', 'totalStock'));
    }


    private function getVariantsPaginator(int $itemId, Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $perPage = (int) $request->input('per_page', 5);
        if ($perPage <= 0) {
            $perPage = 5;
        }

        $query = \App\Models\ItemVariant::query()
            ->with(['color', 'size', 'inventoryLogs'])
            ->where('item_id', $itemId);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('color', function ($c) use ($q) {
                    $c->where('name', 'like', "%{$q}%")
                      ->orWhere('color_code', 'like', "%{$q}%");
                })
                ->orWhereHas('size', function ($s) use ($q) {
                    $s->where('name', 'like', "%{$q}%");
                });
            });
        }

        return $query
            ->orderBy('color_id')
            ->orderBy('size_id')
            ->paginate($perPage);
    }


    public function edit(Item $item): View
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('item-edit')) {
            abort(403);
        }
        $item->load('variants');

        return view('admin.items.edit', [
            'item'           => $item,
            'categories'     => Category::orderBy('name')->get(),
            'groups'         => Group::orderBy('name')->get(),
            'sub_categories' => SubCategory::orderBy('name')->get(),
            'sub_groups'     => SubGroup::orderBy('name')->get(),
            'colors'         => Color::orderBy('name')->get(),
            'sizes'          => Size::orderBy('name')->get(),
            'taxes'          => TaxMaster::orderBy('tax_percentage')->get(),
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('item-edit')) {
            abort(403);
        }

    $validated = $request->validate([
        'name'             => 'required|string|max:255',
        'article_number'   => 'nullable|string|max:100|unique:items,article_number,' . $item->id,
        'item_code'        => 'nullable|string|max:100|unique:items,item_code,' . $item->id,
        'sub_category'     => 'nullable|exists:sub_categories,id',
        'sub_group'        => 'nullable|exists:sub_groups,id',
        'variants'         => 'required|array|min:1',
        'variants.*.color_id'  => 'required|exists:colors,id',
        'variants.*.size_id'   => 'required|exists:sizes,id',
        'variants.*.quantity'  => 'required|integer|min:0',
        'description'      => 'nullable|string',
        'category_id'      => 'nullable|exists:categories,id',
        'group_id'         => 'nullable|exists:groups,id',
        'unit'             => 'nullable|string|max:50',
        'price'            => 'required|numeric|min:0',
        'tax_id'           => 'nullable|exists:tax_masters,id',
        'video_link'       => 'nullable|url',
        'images'           => 'nullable|array|max:5',
        'images.*'         => 'image|mimes:jpg,jpeg,png|max:2048',
        'status'           => 'nullable|in:0,1',
        'show_item_on_web' => 'nullable|in:0,1',
    ]);

    // ── Image handling (keep your existing image logic) ──────────────
    $MAX_FILES    = 5;
    $prevImages   = is_array($item->images) ? $item->images : ($item->image ? [$item->image] : []);
    $keptExisting = $request->input('existing_images', $prevImages);
    if (!is_array($keptExisting)) { $keptExisting = [$keptExisting]; }

    $toDelete = array_diff($prevImages, $keptExisting);
    foreach ($toDelete as $d) { Storage::disk('public')->delete($d); }

    $finalImages   = array_values($keptExisting);
    $uploadedPaths = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            if (count($finalImages) >= $MAX_FILES) continue;
            $path            = $file->store('items', 'public');
            $uploadedPaths[] = $path;
            $finalImages[]   = $path;
        }
    }

    $primary     = $request->input('primary_image');
    $primaryPath = null;
    if ($primary) {
        if (str_starts_with($primary, 'new-')) {
            $idx = (int) Str::after($primary, 'new-');
            if (isset($uploadedPaths[$idx])) { $primaryPath = $uploadedPaths[$idx]; }
        } else {
            if (in_array($primary, $finalImages)) { $primaryPath = $primary; }
        }
    }
    $primaryPath = $primaryPath ?? ($finalImages[0] ?? null);
    if ($primaryPath) { $validated['image'] = $primaryPath; }
    if (Schema::hasColumn('items', 'images')) { $validated['images'] = $finalImages; }
    // ── End image handling ───────────────────────────────────────────

    $tax = !empty($validated['tax_id']) ? TaxMaster::find($validated['tax_id']) : null;
    $validated['tax_id']      = $validated['tax_id'] ?? null;
    $validated['tax_percent'] = $tax ? $tax->tax_percentage : 0;
    $validated['status']           = (int) ($validated['status'] ?? 1);
    $validated['show_item_on_web'] = (int) ($validated['show_item_on_web'] ?? 1);

    $variants = $validated['variants'] ?? [];
    unset($validated['variants']);

    // Duplicate combination check
    $combinations = [];
    foreach ($variants as $variant) {
        $key = $variant['color_id'] . '_' . $variant['size_id'];
        if (in_array($key, $combinations)) {
            return back()->withInput()->withErrors([
                'variants' => 'Duplicate Color + Size combination is not allowed.'
            ]);
        }
        $combinations[] = $key;
    }

    $userId = auth()->id();

    DB::transaction(function () use ($item, $validated, $variants, $userId) {

        $item->update($validated);

        // Build map of EXISTING variants keyed by color_id_size_id
        // KEY FIX: do NOT delete variants — update them in place to preserve
        // inventory_logs foreign keys
        $existingVariants = $item->variants()->get()->keyBy(function ($v) {
            return $v->color_id . '_' . $v->size_id;
        });

        // Track which variant keys are in the new submission
        $submittedKeys = [];

        foreach ($variants as $variant) {
            $key    = $variant['color_id'] . '_' . $variant['size_id'];
            $newQty = (int) $variant['quantity'];
            $submittedKeys[] = $key;

            if ($existingVariants->has($key)) {
                // --- EXISTING VARIANT: update qty, log only the difference ---
                $existingVariant = $existingVariants->get($key);
                $oldQty          = (int) $existingVariant->quantity;

                // Update qty on the existing record (preserves the ID and all logs)
                $existingVariant->update(['quantity' => $newQty]);

                if ($newQty === $oldQty) {
                    // No change — no log needed
                    continue;
                }

                $diff = abs($newQty - $oldQty);
                $type = $newQty > $oldQty ? 'restock' : 'deduct';
                $note = $newQty > $oldQty ? 'Stock updated' : 'Stock adjusted';

                $existingVariant->inventoryLogs()->create([
                    'item_variant_id' => $existingVariant->id,
                    'order_master_id' => null,
                    'type'            => $type,
                    'qty'             => $diff,
                    'note'            => $note,
                    'created_by'      => $userId,
                ]);

            } else {
                // --- BRAND NEW VARIANT: create it and log initial stock ---
                $newVariant = $item->variants()->create([
                    'color_id' => $variant['color_id'],
                    'size_id'  => $variant['size_id'],
                    'quantity' => $newQty,
                ]);

                // Log full qty as initial stock — same as store()
                if ($newQty > 0) {
                    $newVariant->inventoryLogs()->create([
                        'item_variant_id' => $newVariant->id,
                        'order_master_id' => null,
                        'type'            => 'restock',
                        'qty'             => $newQty,
                        'note'            => 'Initial stock',
                        'created_by'      => $userId,
                    ]);
                }
            }
        }

        // --- REMOVED VARIANTS: delete only variants not in submission ---
        // Their logs are also deleted via cascade (set up in migration)
        $keysToDelete = $existingVariants->keys()->diff($submittedKeys);
        if ($keysToDelete->isNotEmpty()) {
            $item->variants()
                ->whereIn('id', $existingVariants->only($keysToDelete)->pluck('id'))
                ->delete();
        }
    });

    return redirect()->route('items.index')->withSuccess('Item updated successfully.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('item-delete')) {
            abort(403);
        }
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

        $auth = auth()->user();
        $canView = $auth?->hasPermission('item-view') ?? false;
        $canEdit = $auth?->hasPermission('item-edit') ?? false;
        $canDelete = $auth?->hasPermission('item-delete') ?? false;
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

            if ($canView) {
                $actions .= '<a class="dropdown-item" href="' . $viewUrl . '">View</a>';
            }
            if ($canEdit) {
                $actions .= '<a class="dropdown-item" href="' . $editUrl . '">Edit</a>';
            }
            if ($canDelete) {
                $actions .= '
                    <form method="POST" action="' . $deleteUrl . '" style="display:inline;">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="dropdown-item deleteButton">Delete</button>
                    </form>
                ';
            }
            $actions .= '</div></div>';

            $data[] = [
                'id'             => $start + $idx + 1,
                'name'           => $item->name,
                'article_number' => $item->article_number ?? '-',
                'category'       => optional($item->category)->name ?? '-',
                'group'          => optional($item->group)->name   ?? '-',
                // 'sizes'          => $sizesDisplay ?: '-',
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
                'tax_percent'    => (float) $item->tax->tax_percentage,
                'image_url'      => $item->image_url,
                'description'    => $item->description,
            ];
        });

        return response()->json(['data' => $items]);
    }
}