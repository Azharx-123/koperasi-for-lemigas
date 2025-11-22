<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rating;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Store a new rating or update existing
     */
    public function store(Request $request, Product $product)
    {
        // Validasi request
        $request->validate([
            'score' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
        ]);

        // Cek apakah user pernah membeli produk
        $verifiedPurchase = Order::where('user_id', Auth::id())
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->where('status', 'delivered')
            ->exists();

        // Cari rating yang sudah ada atau buat baru
        $rating = Rating::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ],
            [
                'score' => $request->score,
                'review' => $request->review,
                'verified_purchase' => $verifiedPurchase,
                // Auto-approve jika verified purchase
                'is_approved' => $verifiedPurchase,
            ]
        );

        // Refresh cache rating
        $product->refreshRatingCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil disimpan',
                'rating' => $rating,
                'product_rating' => [
                    'average' => $product->rating_average,
                    'count' => $product->rating_count,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Rating berhasil disimpan');
    }

    /**
     * Display ratings for a product
     */
    public function index(Product $product)
    {
        $ratings = $product->ratings()
            ->approved()
            ->with('user')
            ->orderBy('verified_purchase', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $userRating = null;

        if (Auth::check()) {
            $userRating = $product->ratings()
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('ratings.index', compact('product', 'ratings', 'userRating'));
    }
}
