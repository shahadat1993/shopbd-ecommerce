<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->paginate(15);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'image'    => 'required|image|max:4096',
            'position' => 'required|in:hero,sidebar,promo',
        ]);
        $data = $request->only('title', 'subtitle', 'link', 'position', 'sort_order');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['image']     = $request->file('image')->store('banners', 'public');
        Banner::create($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate(['title' => 'required|string|max:255', 'image' => 'nullable|image|max:4096']);
        $data = $request->only('title', 'subtitle', 'link', 'position', 'sort_order');
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $data['image'] = $request->file('image')->store('banners', 'public');
        }
        $banner->update($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted.');
    }
}
