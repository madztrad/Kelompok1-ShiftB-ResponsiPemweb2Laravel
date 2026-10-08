<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /** Publik: dipakai dropdown pada form & filter beranda. */
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::orderBy('name')->get());
    }

    /** Admin. */
    public function store(CategoryRequest $request): JsonResponse
    {
        $name = $request->validated('name');
        $category = Category::create(['name' => $name, 'slug' => Str::slug($name)]);

        return CategoryResource::make($category)->response()->setStatusCode(201);
    }

    /** Admin. */
    public function update(CategoryRequest $request, Category $category): CategoryResource
    {
        $name = $request->validated('name');
        $category->update(['name' => $name, 'slug' => Str::slug($name)]);

        return CategoryResource::make($category);
    }

    /** Admin. */
    public function destroy(Category $category): Response
    {
        if ($category->items()->exists()) {
            abort(409, 'Kategori masih dipakai oleh laporan barang.');
        }

        $category->delete();

        return response()->noContent();
    }
}