{{-- resources/views/components/storefront/ui/category-card.blade.php --}}
@props(['category'])

@php
    $imageUrl = $category->image_url ?: null;
@endphp

<a href="{{ route('category.product', $category->id) }}" 
   class="group relative flex flex-col items-center text-center p-3 sm:p-3.5 rounded-2xl bg-white border border-surface-border hover:border-primary-500 hover:shadow-card-hover transition-all duration-300 w-32 sm:w-40 shrink-0 hover:-translate-y-1">
    
    <!-- Image Media Box with Cover & Hover Zoom -->
    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-gray-100 border border-cream group-hover:border-primary-400 relative mb-2.5 transition-colors">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" 
                 alt="{{ $category->name }}" 
                 loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gradient-to-br from-primary-800 to-primary-950 flex items-center justify-center text-white text-xl font-black">
                {{ mb_substr($category->name, 0, 1) }}
            </div>
        @endif
        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
    </div>

    <!-- Category Name -->
    <h4 class="font-bold text-xs sm:text-sm text-charcoal-heading group-hover:text-primary-800 transition-colors line-clamp-1 w-full px-1">
        {{ $category->name }}
    </h4>

    <!-- Product Count Badge -->
    @if(isset($category->products_count))
        <span class="text-[10px] text-accent-700 font-bold mt-1 bg-accent-50 px-2 py-0.5 rounded-full border border-accent-100">
            {{ $category->products_count }} صنف
        </span>
    @else
        <span class="text-[10px] text-charcoal-muted mt-1">
            تصفح القسم ←
        </span>
    @endif
</a>
