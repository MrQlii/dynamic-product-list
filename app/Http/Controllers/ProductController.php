<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use APP\Models\ProductCategory;

class ProductController extends Controller
{
    public function index()
    {
        // $products = Product::with('categories')->get();
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=> 'rqeuired|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads'), $filename);
            $product->image = $filename;
        }

        $product->save();
        // $product = Product::create(['name' => $request->name]);

        return response()->json(['product' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->image) {
            $imagePath = public_path('uploads/' . $product->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }

    public function deleteImage($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            $imagePath = public_path('uploads/' . $product->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $product->image = null;
            $product->save();
        }

        return response()->json(['message' => 'Image deleted']);
    }
}
