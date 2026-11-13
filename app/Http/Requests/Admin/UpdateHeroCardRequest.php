<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHeroCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg,avif', 'max:10240'],
            'url' => ['nullable', 'string', 'max:1000'],
            'link_text' => ['nullable', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان كارت الهيرو مطلوب.',
            'title.max' => 'عنوان كارت الهيرو يجب ألا يتجاوز 255 حرفاً.',
            'subtitle.max' => 'الوصف الفرعي يجب ألا يتجاوز 255 حرفاً.',
            'image.image' => 'الملف المرفوع يجب أن يكون صورة صالحة.',
            'image.mimes' => 'صيغ الصور المسموح بها هي: JPG, JPEG, PNG, WEBP, GIF, SVG, AVIF.',
            'image.max' => 'حجم الصورة كبير جداً، الحد الأقصى 10 ميغابايت.',
            'url.max' => 'رابط التوجيه طويل جداً.',
            'link_text.max' => 'نص الرابط يجب ألا يتجاوز 255 حرفاً.',
            'badge_text.max' => 'شارة التمييز يجب ألا تتجاوز 100 حرف.',
            'order.integer' => 'ترتيب العرض يجب أن يكون رقماً صحيحاً.',
        ];
    }
}

