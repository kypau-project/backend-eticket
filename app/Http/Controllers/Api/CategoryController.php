<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        // return response()->json([
        //     'status' => 'success',
        //     'data' => Category::all()
        // ]);
        // return ResponseHelper::jsonResponseMethod('success', Category::all());

        // Category::create([
        //     'name' => 'test',
        //     'description' => 'test',
        // ]);

        // $category = new Category();
        // $category->name = 'test';
        // $category->description = 'test';
        // $category->save();

        return ResponseHelper::jsonResponseMethod(data: Category::all(), status:'success');
    }
}
