<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feature;
use App\Models\Carousel_item;
use App\Models\Persona;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sponsor;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $features = Feature::where('is_active', true)->orderBy('order')->get();
        $carousel_items = Carousel_item::where('is_active', true)
            ->orderBy('order')
            ->get();

        $best_products = Product::where('featured', true)->take(4)->get();
        $categories = Category::active()->get();
        $personas = Persona::where('is_active', true)
            ->orderBy('order')
            ->get();

        $sponsors = Sponsor::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('welcome', compact(
            'carousel_items',
            'features',
            'best_products',
            'categories',
            'personas',
            'sponsors'
        ));
    }

    public function showProduct($slug)
    {
        // Get the product by slug
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();

        // Get related products (same category, different product)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
