<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from) : Carbon::now()->startOfMonth();
        $to   = $request->to   ? Carbon::parse($request->to)   : Carbon::now()->endOfMonth();

        $orders = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$from, $to])
            ->with('items')
            ->get();

        $summary = [
            'total_orders'   => $orders->count(),
            'total_revenue'  => $orders->sum('total'),
            'avg_order_value'=> $orders->avg('total'),
            'total_items'    => $orders->sum(fn($o) => $o->items->sum('qty')),
        ];

        // Daily breakdown
        $daily = $orders->groupBy(fn($o) => $o->created_at->format('Y-m-d'))
            ->map(fn($dayOrders) => [
                'date'    => $dayOrders->first()->created_at->format('d M'),
                'orders'  => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total'),
            ])->values();

        return view('admin.reports.sales', compact('summary', 'daily', 'from', 'to'));
    }

    public function products(Request $request)
    {
        $topSelling = OrderItem::selectRaw('product_id, product_name, SUM(qty) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->take(20)
            ->get();

        $lowStock = Product::where('stock', '<=', 10)->orderBy('stock')->get();

        return view('admin.reports.products', compact('topSelling', 'lowStock'));
    }

    public function customers(Request $request)
    {
        $topCustomers = User::role('customer')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderByDesc('orders_sum_total')
            ->take(20)
            ->get();

        $newCustomers = User::role('customer')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        return view('admin.reports.customers', compact('topCustomers', 'newCustomers'));
    }

    public function export(Request $request, string $type)
    {
        // Simple CSV export
        $filename = $type . '_report_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');

            if ($type === 'orders') {
                fputcsv($file, ['Order #','Customer','Total','Status','Payment','Date']);
                Order::with('user')->chunk(500, function ($orders) use ($file) {
                    foreach ($orders as $o) {
                        fputcsv($file, [$o->order_number, $o->user?->name ?? $o->shipping_name, $o->total, $o->status, $o->payment_status, $o->created_at->format('Y-m-d')]);
                    }
                });
            } elseif ($type === 'products') {
                fputcsv($file, ['ID','Name','SKU','Stock','Price','Category']);
                Product::with('category')->chunk(500, function ($products) use ($file) {
                    foreach ($products as $p) {
                        fputcsv($file, [$p->id, $p->name, $p->sku, $p->stock, $p->price, $p->category?->name]);
                    }
                });
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
