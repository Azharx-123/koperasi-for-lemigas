<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_only_shows_active_products(): void
    {
        $active = Product::factory()->create(['name' => 'Kopi Robusta Aktif']);
        $inactive = Product::factory()->inactive()->create(['name' => 'Produk Nonaktif']);
        $draft = Product::factory()->create(['name' => 'Produk Draft', 'status' => 'draft']);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee($active->name);
        $response->assertDontSee($inactive->name);
        $response->assertDontSee($draft->name);
    }

    public function test_index_can_search_products_by_name(): void
    {
        $match = Product::factory()->create(['name' => 'Sarung Tangan Safety']);
        $other = Product::factory()->create(['name' => 'Helm Proyek']);

        $response = $this->get(route('products.index', ['search' => 'Sarung Tangan']));

        $response->assertOk();
        $response->assertSee($match->name);
        $response->assertDontSee($other->name);
    }

    public function test_index_can_filter_by_category(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        $productInA = Product::factory()->create(['category_id' => $categoryA->id, 'name' => 'Produk Kategori A']);
        $productInB = Product::factory()->create(['category_id' => $categoryB->id, 'name' => 'Produk Kategori B']);

        $response = $this->get(route('products.index', ['category' => $categoryA->id]));

        $response->assertOk();
        $response->assertSee($productInA->name);
        $response->assertDontSee($productInB->name);
    }

    public function test_index_can_sort_by_price_low_to_high(): void
    {
        $expensive = Product::factory()->create(['price' => 300000]);
        $cheap = Product::factory()->create(['price' => 50000]);
        $mid = Product::factory()->create(['price' => 150000]);

        $response = $this->get(route('products.index', ['sort' => 'price_low']));

        $response->assertOk();
        $response->assertViewHas('products', function ($products) use ($cheap, $mid, $expensive) {
            return $products->pluck('id')->all() === [$cheap->id, $mid->id, $expensive->id];
        });
    }

    public function test_index_can_sort_by_price_high_to_low(): void
    {
        $expensive = Product::factory()->create(['price' => 300000]);
        $cheap = Product::factory()->create(['price' => 50000]);
        $mid = Product::factory()->create(['price' => 150000]);

        $response = $this->get(route('products.index', ['sort' => 'price_high']));

        $response->assertOk();
        $response->assertViewHas('products', function ($products) use ($cheap, $mid, $expensive) {
            return $products->pluck('id')->all() === [$expensive->id, $mid->id, $cheap->id];
        });
    }

    public function test_index_can_sort_by_popularity(): void
    {
        $leastSold = Product::factory()->create(['sold_count' => 2]);
        $mostSold = Product::factory()->create(['sold_count' => 50]);
        $midSold = Product::factory()->create(['sold_count' => 10]);

        $response = $this->get(route('products.index', ['sort' => 'popular']));

        $response->assertOk();
        $response->assertViewHas('products', function ($products) use ($mostSold, $midSold, $leastSold) {
            return $products->pluck('id')->all() === [$mostSold->id, $midSold->id, $leastSold->id];
        });
    }

    public function test_show_displays_an_active_product(): void
    {
        $product = Product::factory()->create(['name' => 'Jaket Safety Lapangan']);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee($product->name);
    }

    public function test_show_returns_404_for_an_inactive_product(): void
    {
        $product = Product::factory()->inactive()->create();

        $response = $this->get(route('products.show', $product->slug));

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_a_nonexistent_slug(): void
    {
        $response = $this->get(route('products.show', 'produk-yang-tidak-ada'));

        $response->assertNotFound();
    }

    public function test_show_includes_related_products_from_the_same_category_only(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        $product = Product::factory()->create(['category_id' => $categoryA->id]);
        $sameCategorySibling = Product::factory()->create(['category_id' => $categoryA->id]);
        $otherCategoryProduct = Product::factory()->create(['category_id' => $categoryB->id]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertViewHas('relatedProducts', function ($relatedProducts) use ($product, $sameCategorySibling, $otherCategoryProduct) {
            $ids = $relatedProducts->pluck('id')->all();

            return in_array($sameCategorySibling->id, $ids, true)
                && ! in_array($product->id, $ids, true)
                && ! in_array($otherCategoryProduct->id, $ids, true);
        });
    }
}
