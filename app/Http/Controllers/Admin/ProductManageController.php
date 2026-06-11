<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductManageController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->category) $query->where('category_id', $request->category);
        if ($request->status === '1') $query->where('is_active', true);
        if ($request->status === '0') $query->where('is_active', false);
        if ($request->stock === 'low') $query->where('stock', '<=', 5)->where('stock', '>', 0);
        if ($request->stock === 'out') $query->where('stock', 0);

        $products   = $query->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0|lt:price',
            'cost_price'        => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|unique:products,sku',
            'stock'             => 'required|integer|min:0',
            'category_id'       => 'nullable|exists:categories,id',
            'brand'             => 'nullable|string|max:100',
            'thumbnail'         => 'nullable|image|max:2048',
            'images.*'          => 'nullable|image|max:2048',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'tags'              => 'nullable|string',
        ]);

        $validated['slug']       = Str::slug($request->name) . '-' . Str::random(5);
        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['tags']        = $request->tags ? array_map('trim', explode(',', $request->tags)) : null;

        // Thumbnail Upload to Cloudinary
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = cloudinary()->upload($request->file('thumbnail')->getRealPath())->getSecurePath();
        }

        $product = Product::create($validated);

        // Handle multiple images using Cloudinary
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = cloudinary()->upload($image->getRealPath())->getSecurePath();
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $path,
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'reviews.user', 'variants']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants']);
        $categories = Category::active()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'cost_price'        => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|unique:products,sku,' . $product->id,
            'stock'             => 'required|integer|min:0',
            'category_id'       => 'nullable|exists:categories,id',
            'brand'             => 'nullable|string|max:100',
            'thumbnail'         => 'nullable|image|max:2048',
            'images.*'          => 'nullable|image|max:2048',
        ]);

        $validated['is_active']   = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['tags']        = $request->tags ? array_map('trim', explode(',', $request->tags)) : null;

        // Thumbnail Update using Cloudinary
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = cloudinary()->upload($request->file('thumbnail')->getRealPath())->getSecurePath();
        }

        $product->update($validated);

        // Multiple Gallery Images Update using Cloudinary
        if ($request->hasFile('images')) {
            $existingCount = $product->images()->count();
            foreach ($request->file('images') as $index => $image) {
                $path = cloudinary()->upload($image->getRealPath())->getSecurePath();
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $path,
                    'sort_order' => $existingCount + $index,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // ডিলিট লজিক থেকে স্টোরেজ ডিলিট বাদ
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);
        return back()->with('success', 'Product updated.');
    }

    public function deleteImage(ProductImage $image)
    {
        // স্টোরেজ ডিলিট বাদ, শুধু ডেটাবেস থেকে ডিলিট
        $image->delete();
        return back()->with('success', 'Image deleted.');
    }
}
