<?php

namespace App\Http\Requests\Storefront;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitize phone number, convert Arabic numbers, strip spaces and formatting before validation
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $phone = (string)$this->input('phone');
            
            // Convert Arabic/Eastern numbers to standard ASCII digits
            $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $phone = str_replace($arabicNumbers, $englishNumbers, $phone);
            
            // Keep leading '+' if international, strip other symbols/spaces/brackets
            $hasPlus = str_starts_with(trim($phone), '+');
            $digitsOnly = preg_replace('/[^0-9]/', '', $phone);
            $normalizedPhone = ($hasPlus ? '+' : '') . $digitsOnly;

            $this->merge(['phone' => $normalizedPhone]);
        }

        // Normalize payment_method if sent as 'cash' or 'cod'
        if ($this->has('payment_method')) {
            $pm = (string) $this->input('payment_method');
            if ($pm === 'cash' || $pm === 'cod') {
                $pm = ($this->input('delivery_type') === Order::DELIVERY_TYPE_PICKUP)
                    ? Order::PAYMENT_METHOD_BRANCH
                    : Order::PAYMENT_METHOD_CASH;
            }
            $this->merge(['payment_method' => $pm]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:150',
            'phone' => ['required', 'string', 'min:7', 'max:25', 'regex:/^\+?[0-9]{7,20}$/'],
            'email' => 'nullable|email|max:150',
            'delivery_type' => 'required|in:' . Order::DELIVERY_TYPE_DELIVERY . ',' . Order::DELIVERY_TYPE_PICKUP,
            'branch_id' => 'required|exists:branches,id',
            'address' => 'required_if:delivery_type,' . Order::DELIVERY_TYPE_DELIVERY . '|nullable|string|min:5|max:300',
            'payment_method' => [
                'required',
                'string',
                'in:' . implode(',', [
                    Order::PAYMENT_METHOD_CASH,
                    Order::PAYMENT_METHOD_INSTAPAY,
                    Order::PAYMENT_METHOD_VODAFONE_CASH,
                    Order::PAYMENT_METHOD_BRANCH,
                    Order::PAYMENT_METHOD_WHATSAPP,
                    'whatsapp',
                    'whatsapp_checkout',
                    'cash',
                    'cod',
                ]),
            ],
            'payment_proof_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'instapay_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يرجى كتابة الاسم بالكامل.',
            'name.min' => 'الاسم يجب ألا يقل عن 3 أحرف.',
            'name.max' => 'الاسم لا يجب أن يتجاوز 150 حرفاً.',
            'phone.required' => 'يرجى كتابة رقم الهاتف للتواصل والتوصيل.',
            'phone.min' => 'رقم الهاتف قصير جداً، يرجى التأكد من كتابة الرقم كاملاً مع كود الدولة.',
            'phone.max' => 'رقم الهاتف طويل جداً، يرجى التأكد من الرقم المدخل.',
            'phone.regex' => 'يرجى إدخال رقم هاتف صحيح (أرقام فقط، مع إمكانية إضافة + في البداية للأرقام الدولية).',
            'delivery_type.required' => 'يرجى اختيار طريقة الاستلام (توصيل للمنزل أو استلام من الفرع).',
            'delivery_type.in' => 'طريقة الاستلام المحددة غير صالحة.',
            'branch_id.required' => 'يرجى اختيار فرع حورس.',
            'branch_id.exists' => 'الفرع المحدد غير موجود في سجلاتنا.',
            'address.required_if' => 'يرجى كتابة عنوان التوصيل بالتفصيل (المنطقة، الشارع، رقم العقار).',
            'address.min' => 'العنوان قصير جداً، يرجى توضيح المنطقة والشارع ورقم المنزل.',
            'payment_method.required' => 'يرجى اختيار طريقة الدفع المناسبة.',
            'payment_method.in' => 'طريقة الدفع المختارة غير صالحة.',
            'payment_proof_image.image' => 'الملف المرفق يجب أن يكون صورة لإيصال التحويل.',
            'payment_proof_image.mimes' => 'صيغ الصور المقبولة للإيصال هي: jpeg, png, jpg, webp.',
            'payment_proof_image.max' => 'حجم صورة الإيصال لا يجب أن يتجاوز 5 ميجابايت.',
            'instapay_reference.max' => 'الرقم أو الحساب المحوّل منه يجب ألا يتجاوز 100 حرف.',
            'notes.max' => 'الملاحظات لا يجب أن تتجاوز 500 حرف.',
        ];
    }
}
