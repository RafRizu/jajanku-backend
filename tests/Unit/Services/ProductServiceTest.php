<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;
    private Shop $shop;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();

        $seller = User::factory()->create();

        $this->shop = Shop::create([
            'user_id' => $seller->id,
            'name' => 'Warung Nasi Goreng',
            'address' => 'Jl. Merdeka No. 10',
            'phone' => '08111222333',
            'is_open' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Makanan',
            'slug' => 'makanan',
        ]);
    }

    public function test_can_create_product_without_image(): void
    {
        $product = $this->productService->createProduct([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'name' => 'Mie Goreng Telur',
            'price' => 12000,
            'is_available' => true,
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Mie Goreng Telur', $product->name);
        $this->assertEquals(12000, $product->price);
        $this->assertNull($product->image);
    }

    public function test_can_create_product_with_image(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('mie_goreng.jpg');

        $product = $this->productService->createProduct([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'name' => 'Mie Goreng Spesial',
            'price' => 15000,
            'is_available' => true,
        ], $image);

        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_can_toggle_product_availability(): void
    {
        $product = Product::create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'name' => 'Ayam Bakar',
            'price' => 20000,
            'is_available' => true,
        ]);

        $updatedProduct = $this->productService->toggleAvailability($product);

        $this->assertFalse($updatedProduct->is_available);

        $reToggledProduct = $this->productService->toggleAvailability($updatedProduct);

        $this->assertTrue($reToggledProduct->is_available);
    }

    public function test_can_update_product_and_replace_image(): void
    {
        Storage::fake('public');
        $oldImage = UploadedFile::fake()->image('old.jpg');

        $product = $this->productService->createProduct([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'name' => 'Es Jeruk',
            'price' => 4000,
            'is_available' => true,
        ], $oldImage);

        $oldImagePath = $product->image;
        Storage::disk('public')->assertExists($oldImagePath);

        $newImage = UploadedFile::fake()->image('new.jpg');
        $updatedProduct = $this->productService->updateProduct($product, [
            'price' => 5000,
        ], $newImage);

        $this->assertEquals(5000, $updatedProduct->price);
        Storage::disk('public')->assertMissing($oldImagePath);
        Storage::disk('public')->assertExists($updatedProduct->image);
    }

    public function test_can_delete_product_and_remove_image(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('delete_me.jpg');

        $product = $this->productService->createProduct([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'name' => 'Soto Ayam',
            'price' => 15000,
            'is_available' => true,
        ], $image);

        $imagePath = $product->image;

        $this->productService->deleteProduct($product);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }
}
