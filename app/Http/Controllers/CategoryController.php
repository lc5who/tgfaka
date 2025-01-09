<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $categories = Category::query()->orderBy('weigh','desc')->paginate($limit);
        return $this->success($categories, 'Categories retrieved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->success($category, 'Category retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'weigh' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors(), 'Validation failed', 422);
        }

        $category = Category::create($request->all());
        return $this->success($category, 'Category created successfully', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'weigh' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors(), 'Validation failed', 422);
        }

        $category->update($request->all());
        return $this->success($category, 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->success(null, 'Category deleted successfully');
    }

}
