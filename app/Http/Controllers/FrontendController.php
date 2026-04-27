<?php

namespace App\Http\Controllers;

use App\Models\Item;

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

        return view('frontend.products', compact('items'));
    }

    public function show(Item $item)
    {
        return view('frontend.product-details', compact('item'));
    }
}