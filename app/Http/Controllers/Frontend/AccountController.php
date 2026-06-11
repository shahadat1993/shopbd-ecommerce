<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function dashboard()
    {
        $user   = auth()->user();
        $orders = Order::where('user_id', $user->id)->latest()->take(5)->get();
        $stats  = [
            'total_orders'     => Order::where('user_id', $user->id)->count(),
            'pending_orders'   => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'delivered_orders' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
            'wishlist_count'   => $user->wishlist()->count(),
        ];
        return view('frontend.account.dashboard', compact('user', 'orders', 'stats'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())->with('items')->latest()->paginate(10);
        return view('frontend.account.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        $order->load('items.product');
        return view('frontend.account.order-detail', compact('order'));
    }

    public function profile()
    {
        return view('frontend.account.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = [
            'name'  => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Delete old avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password changed successfully.');
    }

    public function addresses()
    {
        $addresses = auth()->user()->addresses()->get();
        return view('frontend.account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label'   => 'required|string|max:50',
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'address' => 'required|string',
            'city'    => 'required|string',
            'state'   => 'nullable|string',
            'zip'     => 'nullable|string',
        ]);

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $validated['user_id']    = auth()->id();
        $validated['is_default'] = $request->boolean('is_default');
        Address::create($validated);

        return back()->with('success', 'Address saved successfully.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);
        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }
        $address->update(array_merge(
            $request->only('label','name','phone','address','city','state','zip'),
            ['is_default' => $request->boolean('is_default')]
        ));
        return back()->with('success', 'Address updated.');
    }

    public function deleteAddress(Address $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);
        $address->delete();
        return back()->with('success', 'Address deleted.');
    }
}
