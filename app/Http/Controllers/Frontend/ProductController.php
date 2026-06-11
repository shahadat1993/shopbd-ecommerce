<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'images']);

        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('description', 'like', "%$s%")->orWhere('brand', 'like', "%$s%"));
        }
        if ($request->category)
            $query->where('category_id', $request->category);
        if ($request->brand)
            $query->where('brand', $request->brand);
        if ($request->min_price)
            $query->where('price', '>=', $request->min_price);
        if ($request->max_price)
            $query->where('price', '<=', $request->max_price);
        if ($request->in_stock)
            $query->inStock();
        if ($request->on_sale)
            $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');

        match ($request->sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'popular' => $query->withCount('orderItems')->orderByDesc('order_items_count'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->with('children')->withCount('products')->whereNull('parent_id')->get();
        $brands = Product::active()->whereNotNull('brand')->distinct()->pluck('brand');
        $priceRange = ['min' => Product::active()->min('price'), 'max' => Product::active()->max('price')];

        return view('frontend.products.index', compact('products', 'categories', 'brands', 'priceRange'));
    }

    // Product $product এর বদলে সরাসরি $slug গ্রহণ করো
    public function show($slug)
    {
        // স্লাগ দিয়ে প্রোডাক্টটি খুঁজে বের করো, না পেলে ৪MD৪ দেবে
        $product = Product::where('slug', $slug)->firstOrFail();

        if (!$product->is_active)
            abort(404);

        $product->load([
            'category.children',
            'images',
            'variants',
            'reviews' => fn($q) => $q->where('is_approved', true)->with('user')->latest()
        ]);

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)->inStock()->take(6)->get();

        $inWishlist = auth()->check()
            ? auth()->user()->wishlist()->where('product_id', $product->id)->exists()
            : false;

        return view('frontend.products.show', compact('product', 'relatedProducts', 'inWishlist'));
    }

    // Category $category এর বদলে সরাসরি $slug গ্রহণ করো
    public function byCategory($slug)
    {
        // ১. আইডি-র বদলে স্লাগ দিয়ে ক্যাটাগরি এবং তার চাইল্ডগুলোকে খুঁজে বের করো
        $category = Category::where('slug', $slug)->with('children')->firstOrFail();

        // একটিভ চাইল্ড সহ ক্যাটাগরি লোড করা নিশ্চিত করা
        $category->load(['children' => fn($q) => $q->where('is_active', true)]);

        $query = Product::active()->with('category');

        // ২. বর্তমান ক্যাটাগরি এবং চাইল্ড ক্যাটাগরির আইডি মার্চ করা
        $categoryIds = collect([$category->id]);
        if ($category->children->count() > 0) {
            $categoryIds = $categoryIds->merge($category->children->pluck('id'));
        }
        $query->whereIn('category_id', $categoryIds);

        // ৩. রিকোয়েস্ট ফিল্টার অ্যাপ্লাই (আগের মতোই থাকবে)
        $request = request();
        if ($request->brand) {
            $query->where('brand', $request->brand);
        }
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->in_stock) {
            $query->inStock();
        }
        if ($request->on_sale) {
            $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
        }

        // ৪. সর্টিং লজিক
        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'popular') {
            $query->withCount('orderItems')->orderByDesc('order_items_count');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        // সাইডবারের জন্য প্যারেন্ট ক্যাটাগরিগুলো নেওয়া
        $categories = Category::active()->with('children')->whereNull('parent_id')->get();

        $brands = Product::active()->whereIn('category_id', $categoryIds)->whereNotNull('brand')->distinct()->pluck('brand');

        $priceRange = [
            'min' => Product::active()->whereIn('category_id', $categoryIds)->min('price') ?? 0,
            'max' => Product::active()->whereIn('category_id', $categoryIds)->max('price') ?? 0
        ];

        return view('frontend.products.index', compact('products', 'categories', 'brands', 'priceRange', 'category'));
    }

    public function search(Request $request)
    {
        $q = $request->q;
        $query = Product::active()->with('category');
        if ($q) {
            $query->where(fn($qu) => $qu->where('name', 'like', "%$q%")->orWhere('brand', 'like', "%$q%")->orWhere('description', 'like', "%$q%"));
        }
        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->with('children')->withCount('products')->whereNull('parent_id')->get();
        $brands = Product::active()->whereNotNull('brand')->distinct()->pluck('brand');
        $priceRange = ['min' => 0, 'max' => 200000];
        return view('frontend.products.index', compact('products', 'categories', 'brands', 'priceRange'));
    }

    public function suggest(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2)
            return response()->json([]);
        $products = Product::active()
            ->where(fn($query) => $query->where('name', 'like', "%$q%")->orWhere('brand', 'like', "%$q%"))
            ->with('category')->take(6)->get();
        return response()->json($products->map(fn($p) => [
            'name' => $p->name,
            'url' => route('products.show', $p),
            'image' => $p->thumbnail_url,
            'price' => number_format($p->current_price, 0),
            'category' => $p->category?->name ?? 'General',
            'badge' => $p->discount_percent > 0 ? $p->discount_percent : null,
        ]));
    }
}
