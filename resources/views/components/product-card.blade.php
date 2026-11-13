@props(['product'])

@php
 $displayPrice = format_currency($product->final_price, 'USD');
 $productUrl = route('product.details', $product->slug ?? $product->id);
@endphp

<div class="group relative flex flex-col justify-between h-full bg-white hover:bg-[#FAF8F5] border border-transparent hover:border-[#EADBCC] rounded-2xl p-2.5 sm:p-3 text-center transition-all duration-500 ease-out hover:-translate-y-1.5 hover:shadow-xl select-none" data-product-id="{{ $product->id }}">
 
 {{-- Product Image Container: Marketchino 1:1 ratio with gentle smooth zoom on hover --}}
 <div class="relative w-full aspect-square bg-[#F8F9FA] group-hover:bg-white rounded-2xl overflow-hidden mb-2.5 flex items-center justify-center p-1.5 border border-transparent group-hover:border-[#EADBCC]/60 transition-all duration-500">
 <a href="{{ $productUrl }}" class="block w-full h-full flex items-center justify-center overflow-hidden rounded-xl">
 <img src="{{ $product->image_url ?? asset('assets/site/img/product/product-1.jpg') }}" 
 alt="{{ $product->name }}" 
 loading="lazy" 
 class="w-full h-full object-cover rounded-xl transition-transform duration-700 ease-out group-hover:scale-110">
 </a>

 {{-- Floating Badges (Start Edge) --}}
 <div class="absolute top-2.5 start-2.5 z-10 flex flex-col gap-1 items-start pointer-events-none">
 @if($product->has_discount)
 <span class="bg-rose-600 text-white font-bold text-[10px] sm:text-xs px-2 py-0.5 rounded-full shadow-sm animate-pulse">
 -{{ $product->discount_percentage }}%
 </span>
 @endif
 @if($product->featured)
 <span class="bg-[#C5A059] text-white font-extrabold text-[9px] sm:text-[10px] px-2 py-0.5 rounded-full shadow-sm">
 {{ __('مميز') }}
 </span>
 @endif
 </div>

 {{-- Floating Wishlist Button (End Edge) --}}
 @if(settings('wishlist_enabled', true))
 @php
 $isWishlisted = auth()->check() && auth()->user()->wishlistItems()->where('product_id', $product->id)->exists();
 @endphp
 <button type="button" 
 onclick="event.stopPropagation(); event.preventDefault(); window.toggleWishlist(event, {{ $product->id }}, this);"
 class="btn-wishlist-toggle absolute top-2.5 end-2.5 z-10 w-8 h-8 rounded-full bg-white/90 border border-gray-100 hover:border-[#C5A059] {{ $isWishlisted ? 'text-rose-600' : 'text-gray-400' }} hover:text-red-500 flex items-center justify-center transition-all duration-300 shadow-sm backdrop-blur-sm hover:scale-110 active:scale-95" 
 data-id="{{ $product->id }}" 
 title="{{ $isWishlisted ? __('إزالة من المفضلة') : __('إضافة للمفضلة') }}">
 <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
 <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
 </svg>
 </button>
 @endif
 </div>

 {{-- Details Block (Minimalist & Centered) --}}
 <div class="flex flex-col flex-grow items-center justify-between px-1">
 {{-- Product Title (Exact Marketchino Typography) --}}
 <a href="{{ $productUrl }}" class="hover:text-[#C5A059] transition-colors w-full mb-1">
 <h3 class="font-sans font-medium text-xs sm:text-sm text-[#18181B] line-clamp-1 leading-normal" title="{{ $product->name }}">
 {{ $product->name }}
 </h3>
 </a>

 {{-- Price Block (Direct centered price display) --}}
 <div class="my-auto py-1">
 <div class="flex items-center justify-center gap-1.5 flex-wrap">
 <span class="font-bold text-xs sm:text-sm text-[#18181B] font-sans tabular-nums">
 {{ $displayPrice }}
 </span>
 @if($product->has_discount)
 <span class="text-[10px] text-gray-400 line-through tabular-nums">
 {{ format_currency($product->price, 'USD') }}
 </span>
 @endif
 </div>
 </div>
 </div>

 @php
     $storeWhatsApp = preg_replace('/[^0-9]/', '', settings('store_whatsapp', '201002527072'));
 @endphp
 <div class="mt-2 w-full flex items-center gap-1.5">
     <button type="button" 
             onclick="event.stopPropagation(); event.preventDefault(); window.addToCart({{ $product->id }}, this);"
             class="js-add-to-cart flex-1 py-2 px-3 rounded-full bg-[#C8A635] hover:bg-[#B38F24] active:scale-95 text-[#073F40] text-xs font-black transition-colors duration-200 shadow-2xs border border-[#B38F24] flex items-center justify-center gap-1.5 cursor-pointer" 
             data-id="{{ $product->id }}">
         <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
         </svg>
         <span>{{ __('أضف للسلة') }}</span>
     </button>

     <a href="https://wa.me/{{ $storeWhatsApp }}?text={{ urlencode('مرحباً مؤسسة حورس، أود الاستفسار عن المنتج: ' . $product->name . ' بسعر ' . $displayPrice) }}"
        target="_blank"
        rel="noopener noreferrer"
        onclick="event.stopPropagation();"
        class="w-8 h-8 rounded-full bg-[#25D366] hover:bg-[#20bd5a] text-white flex items-center justify-center shadow-2xs transition-transform active:scale-90 shrink-0"
        title="تواصل فوراً عبر واتساب">
         <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
             <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
         </svg>
     </a>
 </div>
</div>
