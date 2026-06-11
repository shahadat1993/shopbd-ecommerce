<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('shipping_name', 'like', '%' . $request->search . '%')
                  ->orWhere('shipping_phone', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->status)         $query->where('status', $request->status);
        if ($request->payment_status) $query->where('payment_status', $request->payment_status);
        if ($request->payment_method) $query->where('payment_method', $request->payment_method);
        if ($request->date_from)      $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)        $query->whereDate('created_at', '<=', $request->date_to);

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'coupon']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);
        $order->update(['admin_notes' => $request->admin_notes]);
        return back()->with('success', 'Order notes updated.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'shipped')   $data['shipped_at']   = now();
        if ($request->status === 'delivered') $data['delivered_at'] = now();

        $order->update($data);

        // TODO: send email notification to customer

        return back()->with('success', 'Order status updated to ' . ucfirst($request->status) . '.');
    }

    public function invoice(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.invoice', compact('order'));
    }
}
