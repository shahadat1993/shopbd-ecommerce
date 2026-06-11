<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewManageController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user'])->latest();
        if ($request->status === 'pending')  $query->where('is_approved', false);
        if ($request->status === 'approved') $query->where('is_approved', true);
        if ($request->product) $query->where('product_id', $request->product);

        $reviews = $query->paginate(15)->withQueryString();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['product', 'user']);
        return view('admin.reviews.show', compact('review'));
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted.');
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => !$review->is_approved]);
        $msg = $review->is_approved ? 'Review approved.' : 'Review unapproved.';
        return back()->with('success', $msg);
    }
}
