<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class APIProductStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->headers = [
            'Authorisation' => config('app.api_key')
        ];
    }

    public function test_post_product_successful() {
        $productData = [
            'name' => 'test name',
            'description' => 'test description',
            'slug' => 'test-slug',
            'price' => 100,
            'active' => true
        ];

        $response = $this->post('/api/product', $productData, $this->headers);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'description' => $productData['description'],
            'slug' => $productData['slug'],
            'price' => $productData['price'],
            'active' => $productData['active'],

        ]);

        $this->assertDatabaseCount('products', 1);
    }

    public function test_post_product_missing_data() {
        $productData = [];

        $this->post('/api/product', $productData, $this->headers);

        $this->assertDatabaseCount('products', 0);
    }

    // test still creates a record if there is an extra 'wrong' field
    public function test_post_product_extra_data() {
        $productData = [
            'name' => 'test name',
            'description' => 'test description',
            'slug' => 'test-slug',
            'price' => 100,
            'active' => true,
            'extra_field' => 'extra'
        ];

        $this->post('/api/product', $productData, $this->headers);

        $this->assertDatabaseCount('products', 1);
    }

    public function test_post_product_wrong_data_type() {
        $productData = [
            'name' => 1, // wrong datatype - should be string
            'description' => 'test description',
            'slug' => 'test-slug',
            'price' => 100,
            'active' => true
        ];

        $this->post('/api/product', $productData, $this->headers);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_post_product_missing_authorisation() {
        $productData = [
            'name' => 'test name',
            'description' => 'test description',
            'slug' => 'test-slug',
            'price' => 100,
            'active' => true
        ];

        $headersWithoutAuth = [];

        $this->post('/api/product', $productData, $headersWithoutAuth);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_post_product_duplicate_slug() {
        Product::factory()
            ->mockData()
            ->hasImages(4)
            ->hasDiscounts(1)
            ->create();

        $productData = [
            'name' => 'test name 2',
            'description' => 'test description 2',
            'slug' => 'mock-slug',
            'price' => 100,
            'active' => true
        ];

        $this->post('/api/product', $productData, $this->headers);

        $this->assertDatabaseCount('products', 1);
    }


}
