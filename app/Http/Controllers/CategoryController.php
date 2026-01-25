<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::parentsOnly()
            ->with('children')
            ->where('is_active', true)
            ->get();

        return response()->json($categories);
    }
}
