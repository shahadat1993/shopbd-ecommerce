<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function general()
    {
        $settings = Setting::getGroup('general');
        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $fields = ['site_name','site_tagline','site_email','site_phone','site_address','site_currency','site_currency_symbol','site_logo','site_favicon'];
        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                $old = Setting::get($field);
                if ($old) Storage::disk('public')->delete($old);
                Setting::set($field, $request->file($field)->store('settings', 'public'), 'general');
            } elseif ($request->has($field)) {
                Setting::set($field, $request->input($field), 'general');
            }
        }
        return back()->with('success', 'General settings updated.');
    }

    public function payment()
    {
        $settings = Setting::getGroup('payment');
        return view('admin.settings.payment', compact('settings'));
    }

    public function updatePayment(Request $request)
    {
        $fields = ['stripe_enabled','stripe_key','stripe_secret','sslcommerz_enabled','sslcommerz_store_id','sslcommerz_store_password','sslcommerz_sandbox','cod_enabled'];
        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, '0'), 'payment');
        }
        return back()->with('success', 'Payment settings updated.');
    }

    public function email()
    {
        $settings = Setting::getGroup('email');
        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmail(Request $request)
    {
        $fields = ['mail_driver','mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name'];
        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, ''), 'email');
        }
        return back()->with('success', 'Email settings updated.');
    }

    public function shipping()
    {
        $settings = Setting::getGroup('shipping');
        return view('admin.settings.shipping', compact('settings'));
    }

    public function updateShipping(Request $request)
    {
        $fields = ['free_shipping_enabled','free_shipping_min','flat_rate_enabled','flat_rate_amount','shipping_zones'];
        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, ''), 'shipping');
        }
        return back()->with('success', 'Shipping settings updated.');
    }
}
