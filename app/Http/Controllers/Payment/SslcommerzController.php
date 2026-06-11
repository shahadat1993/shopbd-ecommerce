<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class SslcommerzController extends Controller
{
    /**
     * GET: Called from CheckoutController redirect
     * Initializes SSLCommerz and redirects to gateway
     */
    public function initGet(Request $request)
    {
        $orderId = $request->order ?? session('ssl_order_id');
        $order = Order::with('items')->findOrFail($orderId);

        if ($order->user_id !== auth()->id())
            abort(403);

        $storeId = config('services.sslcommerz.store_id');
        $storePass = config('services.sslcommerz.store_password');

        if (!$storeId || str_contains($storeId, 'your_') || $storeId === '') {
            // SSLCommerz not configured — fallback to COD
            $order->update([
                'payment_method' => 'cod',
                'status' => Order::STATUS_CONFIRMED,
            ]);
            return redirect()->route('checkout.success', $order)
                ->with('success', 'SSLCommerz not configured. Order placed as COD.');
        }

        $isSandbox = config('services.sslcommerz.sandbox', true);
        $apiUrl = $isSandbox
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

        $postData = [
            'store_id' => $storeId,
            'store_passwd' => $storePass,
            'total_amount' => $order->total,
            'currency' => 'BDT',
            'tran_id' => $order->order_number,
            'success_url' => route('sslcommerz.success'),
            'fail_url' => route('sslcommerz.fail'),
            'cancel_url' => route('sslcommerz.cancel'),
            'ipn_url' => route('sslcommerz.ipn'),
            'cus_name' => $order->shipping_name,
            'cus_email' => $order->shipping_email,
            'cus_phone' => $order->shipping_phone,
            'cus_add1' => $order->shipping_address,
            'cus_city' => $order->shipping_city,
            'cus_country' => $order->shipping_country,
            'ship_name' => $order->shipping_name,
            'ship_add1' => $order->shipping_address,
            'ship_city' => $order->shipping_city,
            'ship_country' => $order->shipping_country,
            'shipping_method' => 'Courier',
            'product_name' => 'Order #' . $order->order_number,
            'product_category' => 'Mixed',
            'product_profile' => 'general',
            'num_of_item' => $order->items->sum('qty'),
            'value_a' => $order->id,
        ];

        $curl = curl_init($apiUrl);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = json_decode(curl_exec($curl), true);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($curlError) {
            \Log::error('SSLCommerz cURL error: ' . $curlError);
            return redirect()->route('checkout.cancel')
                ->with('error', 'Could not connect to SSLCommerz. Try COD or Stripe.');
        }

        if (isset($response['GatewayPageURL']) && $response['GatewayPageURL']) {
            $order->update(['sslcommerz_tran_id' => $order->order_number]);
            session()->forget('ssl_order_id');
            return redirect($response['GatewayPageURL']);
        }

        $errMsg = $response['failedreason'] ?? 'Gateway initialization failed';
        \Log::error('SSLCommerz init failed: ' . $errMsg);
        return redirect()->route('checkout.cancel')
            ->with('error', 'SSLCommerz error: ' . $errMsg . '. Try COD instead.');
    }

    /**
     * POST: SSLCommerz calls this on success
     */
    public function success(Request $request)
    {
        $order = Order::where('order_number', $request->tran_id)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        // Validate transaction
        if ($request->status === 'VALID' || $request->status === 'VALIDATED') {
            $order->update([
                'payment_status' => Order::PAYMENT_PAID,
                'status' => Order::STATUS_CONFIRMED,
                'transaction_id' => $request->bank_tran_id,
            ]);

            // Only show success to the actual customer
            if (auth()->check() && auth()->id() === $order->user_id) {
                return redirect()->route('checkout.success', $order)
                    ->with('success', 'Payment successful! Order confirmed.');
            }
        }

        return redirect()->route('home')->with('success', 'Payment processed successfully.');
    }

    /**
     * POST: SSLCommerz calls this on failure
     */
    public function fail(Request $request)
    {
        $order = Order::where('order_number', $request->tran_id)->first();
        if ($order) {
            $order->update(['payment_status' => Order::PAYMENT_FAILED]);
        }
        return redirect()->route('checkout.cancel')
            ->with('error', 'Payment failed. Please try again or use COD.');
    }

    /**
     * POST: SSLCommerz calls this on cancel
     */
    public function cancel(Request $request)
    {
        $order = Order::where('order_number', $request->tran_id)->first();
        if ($order) {
            $order->update(['status' => Order::STATUS_CANCELLED]);
        }
        return redirect()->route('checkout.cancel')
            ->with('error', 'Payment was cancelled.');
    }

    /**
     * POST: SSLCommerz IPN (server-to-server verification)
     */
    public function ipn(Request $request)
    {
        $order = Order::where('order_number', $request->tran_id)->first();
        if ($order && in_array($request->status, ['VALID', 'VALIDATED'])) {
            $order->update([
                'payment_status' => Order::PAYMENT_PAID,
                'status' => Order::STATUS_CONFIRMED,
                'transaction_id' => $request->bank_tran_id,
            ]);
        }
        return response('IPN received', 200);
    }
}
