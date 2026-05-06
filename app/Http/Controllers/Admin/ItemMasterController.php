<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Group;
use App\Models\Size;
use App\Models\Color; 
use Illuminate\Http\Request;

class ItemMasterController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $groups = Group::all();
        $sizes = Size::all();
        $colors = Color::all(); 
        return view('admin.item-master.index', compact('categories', 'groups', 'sizes', 'colors'));
    }

    // placeholder endpoints could be added later for AJAX partials
}
