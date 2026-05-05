<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Group;
use App\Models\Size;
use App\Models\Color; 
use App\Models\Set;
use Illuminate\Http\Request;

class ItemMasterController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $groups = Group::all();
        $sizes = Size::all();
        $colors = Color::all(); 
        $sets = Set::with('sizes')->get();
        return view('admin.item-master.index', compact('categories', 'groups', 'sizes', 'colors', 'sets'));
    }

    // placeholder endpoints could be added later for AJAX partials
}
