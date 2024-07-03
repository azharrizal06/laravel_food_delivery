<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //get all products by restaurant
    public function index(Request $request)
    {
        $products = Products::where('user_id', $request->user()->id)->get();
        return response()->json(['status' => 'success', 'data' => $products],200);
    }
    //store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'is_available' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'required|image',
        ]);
        $user = $request->user();
        $request ->merge(['user_id' => $user->id]);
        $product = Products::create($request->all());
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $product->image = $filename;
            $product->save();
        }
        return response()->json(['status' => 'success', 'data' => $product],200);

    }
    //update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'is_available' => 'required|boolean',
            'is_favorite' => 'required|boolean',
        ]);

        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $data = $request->all();

        $product->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }
    //delete
    public function destroy($id)
    {   
        $product = Products::find($id);
        if(!$product){
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }
        $product->delete();
        
        return response()->json(['status' => 'success', 'message' => 'Product deleted successfully'],200);
    }
}
