<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getIdentifier(): array
    {
        return auth()->check()
            ? ['user_id' => auth()->id()]
            : ['session_id' => session()->getId()];
    }

    private function getCartQuery()
    {
        return Cart::where($this->getIdentifier());
    }

    public function index()
    {
        $cartItems   = $this->getCartQuery()->with('product.images')->get();
        $couponCode  = session('coupon_code');
        $couponDiscount = session('coupon_discount', 0);
        $subtotal    = $cartItems->sum('subtotal');
        $shipping    = $this->calculateShipping($subtotal);
        $total       = $subtotal - $couponDiscount + $shipping;

        return view('frontend.cart.index', compact('cartItems', 'subtotal', 'shipping', 'total', 'couponCode', 'couponDiscount'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active || $product->stock < $request->qty) {
            return back()->with('error', 'Product not available or insufficient stock.');
        }

        $identifier = $this->getIdentifier();
        $variant    = $request->variant ? json_encode($request->variant) : null;

        $existing = Cart::where($identifier)->where('product_id', $product->id)->first();

        if ($existing) {
            $newQty = $existing->qty + $request->qty;
            if ($newQty > $product->stock) $newQty = $product->stock;
            $existing->update(['qty' => $newQty]);
        } else {
            Cart::create(array_merge($identifier, [
                'product_id' => $product->id,
                'variant'    => $variant,
                'qty'        => $request->qty,
                'price'      => $product->current_price,
            ]));
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'cart_count' => $this->getCartQuery()->sum('qty')]);
        }

        return back()->with('success', '"' . $product->name . '" added to cart!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['qty' => 'required|integer|min:1']);

        $item = $this->getCartQuery()->findOrFail($id);

        if ($item->product->stock < $request->qty) {
            return back()->with('error', 'Not enough stock.');
        }

        $item->update(['qty' => $request->qty]);
        return back()->with('success', 'Cart updated.');
    }

    public function remove($id)
    {
        $this->getCartQuery()->findOrFail($id)->delete();
        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

        if (!$coupon || !$coupon->isValid()) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        $cartItems = $this->getCartQuery()->get();
        $subtotal  = $cartItems->sum('subtotal');

        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return back()->with('error', 'Minimum order amount of ' . currency($coupon->min_order_amount) . ' required.');
        }

        $discount = $coupon->calculateDiscount($subtotal);
        session(['coupon_code' => $coupon->code, 'coupon_id' => $coupon->id, 'coupon_discount' => $discount]);

        return back()->with('success', 'Coupon applied! You saved ' . currency($discount) . '.');
    }

    public function removeCoupon()
    {
        session()->forget(['coupon_code', 'coupon_id', 'coupon_discount']);
        return back()->with('success', 'Coupon removed.');
    }

    private function calculateShipping($subtotal): float
    {
        $freeMin = (float) \App\Models\Setting::get('free_shipping_min', 1000);
        if ($subtotal >= $freeMin) return 0;
        return (float) \App\Models\Setting::get('flat_rate_amount', 80);
    }
}
