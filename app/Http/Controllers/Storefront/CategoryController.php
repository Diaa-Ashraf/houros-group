<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Display a listing of all active supermarket categories.
     */
    public function index(): View
    {
        $horusSlugs = ['scib-paints', 'gypsum-board', 'fyutec-cornices', 'wall-panels', 'construction-tools', 'finishing-materials', 'doors', 'decor'];
        $categories = Cache::remember('horus_storefront_categories_page_v1_' . request('page', 1), 1800, function () use ($horusSlugs) {
            return Category::select(['id', 'name', 'slug', 'image', 'description'])
                ->active()
                ->whereIn('slug', $horusSlugs)
                ->withCount(['products' => function ($q) {
                    $q->active();
                }])
                ->ordered()
                ->paginate(16);
        });

        return view('storefront.categories.index', compact('categories'));
    }

    /**
     * Display products belonging to a specific category with filters.
     */
    public function show(Request $request, int|string $id): View
    {
        // Find by ID or Slug
        $horusSlugs = ['scib-paints', 'gypsum-board', 'fyutec-cornices', 'wall-panels', 'construction-tools', 'finishing-materials', 'doors', 'decor'];
        $categoryQuery = Category::whereIn('slug', $horusSlugs);
        $category = is_numeric($id)
            ? $categoryQuery->whereKey($id)->firstOrFail()
            : $categoryQuery->where('slug', $id)->firstOrFail();

        $query = Product::select([
            'id',
            'name',
            'slug',
            'image',
            'price',
            'discount_price',
            'featured',
            'is_available',
            'stock_quantity',
            'quantity',
            'category_id',
            'created_at',
        ])
        ->where('category_id', $category->id)
        ->active()
        ->with(['category:id,name']);

        // Filter: Deals only
        if ($request->boolean('deals_only')) {
            $query->whereNotNull('discount_price')->where('discount_price', '>', 0);
        }

        // Filter: In stock only
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        // Filter: Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Sort
        $sort = $request->query('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->latest('id'),
        };

        $products = $query->paginate(16)->withQueryString();

        // Other categories for sidebar (cached for 1 hour)
        $otherCategories = Cache::remember('horus_storefront_sidebar_categories_v1', 3600, function () use ($horusSlugs) {
            return Category::select(['id', 'name', 'slug'])
                ->active()
                ->whereIn('slug', $horusSlugs)
                ->withCount(['products' => fn($q) => $q->active()])
                ->ordered()
                ->get();
        });

        return view('storefront.categories.show', compact('category', 'products', 'otherCategories', 'sort'));
    }
}
