<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->withRatingAggregates()->active();

        // Handle search
        //
        // Sebelumnya ini cuma mencocokkan SATU frasa persis ke name/
        // short_description saja. Itu bikin banyak produk yang jelas-jelas
        // ada di database "tidak ditemukan" begitu kata kuncinya:
        // - ada di kolom lain (description panjang, atau nama kategori)
        // - urutan katanya beda dari nama produk (mis. cari "safety sepatu"
        //   tidak ketemu produk bernama "Sepatu Safety Ukuran 42", padahal
        //   jelas relevan)
        // Fix: pecah pencarian jadi kata per kata (AND antar kata, supaya
        // "sepatu safety" tetap match walau urutan/tambahan kata beda),
        // dan tiap kata dicek ke name + short_description + description +
        // nama kategori (OR antar kolom). '%' dan '_' di-escape supaya
        // tidak diperlakukan sebagai wildcard LIKE kalau kebetulan diketik
        // user.
        if ($request->filled('search')) {
            $terms = preg_split('/\s+/', trim((string) $request->search), -1, PREG_SPLIT_NO_EMPTY);

            foreach ($terms as $term) {
                $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term);
                $like = '%' . $escaped . '%';

                $query->where(function ($q) use ($like) {
                    $q->where('name', 'like', $like)
                        ->orWhere('short_description', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhereHas('category', function ($categoryQuery) use ($like) {
                            $categoryQuery->where('name', 'like', $like);
                        });
                });
            }
        }

        // Handle category filter
        if ($request->has('category') && $request->category !== '') {
            $query->where('category_id', $request->category);
        }

        // Handle sorting
        switch ($request->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('sold_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with(['category'])
            ->withRatingAggregates()
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Get related products from same category
        $relatedProducts = Product::with('category')
            ->withRatingAggregates()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->limit(4)
            ->get();

        // Rating & ulasan langsung ditampilkan di halaman produk (sebelumnya
        // fitur ini cuma bisa diakses lewat halaman /products/{slug}/ratings
        // yang tidak ditautkan dari mana pun, jadi praktis tidak pernah
        // ditemukan pengguna). $userRating dipakai untuk mem-prefill form
        // kalau user yang login sudah pernah kasih rating sebelumnya, dan
        // untuk menampilkan status "menunggu persetujuan" kalau rating-nya
        // belum di-approve admin.
        $userRating = auth()->check()
            ? $product->ratings()->where('user_id', auth()->id())->first()
            : null;

        $previewRatings = $product->ratings()
            ->approved()
            ->with('user')
            ->orderBy('verified_purchase', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('products.show', compact('product', 'relatedProducts', 'userRating', 'previewRatings'));
    }
}
