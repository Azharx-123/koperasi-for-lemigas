<?php

namespace App\Http\Controllers;

use App\Models\Product;

class SitemapController extends Controller
{
    /**
     * robots.txt used to be a static file in public/ with no Sitemap: line
     * (and no way for a static file to know the real domain anyway). Now
     * generated so the Sitemap: line always points at the actual APP_URL
     * instead of a guessed/hardcoded domain.
     */
    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Disallow:',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines))->header('Content-Type', 'text/plain');
    }

    /**
     * Hand-rolled XML sitemap — no package needed for a handful of static
     * routes plus one product loop. Lists only public, indexable GET pages:
     * nothing that sits behind auth (cart, checkout, orders, profile) and
     * no write/action endpoints.
     */
    public function index()
    {
        $urls = [
            ['loc' => route('welcome'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('products.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
        ];

        Product::active()
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at'])
            ->each(function ($product) use (&$urls) {
                $urls[] = [
                    'loc' => route('products.show', $product->slug),
                    'lastmod' => optional($product->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            });

        return response(view('sitemap', ['urls' => $urls])->render())
            ->header('Content-Type', 'text/xml');
    }
}
