<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal       = $cartItems->sum('subtotal');
        $couponDiscount = session('coupon_discount', 0);
        $shipping       = $this->calculateShipping($subtotal);
        $total          = $subtotal - $couponDiscount + $shipping;
        $addresses      = auth()->user()->addresses()->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        return view('frontend.checkout.index', compact(
            'cartItems', 'subtotal', 'shipping', 'total',
            'couponDiscount', 'addresses', 'defaultAddress'
        ));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email',
            'address'        => 'required|string',
            'city'           => 'required|string',
            'payment_method' => 'required|in:cod,stripe,sslcommerz',
        ]);

        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cart is empty.');
        }

        DB::beginTransaction();
        try {
            $subtotal       = $cartItems->sum('subtotal');
            $couponDiscount = session('coupon_discount', 0);
            $shipping       = $this->calculateShipping($subtotal);
            $total          = $subtotal - $couponDiscount + $shipping;

            $order = Order::create([
                'order_number'    => Order::generateOrderNumber(),
                'user_id'         => auth()->id(),
                'subtotal'        => $subtotal,
                'discount'        => $couponDiscount,
                'shipping_charge' => $shipping,
                'tax'             => 0,
                'total'           => $total,
                'coupon_id'       => session('coupon_id'),
                'coupon_code'     => session('coupon_code'),
                'coupon_discount' => $couponDiscount,
                'status'          => Order::STATUS_PENDING,
                'payment_status'  => Order::PAYMENT_PENDING,
                'payment_method'  => $request->payment_method,
                'shipping_name'   => $request->name,
                'shipping_phone'  => $request->phone,
                'shipping_email'  => $request->email,
                'shipping_address'=> $request->address,
                'shipping_city'   => $request->city,
                'shipping_state'  => $request->state,
                'shipping_zip'    => $request->zip,
                'shipping_country'=> $request->country ?? 'Bangladesh',
                'notes'           => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku'  => $item->product->sku,
                    'variant'      => $item->variant,
                    'thumbnail'    => $item->product->thumbnail,
                    'price'        => $item->price,
                    'qty'          => $item->qty,
                    'subtotal'     => $item->subtotal,
                ]);
                $item->product->decrement('stock', $item->qty);
            }

            if (session('coupon_id')) {
                Coupon::find(session('coupon_id'))?->increment('used_count');
            }

            // Clear cart
            Cart::where('user_id', auth()->id())->delete();
            session()->forget(['coupon_code', 'coupon_id', 'coupon_discount']);

            DB::commit();

            // ── Payment Routing ──
            if ($request->payment_method === 'stripe') {
                // Store order ID in session for Stripe controller
                session(['stripe_order_id' => $order->id]);
                // Redirect to our Stripe initiate page (GET)
                return redirect()->route('stripe.init', ['order' => $order->id]);
            }

            if ($request->payment_method === 'sslcommerz') {
                // Store order ID in session for SSLCommerz controller
                session(['ssl_order_id' => $order->id]);
                // Redirect to our SSLCommerz initiate page (GET)
                return redirect()->route('sslcommerz.init.get', ['order' => $order->id]);
            }

            // COD — confirm immediately
            $order->update(['status' => Order::STATUS_CONFIRMED]);
            return redirect()->route('checkout.success', $order)
                             ->with('success', 'Order placed successfully! We will deliver it soon.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        return view('frontend.checkout.success', compact('order'));
    }

    public function cancel()
    {
        return view('frontend.checkout.cancel');
    }

    private function calculateShipping($subtotal): float
    {
        $freeMin = (float) \App\Models\Setting::get('free_shipping_min', 1000);
        if ($subtotal >= $freeMin) return 0;
        return (float) \App\Models\Setting::get('flat_rate_amount', 80);
    }
}
