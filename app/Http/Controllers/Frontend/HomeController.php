<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        $heroBanners      = Banner::active()->where('position', 'hero')->get();
        $promoBanners     = Banner::active()->where('position', 'promo')->get();
        $featuredProducts = Product::active()->featured()->inStock()->with('category')->latest()->take(10)->get();
        $newArrivals      = Product::active()->inStock()->with('category')->latest()->take(8)->get();
        $topCategories    = Category::active()
                                ->whereNull('parent_id')
                                ->with(['children' => fn($q) => $q->active()])
                                ->withCount('products')
                                ->orderByDesc('products_count')
                                ->take(8)
                                ->get();
        $dealProducts     = Product::active()
                                ->whereNotNull('sale_price')
                                ->whereColumn('sale_price', '<', 'price')
                                ->inStock()
                                ->with('category')
                                ->take(4)
                                ->get();

        return view('frontend.home.index', compact(
            'heroBanners', 'promoBanners', 'featuredProducts',
            'newArrivals', 'topCategories', 'dealProducts'
        ));
    }
}
