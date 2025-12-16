<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
        $menus = Menu::with('category')->latest()->get();
        $categories = Category::orderBy('name')->get();

        return Inertia::render('Menus/Index', [
            'menus' => $menus,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $validated['image'] = '/storage/' . $path;
        }

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
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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
}
