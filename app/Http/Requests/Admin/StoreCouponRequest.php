<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'product_id' => ['nullable', 'exists:products,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'كود الكوبون مطلوب.',
            'code.unique' => 'كود الكوبون موجود مسبقاً، يرجى اختيار كود آخر.',
            'type.required' => 'نوع الخصم مطلوب.',
            'type.in' => 'نوع الخصم يجب أن يكون قيمة ثابتة أو نسبة مئوية.',
            'value.required' => 'قيمة الخصم مطلوبة.',
            'value.min' => 'قيمة الخصم يجب أن تكون أكبر من الصفر.',
            'expires_at.after_or_equal' => 'تاريخ انتهاء الكوبون يجب أن يكون بعد أو مساوياً لتاريخ البدء.',
        ];
    }
}
