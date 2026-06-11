<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    /**
     * GET: Called from CheckoutController redirect
     * Creates Stripe session and redirects to Stripe
     */
    public function init(Request $request)
    {
        $orderId = $request->order ?? session('stripe_order_id');
        $order   = Order::with('items')->findOrFail($orderId);

        // Verify this order belongs to logged in user
        if ($order->user_id !== auth()->id()) abort(403);

        $stripeKey = config('services.stripe.secret');

        if (!$stripeKey || str_contains($stripeKey, 'your_stripe')) {
            // Stripe not configured — fallback to COD confirmation
            $order->update([
                'payment_method' => 'cod',
                'status'         => Order::STATUS_CONFIRMED,
            ]);
            return redirect()->route('checkout.success', $order)
                             ->with('success', 'Stripe not configured. Order placed as COD.');
        }

        try {
            \Stripe\Stripe::setApiKey($stripeKey);

            $lineItems = $order->items->map(fn($item) => [
                'price_data' => [
                    'currency'     => 'bdt',
                    'product_data' => [
                        'name'   => $item->product_name,
                        'images' => [],
                    ],
                    'unit_amount' => (int)($item->price * 100),
                ],
                'quantity' => $item->qty,
            ])->toArray();

            // Add shipping as line item if applicable
            if ($order->shipping_charge > 0) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'bdt',
                        'product_data' => ['name' => 'Shipping Charge'],
                        'unit_amount'  => (int)($order->shipping_charge * 100),
                    ],
                    'quantity' => 1,
                ];
            }

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'customer_email'       => $order->shipping_email,
                'success_url'          => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('stripe.cancel') . '?order_id=' . $order->id,
                'metadata'             => [
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);

            // Save session ID
            $order->update(['stripe_session_id' => $session->id]);

            // Redirect to Stripe hosted checkout
            return redirect($session->url);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe error: ' . $e->getMessage());
            return redirect()->route('checkout.cancel')
                             ->with('error', 'Stripe error: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Stripe general error: ' . $e->getMessage());
            return redirect()->route('checkout.cancel')
                             ->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    /**
     * GET: Stripe redirects here after successful payment
     */
    public function success(Request $request)
    {
        $sessionId = $request->session_id;

        if (!$sessionId) {
            return redirect()->route('home')->with('error', 'Invalid payment session.');
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $stripeSession = \Stripe\Checkout\Session::retrieve($sessionId);
            $order = Order::where('stripe_session_id', $sessionId)->first();

            if (!$order) {
                return redirect()->route('account.orders')->with('error', 'Order not found.');
            }

            if ($stripeSession->payment_status === 'paid') {
                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'status'         => Order::STATUS_CONFIRMED,
                    'transaction_id' => $stripeSession->payment_intent,
                ]);
            }

            session()->forget('stripe_order_id');
            return redirect()->route('checkout.success', $order)
                             ->with('success', 'Payment successful! Order confirmed.');

        } catch (\Exception $e) {
            \Log::error('Stripe success error: ' . $e->getMessage());
            return redirect()->route('account.orders')
                             ->with('error', 'Could not verify payment. Contact support.');
        }
    }

    /**
     * GET: User cancelled Stripe payment
     */
    public function cancel(Request $request)
    {
        $orderId = $request->order_id;
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order && $order->user_id === auth()->id()) {
                $order->update([
                    'payment_status' => Order::PAYMENT_FAILED,
                    'status'         => Order::STATUS_CANCELLED,
                ]);
            }
        }
        return redirect()->route('checkout.cancel')
                         ->with('error', 'Payment was cancelled.');
    }

    /**
     * POST: Stripe webhook (server-to-server)
     */
    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            if ($secret) {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
            } else {
                $event = json_decode($payload);
            }

            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;
                $order   = Order::where('stripe_session_id', $session->id)->first();
                if ($order && $session->payment_status === 'paid') {
                    $order->update([
                        'payment_status' => Order::PAYMENT_PAID,
                        'status'         => Order::STATUS_CONFIRMED,
                        'transaction_id' => $session->payment_intent,
                    ]);
                }
            }

        } catch (\Exception $e) {
            \Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['status' => 'ok']);
    }
}
