<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'title'   => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if already reviewed
        $existing = Review::where('product_id', $product->id)
                          ->where('user_id', auth()->id())
                          ->first();

        if ($existing) {
            $existing->update([
                'rating'  => $request->rating,
                'title'   => $request->title,
                'comment' => $request->comment,
                'is_approved' => false,
            ]);
            return back()->with('success', 'Review updated and pending approval.');
        }

        Review::create([
            'product_id'  => $product->id,
            'user_id'     => auth()->id(),
            'rating'      => $request->rating,
            'title'       => $request->title,
            'comment'     => $request->comment,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Review submitted and pending approval. Thank you!');
    }
}
