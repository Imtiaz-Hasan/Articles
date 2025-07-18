<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // GET /api/categories
    public function index()
    {
        return response()->json(Category::all());
    }

    // POST /api/categories
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $slug = Str::slug($request->name);
        $slug = Category::where('slug', $slug)->exists() ? $slug . '-' . uniqid() : $slug;
        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
        ]);
        return response()->json($category, 201);
    }

    // PUT /api/categories/{id}
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $slug = Str::slug($request->name);
        $slug = Category::where('slug', $slug)->where('id', '!=', $category->id)->exists() ? $slug . '-' . uniqid() : $slug;
        $category->name = $request->name;
        $category->slug = $slug;
        $category->save();
        return response()->json($category);
    }

    // DELETE /api/categories/{id}
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
