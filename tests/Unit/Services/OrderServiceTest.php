<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Bundle;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
        $this->category = Category::create([
            'name' => 'البقالة والتموين',
            'slug' => 'grocery',
            'image' => 'default-cat.jpg',
            'is_active' => true,
        ]);
    }

    public function test_creates_order_and_deducts_stock_properly(): void
    {
        $product = Product::create([
            'name' => 'سكر أبيض فاخر 1ك',
            'slug' => 'white-sugar-1kg',
            'category_id' => $this->category->id,
            'price' => 35.00,
            'stock_quantity' => 15,
            'quantity' => 15,
            'is_active' => true,
            'is_available' => true,
        ]);

        $customerData = [
            'name' => 'أحمد محمود',
            'phone' => '01012345678',
            'address' => 'العامرية، شارع السوق',
            'payment_method' => Order::PAYMENT_METHOD_CASH,
            'delivery_type' => Order::DELIVERY_TYPE_DELIVERY,
        ];

        $cartItems = [
            'product_' . $product->id => [
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'price' => 35.00,
                'quantity' => 3,
                'total' => 105.00,
            ],
        ];

        $totals = [
            'subtotal' => 105.00,
            'delivery_fee' => 15.00,
            'discount_amount' => 0.00,
            'grand_total' => 120.00,
        ];

        $order = $this->orderService->createOrder($customerData, $cartItems, $totals);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'customer_name' => 'أحمد محمود',
            'total_price' => 120.00,
            'status' => Order::STATUS_PENDING,
            'payment_method' => Order::PAYMENT_METHOD_CASH,
        ]);

        // Stock should be deducted from 15 to 12
        $product->refresh();
        $this->assertEquals(12, $product->stock_quantity);
        $this->assertEquals(12, $product->quantity);
        $this->assertEquals(12, $product->available_stock);
    }

    public function test_restores_stock_when_order_is_cancelled(): void
    {
        $product = Product::create([
            'name' => 'أرز مصري عريض الحبة 1ك',
            'slug' => 'egyptian-rice-1kg',
            'category_id' => $this->category->id,
            'price' => 30.00,
            'stock_quantity' => 10,
            'quantity' => 10,
            'is_active' => true,
            'is_available' => true,
        ]);

        $order = $this->orderService->createOrder(
            [
                'name' => 'علي سالم',
                'phone' => '01298765432',
                'address' => 'العامرية، كينج مريوط',
                'payment_method' => Order::PAYMENT_METHOD_CASH,
            ],
            [
                'product_' . $product->id => [
                    'type' => 'product',
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 30.00,
                    'quantity' => 4,
                    'total' => 120.00,
                ],
            ],
            ['grand_total' => 120.00]
        );

        $product->refresh();
        $this->assertEquals(6, $product->stock_quantity);

        // Cancel order -> should restore stock back to 10
        $this->orderService->updateStatus($order, Order::STATUS_CANCELLED, 'العميل طلب الإلغاء');

        $product->refresh();
        $this->assertEquals(10, $product->stock_quantity);
        $this->assertEquals(10, $product->quantity);
        $this->assertEquals(10, $product->available_stock);
        $this->assertEquals(Order::STATUS_CANCELLED, $order->fresh()->status);

        $this->orderService->updateStatus($order, Order::STATUS_PENDING);
        $product->refresh();
        $this->assertEquals(6, $product->stock_quantity);
        $this->assertEquals(6, $product->quantity);
        $this->assertEquals(6, $product->available_stock);
    }

    public function test_rejects_inactive_product_still_present_in_cart(): void
    {
        $product = Product::create([
            'name' => 'منتج متوقف', 'slug' => 'inactive-item', 'category_id' => $this->category->id,
            'price' => 20, 'stock_quantity' => 8, 'quantity' => 8,
            'is_active' => false, 'is_available' => true,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('لم يعد متاحاً');

        $this->orderService->createOrder(['phone' => '01012345678'], [[
            'type' => 'product', 'id' => $product->id, 'price' => 20, 'quantity' => 1, 'total' => 20,
        ]], ['grand_total' => 20]);
    }

    public function test_rejects_price_changed_since_product_was_added_to_cart(): void
    {
        $product = Product::create([
            'name' => 'منتج تغير سعره', 'slug' => 'changed-price-item', 'category_id' => $this->category->id,
            'price' => 25, 'stock_quantity' => 8, 'quantity' => 8,
            'is_active' => true, 'is_available' => true,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('تغير سعر المنتج');

        $this->orderService->createOrder(['phone' => '01012345678'], [[
            'type' => 'product', 'id' => $product->id, 'price' => 20, 'quantity' => 1, 'total' => 20,
        ]], ['grand_total' => 20]);
    }

    public function test_bundle_components_are_saved_on_order_and_reserved_by_component_quantity(): void
    {
        $first = Product::create([
            'name' => 'مكون أول', 'slug' => 'bundle-component-one', 'category_id' => $this->category->id,
            'price' => 30, 'stock_quantity' => 10, 'quantity' => 10, 'is_active' => true, 'is_available' => true,
        ]);
        $second = Product::create([
            'name' => 'مكون ثان', 'slug' => 'bundle-component-two', 'category_id' => $this->category->id,
            'price' => 20, 'stock_quantity' => 10, 'quantity' => 10, 'is_active' => true, 'is_available' => true,
        ]);
        $bundle = Bundle::create([
            'name' => 'عرض تشطيب', 'original_total' => 50, 'bundle_price' => 45, 'is_active' => true,
        ]);
        $bundle->products()->attach([
            $first->id => ['quantity' => 2],
            $second->id => ['quantity' => 1],
        ]);

        $order = $this->orderService->createOrder(
            ['name' => 'عميل العرض', 'phone' => '01012345678'],
            [[
                'type' => 'bundle', 'id' => $bundle->id, 'name' => $bundle->name,
                'price' => 45, 'quantity' => 1, 'total' => 45,
            ]],
            ['grand_total' => 45]
        );

        $this->assertCount(2, $order->orderItems()->get());
        $this->assertEquals(2, $order->orderItems()->where('product_id', $first->id)->value('quantity'));
        $this->assertEquals(8, $first->fresh()->available_stock);
        $this->assertEquals(9, $second->fresh()->available_stock);
        $this->assertEquals(45, (float) $order->orderItems()->sum('total_price'));
    }
}
