<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
{
    $items = Item::where('status',1)->latest()->take(8)->get();
    return view('frontend.home', compact('items'));
}

    public function products()
    {
        $items = Item::with('category')
            ->where('status', 1)
            ->latest()
            ->paginate(12);

        $categories = Category::orderBy('name')->get();
        return view('frontend.products', compact('items', 'categories'));
    }

    public function category(Category $category)
    {
        $items = Item::with('category')
            ->where('status', 1)
            ->where('category_id', $category->id)
            ->latest()
            ->paginate(12);

        return view('frontend.category', compact('category', 'items'));
    }

    public function categories(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $items = Item::with('category')
            ->where('status', 1)
            ->latest()
            ->paginate(12);

        return view('frontend.categories', compact('categories', 'items'));
    }

    public function categoryItems(Category $category)
    {
        $items = Item::where('status', 1)
            ->where('category_id', $category->id)
            ->latest()
            ->get()
            ->map(function ($it) {
                return [
                    'id' => $it->id,
                    'name' => $it->name,
                    'price' => number_format($it->price, 2),
                    'image' => $it->image_urls[0] ?? asset('no-image.png'),
                    'category' => optional($it->category)->name,
                    'url' => route('products.show', $it),
                ];
            });

        return response()->json(['items' => $items]);
    }

    public function show(Item $item)
    {
        return view('frontend.product-details', compact('item'));
    }
}