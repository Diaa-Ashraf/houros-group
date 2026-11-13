<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        $imagePath = $this->image;

        if (empty($imagePath)) {
            $imagePath = $this->getDefaultImageForCategory();
        }

        if (!$imagePath) {
            return null;
        }

        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }

        if (file_exists(public_path($imagePath))) {
            return asset($imagePath);
        }

        if (Storage::disk('public')->exists($imagePath)) {
            return Storage::disk('public')->url($imagePath);
        }

        if (Storage::disk('public')->exists("categories/{$imagePath}")) {
            return Storage::disk('public')->url("categories/{$imagePath}");
        }

        return asset($imagePath);
    }

    public function getDefaultImageForCategory(): ?string
    {
        $name = mb_strtolower($this->name ?? '');

        if (str_contains($name, 'دهان') || str_contains($name, 'سكيب') || str_contains($name, 'scib')) {
            return 'assets/site/img/categories/scib-paints.jpg';
        }
        if (str_contains($name, 'جبسون') || str_contains($name, 'جبس') || str_contains($name, 'أسقف') || str_contains($name, 'اسقف')) {
            return 'assets/site/img/categories/gypsum-board.jpg';
        }
        if (str_contains($name, 'فيوتك') || str_contains($name, 'كرانيش')) {
            return 'assets/site/img/categories/fyutec-cornices.jpg';
        }
        if (str_contains($name, 'خشب') || str_contains($name, 'ورق') || str_contains($name, 'حائط') || str_contains($name, 'بديل')) {
            return 'assets/site/img/categories/wall-panels.jpg';
        }
        if (str_contains($name, 'أدوات') || str_contains($name, 'ادوات') || str_contains($name, 'مستلزمات') || str_contains($name, 'بناء')) {
            return 'assets/site/img/categories/construction-tools.jpg';
        }
        if (str_contains($name, 'مواد') || str_contains($name, 'تشطيب') || str_contains($name, 'عزل')) {
            return 'assets/site/img/categories/finishing-materials.jpg';
        }
        if (str_contains($name, 'أبواب') || str_contains($name, 'ابواب') || str_contains($name, 'مصفح')) {
            return 'assets/site/img/categories/doors.jpg';
        }
        if (str_contains($name, 'ديكور')) {
            return 'assets/site/img/categories/decor.jpg';
        }

        return 'assets/site/img/categories/finishing-materials.jpg';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query;
    }

    public function scopeOrdered(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('id', 'asc');
    }
}
