<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WhatsappClick;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class WhatsappService
{
    /**
     * Build WhatsApp URL with order details message
     */
    public function buildOrderWhatsappUrl(Order $order, array $cartItems, array $customerData, ?User $user = null): string
    {
        $phone = $this->getStoreWhatsappNumber();
        $message = $this->formatOrderMessage($order, $cartItems, $customerData);

        // Track click analytics with full customer and order details
        $this->trackClick(
            clickType: 'checkout',
            userId: $user?->id,
            orderData: [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $customerData['name'] ?? '',
                'customer_phone' => $customerData['phone'] ?? '',
                'customer_address' => $customerData['address'] ?? '',
                'customer_notes' => $customerData['notes'] ?? '',
                'total' => (float)$order->total_price,
                'currency' => $order->currency ?? 'USD',
                'formatted_total' => format_currency((float)$order->total_price, $order->currency ?? 'USD'),
                'items_count' => count($cartItems),
                'items' => array_map(fn($item) => [
                    'name' => $item['name'] ?? '',
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                    'total' => $item['total'] ?? 0,
                ], array_values($cartItems)),
            ],
            sourcePage: 'checkout'
        );

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    /**
     * Build WhatsApp URL with live cart items and total breakdown
     */
    public function buildCartWhatsappUrl(array $cartItems, array $totals, ?User $user = null): string
    {
        $phone = $this->getStoreWhatsappNumber();
        $storeName = settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات');
        $currency = get_active_currency();

        $msg = "👑 *طلب وتأكيد مقتنيات السلة | {$storeName}*\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "مرحباً، أود إتمام وطلب المنتجات الموجودة في سلة التسوق الخاصة بي:\n\n";

        $itemCounter = 1;
        foreach (array_values($cartItems) as $item) {
            $name = $item['name'] ?? 'منتج';
            $qty = (int)($item['quantity'] ?? 1);
            $unitPrice = (float)($item['price'] ?? 0);
            $totalPrice = (float)($item['total'] ?? ($unitPrice * $qty));
            $priceFormatted = format_currency($unitPrice, $currency);
            $totalFormatted = format_currency($totalPrice, $currency);

            $msg .= "{$itemCounter}. *{$name}*\n";
            $msg .= "   • الكمية: {$qty}\n";
            $msg .= "   • السعر: {$priceFormatted}\n";
            $msg .= "   • الإجمالي: {$totalFormatted}\n\n";
            $itemCounter++;
        }

        $subtotal = (float)($totals['subtotal'] ?? 0);
        $grandTotal = (float)($totals['grand_total'] ?? $subtotal);

        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "💵 *المجموع الإجمالي:* " . format_currency($grandTotal, 'USD') . "\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "يرجى تزويدي بتفاصيل الشحن والتوصيل. شكراً لكم!";

        return "https://wa.me/{$phone}?text=" . urlencode($msg);
    }

    /**
     * Build general inquiry WhatsApp URL for a product
     */
    public function buildProductInquiryUrl(string $productName, float $price, ?string $imageUrl = null, ?User $user = null): string
    {
        $phone = $this->getStoreWhatsappNumber();
        $storeName = settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات');

        $message = "👑 *{$storeName} | استفسار عن منتج*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "مرحباً، أود الاستفسار عن توفر وسعر المنتج التالي:\n\n";
        $message .= "🏷️ *المنتج:* {$productName}\n";
        $message .= "💰 *السعر:* " . format_currency($price) . "\n";

        if ($imageUrl) {
            $message .= "🖼️ *رابط الصورة:* {$imageUrl}\n";
        }

        $message .= "\n━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "يرجى تزويدي بكافة التفاصيل وطريقة الشحن. شكراً لكم!";

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    /**
     * Format the complete WhatsApp order message
     */
    public function formatOrderMessage(Order $order, array $cartItems, array $customerData): string
    {
        $storeName = settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات');
        $currency = $order->currency ?? 'EGP';
        $isPickup = ($customerData['delivery_type'] ?? $order->delivery_type) === Order::DELIVERY_TYPE_PICKUP;
        $branchName = $order->branch?->name ?? 'فرع مؤسسة حورس';

        $msg = "🦅 *طلب جديد عبر واتساب | {$storeName}*\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "🔖 *رقم الطلب:* `{$order->order_number}`\n";
        $msg .= "👤 *اسم العميل:* " . ($customerData['name'] ?? $order->customer_name) . "\n";
        $msg .= "📞 *رقم الهاتف:* " . ($customerData['phone'] ?? $order->phone) . "\n";

        if ($isPickup) {
            $msg .= "🏪 *طريقة الاستلام:* استلام من الفرع ({$branchName})\n";
        } else {
            $address = $customerData['address'] ?? $order->address;
            $msg .= "🚚 *طريقة الاستلام:* توصيل للمنزل\n";
            $msg .= "📍 *عنوان التوصيل:* {$address}\n";
        }

        if (!empty($customerData['notes'] ?? $order->notes)) {
            $notes = $customerData['notes'] ?? $order->notes;
            $msg .= "📝 *ملاحظات الطلب:* {$notes}\n";
        }

        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "📦 *تفاصيل المنتجات المحجوزة:*\n\n";

        $itemCounter = 1;
        foreach (array_values($cartItems) as $item) {
            $name = $item['name'] ?? 'منتج';
            $qty = (int)($item['quantity'] ?? 1);
            $portion = !empty($item['portion_label']) ? " ({$item['portion_label']})" : '';
            $unitPrice = (float)($item['price'] ?? 0);
            $totalPrice = (float)($item['total'] ?? ($unitPrice * $qty));
            $priceFormatted = number_format($unitPrice, 2) . ' ج.م';
            $totalFormatted = number_format($totalPrice, 2) . ' ج.م';

            $msg .= "{$itemCounter}. *{$name}*{$portion}\n";
            $msg .= "   • الكمية: {$qty} × {$priceFormatted}\n";
            $msg .= "   • الإجمالي: {$totalFormatted}\n\n";
            $itemCounter++;
        }

        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $orderTotal = (float) $order->total_price;
        $msg .= "💵 *الإجمالي النهائي:* " . number_format($orderTotal, 2) . " ج.م\n";
        $msg .= "🔒 *حالة الحجز:* تم خصم وحجز المنتجات من المخزون بنجاح\n";
        $msg .= "🔗 *رابط تتبع الطلب:*\n" . route('orders.track', $order->public_order_id) . "\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "أرجو تأكيد تجهيز الطلب وموعد الاستلام/التوصيل. شكراً لكم!";

        return $msg;
    }

    /**
     * Track a click in database for analytics
     */
    public function trackClick(string $clickType, ?int $userId = null, array $orderData = [], ?string $sourcePage = null): void
    {
        try {
            WhatsappClick::create([
                'user_id' => $userId,
                'click_type' => $clickType,
                'source_page' => $sourcePage ?? Request::path(),
                'order_data' => $orderData,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Fail silently to never break the customer journey
        }
    }

    /**
     * Clean and retrieve the store WhatsApp number
     */
    protected function getStoreWhatsappNumber(): string
    {
        $raw = settings('store_whatsapp', '201002527072');
        // Remove all non-numeric characters except leading plus if any
        return preg_replace('/[^0-9]/', '', $raw);
    }
}
