<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Category::all()]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Category::findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['name' => ['required', 'string', 'max:100'], 'icon' => ['nullable', 'string']]);
        $cat = Category::create(['name' => $request->name, 'slug' => Str::slug($request->name), 'icon' => $request->icon]);
        return response()->json(['success' => true, 'data' => $cat], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $cat = Category::findOrFail($id);
        $cat->update(['name' => $request->name ?? $cat->name, 'icon' => $request->icon ?? $cat->icon]);
        return response()->json(['success' => true, 'data' => $cat]);
    }

    public function destroy(int $id): JsonResponse
    {
        Category::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Kategori dihapus.']);
    }
}
