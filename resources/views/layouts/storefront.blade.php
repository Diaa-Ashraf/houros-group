{{-- resources/views/layouts/storefront.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات'))</title>
    <meta name="description" content="@yield('meta_description', 'مؤسسة حورس للتوريدات والتشطيبات. دهانات SCIB، مواد البناء، أدوات التشطيب والديكور في حدائق الأهرام.')">
    <meta name="keywords" content="@yield('meta_keywords', 'حورس للتوريدات والتشطيبات, دهانات SCIB, مواد التشطيب, جبسون بورد, كرانيش فيوتك, حدائق الأهرام')">
    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="ar_EG" />
    <meta property="og:site_name" content="مؤسسة حورس للتوريدات والتشطيبات" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="@yield('title', settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات'))" />
    <meta property="og:description" content="@yield('meta_description', 'كل احتياجات التشطيب والديكور في مكان واحد. حدائق الأهرام، بوابة حورس.')" />
    <meta property="og:image" content="@yield('og_image', asset('assets/site/img/horus-cover.svg'))" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('title', settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات'))" />
    <meta name="twitter:description" content="@yield('meta_description', 'كل احتياجات التشطيب والديكور في مكان واحد. حدائق الأهرام، بوابة حورس.')" />
    <meta name="twitter:image" content="@yield('og_image', asset('assets/site/img/horus-cover.svg'))" />

    @yield('meta')

    <!-- Structured data for Horus Group -->
    @php
        $acceptedPayments = collect([
            settings('enable_cod', '1') == '1' ? 'Cash on delivery' : null,
            settings('enable_instapay', '1') == '1' ? 'InstaPay' : null,
            settings('enable_vodafone_cash', '1') == '1' ? 'Vodafone Cash' : null,
        ])->filter()->implode(', ');
    @endphp
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "HomeAndConstructionBusiness",
        "name": "مؤسسة حورس للتوريدات والتشطيبات",
        "image": "{{ asset('assets/site/img/horus-cover.svg') }}",
        "telephone": @json(settings('store_phone', '01002527072')),
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "٢٢ع حدائق الأهرام، بوابة حورس",
            "addressLocality": "حدائق الأهرام",
            "addressRegion": "الجيزة",
            "addressCountry": "EG"
        },
        "priceRange": "EGP",
        "currenciesAccepted": "EGP",
        "paymentAccepted": @json($acceptedPayments),
        "url": "{{ url('/') }}"
    }
    </script>

    @php
        $siteFavicon = get_site_favicon();
    @endphp
    @if($siteFavicon)
        <link rel="icon" href="{{ $siteFavicon }}">
        <link rel="shortcut icon" href="{{ $siteFavicon }}">
        <link rel="apple-touch-icon" href="{{ $siteFavicon }}">
    @else
        <link rel="icon" href="{{ asset('assets/site/img/horus-mark.png') }}">
    @endif

    <!-- Google Arabic Fonts (Cairo, Alexandria, Readex Pro) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&family=Cairo:wght@400;600;700;800;900&family=Readex+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Swiper & Toastr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Vite Assets (Tailwind & Alpine) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --horus-ink: #073f40; --horus-green: #159b8b; --horus-mint: #82d9c7; --horus-gold: #e2bf52; }
        .horus-brand-mark { background: linear-gradient(145deg, #159b8b, #073f40); border: 1px solid #82d9c7; box-shadow: 0 5px 16px rgba(7,63,64,.22); font-family: Georgia, serif; }
        [x-cloak] {
            display: none !important;
        }

        /* Toastr customization for Horus brand */
        #toast-container > div {
            opacity: 1 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15) !important;
            border-radius: 12px !important;
            font-family: 'Cairo', sans-serif !important;
            text-align: right !important;
            direction: rtl !important;
        }
        .toast-success {
            background-color: #073f40 !important;
        }
        .toast-error { background-color: #7B2D2D !important; }

        /* Prevent unwanted iOS Safari automatic zoom on form input focus */
        @media screen and (max-width: 768px) {
            input, select, textarea {
                font-size: 16px !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="bg-canvas-base text-charcoal font-sans antialiased min-h-screen flex flex-col overflow-x-clip selection:bg-primary-700 selection:text-white">

    <!-- Top Information Bar -->
    <x-storefront.layout.top-bar />

    <!-- Main Navigation Header -->
    <x-storefront.layout.header />

    <!-- Mobile Slide-over Menu -->
    <x-storefront.layout.mobile-menu />

    <!-- Main Body Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Global Footer -->
    <x-storefront.layout.footer />

    <!-- Slide-over Cart Drawer (excluded on Cart & Checkout pages to avoid drawer popups over the page) -->
    @if(!request()->routeIs('cart') && !request()->routeIs('checkout*'))
        <x-storefront.layout.cart-drawer />
    @endif

    <!-- WhatsApp Floating Action Button -->
    <x-storefront.layout.whatsapp-float />

    <!-- Storefront confirmation dialog -->
    <div id="horusModal"
         class="fixed inset-0 z-[99999] bg-charcoal-heading/60 backdrop-blur-xs flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300"
         dir="rtl">
        <div id="horusModalCard"
             class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-2xl border border-cream transform scale-95 transition-all duration-300 space-y-4">

            <div id="horusModalIcon" class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-800 border border-primary-200 flex items-center justify-center text-xl mx-auto shadow-inner">
                🔔
            </div>

            <div class="space-y-1.5">
                <h3 id="horusModalTitle" class="text-base font-bold text-charcoal-heading">
                    تأكيد الإجراء
                </h3>
                <p id="horusModalDesc" class="text-xs text-charcoal-muted leading-relaxed">
                    هل أنت متأكد من المتابعة؟
                </p>
            </div>

            <div class="flex items-center gap-2.5 pt-2">
                <button type="button"
                        id="horusModalCancelBtn"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-charcoal text-xs font-bold transition-colors cursor-pointer">
                    إلغاء
                </button>
                <button type="button"
                        id="horusModalConfirmBtn"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-primary-800 hover:bg-primary-900 text-white text-xs font-bold transition-all shadow-sm cursor-pointer">
                    تأكيد
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        // Setup CSRF token for jQuery AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Toastr Defaults
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-left",
            "timeOut": "3500",
            "rtl": true
        };

        // Flash message toasts
        @if(session('success'))
            toastr.success(@json(session('success')));
        @endif
        @if(session('error'))
            toastr.error(@json(session('error')));
        @endif
        @if(session('status'))
            toastr.info(@json(session('status')));
        @endif

        // Global storefront modal helper
        window.horusConfirm = function(title, message, onConfirm, onCancel, options = {}) {
            const modal = document.getElementById('horusModal');
            const card = document.getElementById('horusModalCard');
            const titleEl = document.getElementById('horusModalTitle');
            const descEl = document.getElementById('horusModalDesc');
            const confirmBtn = document.getElementById('horusModalConfirmBtn');
            const cancelBtn = document.getElementById('horusModalCancelBtn');
            const iconEl = document.getElementById('horusModalIcon');

            if (!modal) return;

            titleEl.textContent = title || 'تأكيد';
            descEl.textContent = message || '';
            confirmBtn.textContent = options.confirmText || 'تأكيد';
            cancelBtn.textContent = options.cancelText || 'إلغاء';
            if (options.icon) iconEl.textContent = options.icon;

            const closeModal = () => {
                modal.classList.add('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
            };

            const newConfirm = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirm, confirmBtn);

            const newCancel = cancelBtn.cloneNode(true);
            cancelBtn.parentNode.replaceChild(newCancel, cancelBtn);

            newConfirm.addEventListener('click', () => {
                closeModal();
                if (typeof onConfirm === 'function') onConfirm();
            });

            newCancel.addEventListener('click', () => {
                closeModal();
                if (typeof onCancel === 'function') onCancel();
            });

            modal.onclick = (e) => {
                if (e.target === modal) {
                    closeModal();
                    if (typeof onCancel === 'function') onCancel();
                }
            };

            modal.classList.remove('opacity-0', 'pointer-events-none');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        };

        // Custom Event listener for cart count updates
        window.addEventListener('cart-updated', function(event) {
            const count = event.detail?.count ?? event.detail;
            const badge = document.getElementById('cart-badge-count');
            if (badge && count !== undefined) {
                badge.textContent = count;
            }
        });

        // Global Wishlist toggle handler
        window.toggleWishlist = function(productId, btnEl) {
            fetch('/wishlist/toggle/' + productId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (res.status === 401) {
                    toastr.info('يرجى تسجيل الدخول لحفظ المنتج في المفضلة');
                    return null;
                }
                return res.json();
            })
            .then(data => {
                if (!data) return;
                if (data.success) {
                    toastr.success(data.message || 'تم تحديث المفضلة بنجاح');
                    if (btnEl) {
                        const svg = btnEl.querySelector('svg');
                        if (data.in_wishlist) {
                            btnEl.classList.add('text-rose-600');
                            if (svg) svg.setAttribute('fill', 'currentColor');
                        } else {
                            btnEl.classList.remove('text-rose-600');
                            if (svg) svg.setAttribute('fill', 'none');
                        }
                    }
                } else {
                    toastr.error(data.message || 'تعذر تحديث المفضلة');
                }
            })
            .catch(() => {
                toastr.error('حدث خطأ أثناء تحديث المفضلة');
            });
        };

        // Global Add To Cart AJAX Helper (Supports: addToCart(id, btn) or addToCart(id, quantity, btn))
        window.addToCart = function(productId, quantityOrBtn = 1, btnEl = null) {
            let quantity = 1;
            if (quantityOrBtn instanceof HTMLElement || (typeof quantityOrBtn === 'object' && quantityOrBtn !== null && quantityOrBtn.nodeType)) {
                btnEl = quantityOrBtn;
                quantity = 1;
            } else {
                quantity = parseInt(quantityOrBtn) || 1;
            }

            let originalContent = '';
            if (btnEl) {
                originalContent = btnEl.innerHTML;
                btnEl.disabled = true;
                btnEl.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';
            }

            return fetch('/add-to-cart/' + productId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (btnEl) {
                    btnEl.disabled = false;
                    btnEl.innerHTML = originalContent;
                }
                if (data.success || data.status) {
                    toastr.success(data.message || 'تمت إضافة المنتج إلى السلة بنجاح');
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count ?? data.count } }));
                } else {
                    toastr.error(data.message || 'تعذر إضافة المنتج للسلة');
                }
                return data;
            })
            .catch(err => {
                if (btnEl) {
                    btnEl.disabled = false;
                    btnEl.innerHTML = originalContent;
                }
                toastr.error('حدث خطأ أثناء إضافة المنتج للسلة');
                throw err;
            });
        };
    </script>

    @stack('scripts')
</body>

</html>
