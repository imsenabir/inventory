<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name', 'asc')->get();

        if($products->isNotEmpty())
        {
            return response()->json([
                'status' => 'success',
                'data' => $products,
                'message' => 'Product found.'
            ]);

        }else{
            return response()->json([
                'status' => 'error',
                'data' => '',
                'message' => 'Product not found'
            ]);
        }

    }// end method

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'message' => 'Validation error!'
            ]);
        }

        $productData = $request->except('image');

        if($request->hasFile('image')){
            $path = $request->file('image')->store('products', 'public');
            $productData['image'] = $path;
        }

        $product = Product::create($productData);

        return response()->json([
                'status' => 'success',
                'data'   => $product,
                'message' => 'Product created successfully'
            ]);

    }

    public function show(Product $product)
    {

        try{
            if(!empty($product))
            {
                return response()->json([
                    'status' => 'success',
                    'data' => $product,
                    'message' => 'Product found.'
                ]);

            }
        }catch(\Exception $e)
        {
                return response()->json([
                    'status' => 'error',
                    'data' => '',
                    'message' => 'Product not found'
                ]);
        }
    }

    public function update($product_id, Request $request)
    {
        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'message' => 'Validation error!'
            ]);
        }

        $path = '';
        if($request->hasFile('image')){
            $path = $request->file('image')->store('products', 'public');
        }


        $product = Product::find($product_id);

        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->image = $path;
        $product->save();

        if($request->expectsJson()){

        }else{
            return redirect('backend/admin/products/list');
        }

        return response()->json([
            'status' => 'success',
            'data' => $product,
            'message' => 'Product updated successfully.'
        ]);

    }

    public function adminProductList()
    {
        $products = Product::orderBy('name', 'asc')->get();

        return view('pages.dashboard.admin.products.list', compact('products'));


    }

    public function adminProductEdit(Product $product)
    {
        $categories = category::all();
        return view('pages.dashboard.admin.products.edit', compact('product', 'categories'));
    }

    public function customerProducts()
    {
        $products = Product::orderBy('name', 'asc')->get();

        return view('pages.dashboard.customers.products.list', compact('products'));
    }
}
