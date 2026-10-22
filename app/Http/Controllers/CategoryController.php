<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = auth()->user()
            ->categories()
            ->orderBy('id', 'desc')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
   {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = auth()->user();

        $user->categories()->create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'カテゴリを登録しました！');
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'カテゴリ名を変更しました！');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'カテゴリを削除しました！');
    }
}
