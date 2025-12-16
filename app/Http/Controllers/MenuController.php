<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    /**
     * Display a listing of the menus.
     */
    public function index(): Response
    {
        $menus = Menu::latest()->get();

        return Inertia::render('Menus/Index', [
            'menus' => $menus,
        ]);
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:makanan,minuman,tambahan',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_available' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $validated['is_available'] = $request->boolean('is_available', true);

        Menu::create($validated);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Update the specified menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:makanan,minuman,tambahan',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_available' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menu->image) {
                $oldPath = str_replace('/storage/', '', $menu->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('menus', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $validated['is_available'] = $request->boolean('is_available', true);

        $menu->update($validated);

        return redirect()->back()->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu)
    {
        // Delete associated image
        if ($menu->image) {
            $path = str_replace('/storage/', '', $menu->image);
            Storage::disk('public')->delete($path);
        }

        $menu->delete();

        return redirect()->back()->with('success', 'Menu berhasil dihapus!');
    }

    /**
     * Toggle menu availability.
     */
    public function toggle(Menu $menu)
    {
        $menu->update([
            'is_available' => !$menu->is_available,
        ]);

        $status = $menu->is_available ? 'tersedia' : 'tidak tersedia';

        return redirect()->back()->with('success', "Menu {$menu->name} sekarang {$status}.");
    }
}
