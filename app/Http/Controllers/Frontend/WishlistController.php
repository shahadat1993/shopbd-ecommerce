<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('user_id', auth()->id())->with('product.images')->paginate(12);
        return view('frontend.wishlist.index', compact('items'));
    }

    public function add(Product $product)
    {
        Wishlist::firstOrCreate(['user_id' => auth()->id(), 'product_id' => $product->id]);
        return back()->with('success', '"' . $product->name . '" added to wishlist.');
    }

    public function remove(Product $product)
    {
        Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->delete();
        return back()->with('success', 'Removed from wishlist.');
    }
}
