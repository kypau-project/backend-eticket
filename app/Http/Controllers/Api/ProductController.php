<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with('category')->when($request->status, function ($query, $status) {
            return $query->where('status', $status);
        })->orderBy('id', 'DESC')->get();
        return ResponseHelper::jsonResponseMethod(data: $products, status:'success');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required',
            'price'         => 'required',
            'criteria'      => 'required',
        ]);

        $product                = new Product();
        $product->category_id   = $request->category_id;
        $product->name          = $request->name;
        $product->price         = $request->price;
        $product->criteria      = $request->criteria;
        $product->stock         = 0;       

        if ($request->file('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());
            $product->image = $image->hashName();
        }

        $product->save();

        $product = Product::with('category')->find($product->id);

        return ResponseHelper::jsonResponseMethod(data: $product, status:'success');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with('category')->find($id);
        if(!$product) {
            return ResponseHelper::jsonResponseMethod(message: 'Product not found', status:'error', code: 404);
        }
        return ResponseHelper::jsonResponseMethod(data: $product, status:'success');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if(!$product) {
            return ResponseHelper::jsonResponseMethod(message: 'Product not found', status:'error', code: 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if(!$product) {
            return ResponseHelper::jsonResponseMethod(message: 'Product not found', status:'error', code: 404);
        }
        $product->delete();
        return ResponseHelper::jsonResponseMethod(message: 'Product deleted successfully', status:'success');
    }
}
