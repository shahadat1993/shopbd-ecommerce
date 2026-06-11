<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'today_revenue' => Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total'),
            'total_products' => Product::count(),
            'low_stock' => Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'total_customers' => User::role('customer')->count(),
            'new_customers' => User::role('customer')->whereDate('created_at', today())->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
        ];

        // Recent Orders
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(8)
            ->get();

        // Monthly Revenue (last 6 months)
        $monthlyRevenue = [];
        $monthlyOrders = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => Order::where('payment_status', 'paid')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total'),
            ];
            $monthlyOrders[] = [
                'month' => $month->format('M Y'),
                'count' => Order::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        // Top Products
        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        // Top Categories
        $topCategories = Category::withCount('products')
            ->orderByDesc('products_count')
            ->take(5)
            ->get();

        // Order Status Distribution
        $orderStatusData = [
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.dashboard.index', compact(
            'stats',
            'recentOrders',
            'monthlyRevenue',
            'monthlyOrders',
            'topProducts',
            'topCategories',
            'orderStatusData'
        ));
    }
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $results = [];

        // Products
        $products = \App\Models\Product::where('name', 'like', "%$q%")
            ->orWhere('sku', 'like', "%$q%")
            ->take(5)->get();
        foreach ($products as $p) {
            $results[] = [
                'type' => 'Product',
                'title' => $p->name,
                'meta' => '৳' . number_format($p->current_price, 0),
                'url' => route('admin.products.edit', $p),
                'image' => $p->thumbnail_url,
            ];
        }

        // Orders
        $orders = \App\Models\Order::where('order_number', 'like', "%$q%")
            ->orWhere('shipping_name', 'like', "%$q%")
            ->take(3)->get();
        foreach ($orders as $o) {
            $results[] = [
                'type' => 'Order',
                'title' => '#' . $o->order_number,
                'meta' => $o->shipping_name . ' · ৳' . number_format($o->total, 0),
                'url' => route('admin.orders.show', $o),
                'image' => asset('sneat-assets/img/icons/misc/cart.png'),
            ];
        }

        return response()->json($results);
    }
}


