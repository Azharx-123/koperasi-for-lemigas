<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\CarouselItem;
use App\Models\Persona;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sponsor;

class WelcomeController extends Controller
{
    public function index()
    {
        $features = Feature::where('is_active', true)->orderBy('order')->get();
        $carousel_items = CarouselItem::where('is_active', true)
            ->orderBy('order')
            ->get();

        $best_products = Product::active()->featured()->take(4)->get();
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
}
