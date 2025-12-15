<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO OPTIMIZATION START -->
    <title>Bakmi Jowo & Mie Lethek Palur - Pertama di Surakarta | Tanpa Micin</title>
    <meta name="description" content="Nikmati Bakmi Jowo & Mie Lethek Palur pertama di Surakarta. Resep otentik, dimasak arang, 100% tanpa micin/MSG. Menu favorit: Mie Lethek Godog, Bakmi Jowo, & Nasi Goreng Mawut.">
    <meta name="keywords" content="bakmi jowo palur, mie lethek palur, kuliner solo, bakmi jowo enak solo, mie lethek enak, kuliner tanpa micin, bakmi jowo surakarta">
    <meta name="author" content="Bakmi Jowo Palur">
    <meta name="robots" content="index, follow">
    <meta name="geo.region" content="ID-JT" />
    <meta name="geo.placename" content="Surakarta" />
    <meta name="geo.position" content="-7.571578;110.852491" />
    <meta name="ICBM" content="-7.571578, 110.852491" />
    <link rel="canonical" href="https://bakmijowopalur.com" />

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="restaurant">
    <meta property="og:url" content="https://bakmijowopalur.com/">
    <meta property="og:title" content="Bakmi Jowo & Mie Lethek Palur - Otentik & Tanpa Micin">
    <meta property="og:description" content="Pelopor Mie Lethek Palur di Solo. Masak pakai anglo, bebas MSG, rasa juara. Buka setiap hari 16.00 - 23.00.">
    <meta property="og:image" content="https://images.unsplash.com/photo-1626804475297-411dbe64fc3b?q=80&w=1200&auto=format&fit=crop">

    <!-- JSON-LD Schema Markup for Local Business (CRITICAL FOR LOCAL SEO) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Restaurant",
      "name": "Bakmi Jowo & Mie Lethek Palur",
      "image": "https://images.unsplash.com/photo-1626804475297-411dbe64fc3b?q=80&w=800&auto=format&fit=crop",
      "priceRange": "Rp 15.000 - Rp 25.000",
      "servesCuisine": "Indonesian",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Jl. Raya Palur",
        "addressLocality": "Mojolaban",
        "addressRegion": "Jawa Tengah",
        "postalCode": "57554",
        "addressCountry": "ID"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": -7.571578,
        "longitude": 110.852491
      },
      "url": "https://bakmijowopalur.com",
      "telephone": "+6281234567890",
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday"
          ],
          "opens": "16:00",
          "closes": "23:00"
        }
      ],
      "menu": "https://bakmijowopalur.com/#menu",
      "description": "Warung Bakmi Jowo dan Mie Lethek otentik pertama di Palur Surakarta yang menyajikan masakan sehat tanpa micin."
    }
    </script>
    <!-- SEO OPTIMIZATION END -->
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Iconify -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #09090b;
        }
        ::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3f3f46;
        }

        /* Subtle Gradients and Utilities */
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .glass-nav {
            background: rgba(9, 9, 11, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Hide map UI elements for cleaner look */
        iframe {
            filter: grayscale(100%) invert(92%) contrast(85%);
        }

        /* Modal Animation */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }
        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 300ms ease-out, transform 300ms ease-out;
        }
        .modal-exit {
            opacity: 1;
            transform: scale(1);
        }
        .modal-exit-active {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 200ms ease-in, transform 200ms ease-in;
        }

        /* Tab Animation */
        .tab-active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: rgba(255, 255, 255, 0.2);
        }
        .tab-inactive {
            color: #a1a1aa; /* zinc-400 */
            border-color: transparent;
        }
        .tab-inactive:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .menu-item-hidden {
            display: none;
        }
        .menu-item-visible {
            display: block;
            animation: fadeIn 0.5s ease forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 selection:bg-amber-500/30 selection:text-amber-200">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <!-- Logo with H1 Context for SEO -->
            <a href="#" class="text-lg font-medium tracking-tighter text-white flex items-center gap-2" aria-label="Bakmi Jowo Palur Home">
                <span class="iconify text-amber-500" data-icon="lucide:utensils-crossed" data-width="20"></span>
                PALUR.
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-zinc-400">
                <a href="#about" class="hover:text-white transition-colors duration-300">Tentang</a>
                <a href="#menu" class="hover:text-white transition-colors duration-300">Menu</a>
                <a href="#location" class="hover:text-white transition-colors duration-300">Lokasi</a>
            </div>

            <!-- CTA & Mobile Toggle -->
            <div class="flex items-center gap-4">
                <a href="https://maps.app.goo.gl/hLQKBBJxnbiwNn3i9" target="_blank" class="hidden md:flex text-xs font-medium bg-white text-black px-4 py-2 rounded-full hover:bg-zinc-200 transition-colors">
                    Kunjungi Kami
                </a>
                
                <!-- Mobile Menu Button -->
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden text-zinc-400 hover:text-white" aria-label="Toggle Menu">
                    <span class="iconify" data-icon="lucide:menu" data-width="24"></span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden md:hidden bg-zinc-900 border-b border-white/5 absolute w-full left-0 top-16">
            <div class="px-6 py-4 flex flex-col gap-4 text-sm font-medium text-zinc-400">
                <a href="#about" class="hover:text-white transition-colors py-2 border-b border-white/5">Tentang</a>
                <a href="#menu" class="hover:text-white transition-colors py-2 border-b border-white/5">Menu</a>
                <a href="#location" class="hover:text-white transition-colors py-2 border-b border-white/5">Lokasi</a>
                <a href="https://maps.app.goo.gl/hLQKBBJxnbiwNn3i9" class="text-white py-2">Buka Peta</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 md:pt-48 md:pb-32 overflow-hidden">
        <!-- Background Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-amber-600/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-6xl mx-auto px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-green-500/20 bg-green-500/5 backdrop-blur-sm mb-6 animate-fade-in">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-xs font-medium text-zinc-300 tracking-wide uppercase">Sehat • Tanpa Micin • Otentik</span>
            </div>
            
            <!-- H1 Optimized for Keywords -->
            <h1 class="text-5xl md:text-7xl font-medium tracking-tight mb-6 text-white leading-[1.1]">
                Bakmi Jowo <br class="md:hidden">
                <span class="text-zinc-500">Mie Lethek Palur.</span>
            </h1>
            
            <p class="text-lg md:text-xl text-zinc-400 max-w-2xl mx-auto mb-10 font-light leading-relaxed px-4">
                Pelopor kuliner <strong>Mie Lethek asli Palur</strong> pertama di Surakarta. Menghadirkan cita rasa otentik yang legendaris, <span class="text-amber-500 font-medium">dijamin sehat tanpa MSG.</span>
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 px-6">
                <a href="#menu" class="w-full sm:w-auto px-8 py-3 bg-white text-zinc-950 font-medium text-sm rounded-full hover:bg-zinc-200 transition-all flex items-center justify-center gap-2">
                    Lihat Menu
                    <span class="iconify" data-icon="lucide:arrow-down" data-width="16"></span>
                </a>
                <a href="https://maps.app.goo.gl/hLQKBBJxnbiwNn3i9" target="_blank" class="w-full sm:w-auto px-8 py-3 glass-panel text-white font-medium text-sm rounded-full hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                    <span class="iconify" data-icon="lucide:map-pin" data-width="16"></span>
                    Petunjuk Arah
                </a>
            </div>
        </div>
    </header>

    <!-- Bento Grid / Features -->
    <section id="about" class="py-20 bg-zinc-950">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1: First in Solo -->
                <div class="md:col-span-2 glass-panel p-8 rounded-3xl relative overflow-hidden group min-h-[300px] flex flex-col justify-between">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    <div class="relative z-10">
                        <span class="iconify text-amber-500 mb-4" data-icon="lucide:medal" data-width="32"></span>
                        <h2 class="text-2xl font-medium text-white mb-2 tracking-tight">Pertama di Surakarta</h2>
                        <p class="text-zinc-400 text-sm leading-relaxed max-w-md">
                            Kami adalah pelopor yang membawa cita rasa asli <strong>Mie Lethek Palur</strong> ke wilayah Surakarta (Solo). Rasakan keaslian resep turun-temurun yang legendaris, kini lebih dekat dengan Anda.
                        </p>
                    </div>
                    <div class="mt-8 w-full h-48 rounded-xl bg-zinc-900 overflow-hidden border border-white/5 relative">
                        <img src="https://images.unsplash.com/photo-1552611052-33e04de081de?q=80&w=1600&auto=format&fit=crop" alt="Suasana Makan Bakmi Jowo Palur" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700">
                    </div>
                </div>

                <!-- Card 2: Ingredients -->
                <div class="glass-panel p-8 rounded-3xl relative overflow-hidden group min-h-[300px]">
                    <div class="relative z-10">
                        <span class="iconify text-amber-500 mb-4" data-icon="lucide:wheat" data-width="32"></span>
                        <h3 class="text-2xl font-medium text-white mb-2 tracking-tight">Mie Lethek Asli</h3>
                        <p class="text-zinc-400 text-sm leading-relaxed mb-6">
                            Terbuat dari tepung tapioka dan singkong, berwarna keruh alami tanpa pemutih, menghasilkan tekstur kenyal yang khas.
                        </p>
                    </div>
                    <div class="absolute bottom-0 right-0 w-32 h-32 bg-amber-500/10 blur-[50px]"></div>
                </div>

                <!-- Card 3: Tanpa Micin (Highlighted) -->
                <div class="glass-panel p-8 rounded-3xl relative overflow-hidden group md:col-span-1 min-h-[200px] border border-green-500/20 bg-green-900/10 hover:border-green-500/40 transition-colors">
                    <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                         <span class="iconify text-green-500" data-icon="lucide:leaf" data-width="80"></span>
                    </div>
                    <span class="iconify text-green-500 mb-4" data-icon="lucide:shield-check" data-width="32"></span>
                    <h3 class="text-2xl font-medium text-white mb-2 tracking-tight">100% Tanpa Micin</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">
                        Nikmati rasa gurih alami yang berasal dari kaldu ayam kampung murni. Dijamin sehat, tanpa MSG, dan aman dikonsumsi setiap hari oleh keluarga.
                    </p>
                </div>

                <!-- Card 4: Atmosphere -->
                <div class="md:col-span-2 glass-panel p-8 rounded-3xl relative overflow-hidden flex flex-col md:flex-row items-center gap-8">
                    <div class="flex-1">
                        <h3 class="text-2xl font-medium text-white mb-2 tracking-tight">Suasana Hangat</h3>
                        <p class="text-zinc-400 text-sm leading-relaxed">
                            Nikmati hidangan di tempat yang nyaman, cocok untuk makan malam bersama keluarga atau teman dengan nuansa Jawa yang kental.
                        </p>
                    </div>
                    <div class="flex-1 w-full">
                        <div class="grid grid-cols-2 gap-2">
                             <div class="h-24 rounded-lg bg-zinc-800 border border-white/5 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=800&auto=format&fit=crop" alt="Interior Warung Bakmi" class="w-full h-full object-cover opacity-70 hover:opacity-100 transition-opacity">
                             </div>
                             <div class="h-24 rounded-lg bg-zinc-800 border border-white/5 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1555126634-323283e090fa?q=80&w=800&auto=format&fit=crop" alt="Memasak Bakmi Jowo" class="w-full h-full object-cover opacity-70 hover:opacity-100 transition-opacity">
                             </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Menu Section (Enhanced UX) -->
    <section id="menu" class="py-24 relative bg-zinc-950">
        <div class="max-w-6xl mx-auto px-6">
            
            <!-- Menu Header & Filters -->
            <div class="flex flex-col items-center text-center mb-12 space-y-8">
                <div>
                    <h2 class="text-3xl md:text-4xl font-medium tracking-tight text-white mb-3">Daftar Menu</h2>
                    <p class="text-zinc-400 text-sm max-w-lg mx-auto">Kami menyajikan hidangan berkualitas dengan bahan pilihan. Klik menu untuk melakukan pemesanan.</p>
                </div>

                <!-- Interactive Filters -->
                <div class="flex flex-wrap justify-center gap-2 p-1 bg-white/5 rounded-full border border-white/5 backdrop-blur-sm">
                    <button onclick="filterMenu('makanan')" id="btn-makanan" class="px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-300 tab-active border border-transparent">
                        Makanan
                    </button>
                    <button onclick="filterMenu('minuman')" id="btn-minuman" class="px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-300 tab-inactive border border-transparent">
                        Minuman
                    </button>
                    <button onclick="filterMenu('tambahan')" id="btn-tambahan" class="px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-300 tab-inactive border border-transparent">
                        Tambahan
                    </button>
                </div>
            </div>

            <!-- Menu Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="menu-grid">
                
                <!-- ================= MAKANAN ================= -->
                
                <div class="menu-item menu-item-visible" data-category="makanan" onclick="openOrderModal('Mie Lethek Godog', '16K', 'https://images.unsplash.com/photo-1626804475297-411dbe64fc3b?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1626804475297-411dbe64fc3b?q=80&w=800&auto=format&fit=crop" alt="Mie Lethek Godog Palur" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                            <div class="absolute top-2 right-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded-lg text-xs font-medium text-amber-400 border border-amber-500/20">Signature</div>
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Mie Lethek Godog</h3>
                            <span class="text-sm font-medium text-zinc-400">16K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Mie singkong rebus dengan kuah kaldu kental gurih, dimasak arang.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-visible" data-category="makanan" onclick="openOrderModal('Mie Lethek Goreng', '16K', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?q=80&w=800&auto=format&fit=crop" alt="Mie Lethek Goreng Palur" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Mie Lethek Goreng</h3>
                            <span class="text-sm font-medium text-zinc-400">16K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Mie singkong goreng dengan bumbu rempah dan kecap manis pas.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-visible" data-category="makanan" onclick="openOrderModal('Bakmi Jowo Godog', '16K', 'https://images.unsplash.com/photo-1596450523090-e51c88746c87?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1596450523090-e51c88746c87?q=80&w=800&auto=format&fit=crop" alt="Bakmi Jowo Godog Solo" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                             <div class="absolute top-2 right-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded-lg text-xs font-medium text-amber-400 border border-amber-500/20">Best Seller</div>
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Bakmi Jowo Godog</h3>
                            <span class="text-sm font-medium text-zinc-400">16K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Mie kuning telur rebus kuah kaldu ayam kampung segar.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-visible" data-category="makanan" onclick="openOrderModal('Bakmi Jowo Goreng', '16K', 'https://images.unsplash.com/photo-1555126634-323283e090fa?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1555126634-323283e090fa?q=80&w=800&auto=format&fit=crop" alt="Bakmi Jowo Goreng" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Bakmi Jowo Goreng</h3>
                            <span class="text-sm font-medium text-zinc-400">16K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Mie kuning goreng nyemek dengan aroma sangit arang yang khas.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-visible" data-category="makanan" onclick="openOrderModal('Nasi Goreng Jowo', '16K', 'https://images.unsplash.com/photo-1603133872878-684f208fb74b?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1603133872878-684f208fb74b?q=80&w=800&auto=format&fit=crop" alt="Nasi Goreng Jowo" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Nasi Goreng Jowo</h3>
                            <span class="text-sm font-medium text-zinc-400">16K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Nasi goreng tradisional, dimasak tanpa saos tomat, warna coklat alami.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-visible" data-category="makanan" onclick="openOrderModal('Nasi Goreng Mawut', '16K', 'https://images.unsplash.com/photo-1512058564366-18510be2db19?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?q=80&w=800&auto=format&fit=crop" alt="Nasi Goreng Mawut / Magelangan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Nasi Goreng Mawut</h3>
                            <span class="text-sm font-medium text-zinc-400">16K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Campuran nasi goreng dan mie lethek, porsi mantap mengenyangkan.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-visible" data-category="makanan" onclick="openOrderModal('Nasi Godog', '16K', 'https://images.unsplash.com/photo-1543826173-70651703c5a4?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1543826173-70651703c5a4?q=80&w=800&auto=format&fit=crop" alt="Nasi Godog Jawa" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Nasi Godog</h3>
                            <span class="text-sm font-medium text-zinc-400">16K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Nasi berkuah kaldu dengan sayuran dan suwiran ayam, hangat di perut.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-visible" data-category="makanan" onclick="openOrderModal('Rica-rica Ayam', '25K', 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1565557623262-b51c2513a641?q=80&w=800&auto=format&fit=crop" alt="Rica-rica Ayam Kampung" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                            <div class="absolute top-2 right-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded-lg text-xs font-medium text-amber-400 border border-amber-500/20">Spesial</div>
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Rica-rica Ayam</h3>
                            <span class="text-sm font-medium text-zinc-400">25K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Potongan ayam kampung dengan bumbu pedas manis meresap.</p>
                    </div>
                </div>
                
                 <div class="menu-item menu-item-visible" data-category="makanan" onclick="openOrderModal('Nasi Putih', '4K', 'https://images.unsplash.com/photo-1536304993881-ffc02132e7f3?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1536304993881-ffc02132e7f3?q=80&w=800&auto=format&fit=crop" alt="Nasi Putih" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Nasi Putih</h3>
                            <span class="text-sm font-medium text-zinc-400">4K</span>
                        </div>
                         <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Nasi putih pulen hangat.</p>
                    </div>
                </div>

                <!-- ================= MINUMAN ================= -->

                <div class="menu-item menu-item-hidden" data-category="minuman" onclick="openOrderModal('Wedang Uwuh', '8K', 'https://images.unsplash.com/photo-1596710629179-4fdde5938896?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1596710629179-4fdde5938896?q=80&w=800&auto=format&fit=crop" alt="Wedang Uwuh" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Wedang Uwuh</h3>
                            <span class="text-sm font-medium text-zinc-400">8K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Minuman herbal khas Imogiri: Jahe, Secang, Cengkeh, Kayu Manis.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-hidden" data-category="minuman" onclick="openOrderModal('Wedang Uwuh Susu', '10K', 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1544787219-7f47ccb76574?q=80&w=800&auto=format&fit=crop" alt="Wedang Uwuh Susu" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Wedang Uwuh Susu</h3>
                            <span class="text-sm font-medium text-zinc-400">10K</span>
                        </div>
                         <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Kombinasi wedang rempah hangat dengan susu kental manis.</p>
                    </div>
                </div>
                
                <div class="menu-item menu-item-hidden" data-category="minuman" onclick="openOrderModal('Teh Manis (Panas/Es)', '4K', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=800&auto=format&fit=crop" alt="Teh Manis" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Teh Manis</h3>
                            <span class="text-sm font-medium text-zinc-400">4K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Teh seduh asli dengan gula batu (Panas/Dingin).</p>
                    </div>
                </div>
                
                 <div class="menu-item menu-item-hidden" data-category="minuman" onclick="openOrderModal('Jeruk (Panas/Es)', '5K', 'https://images.unsplash.com/photo-1613478223719-2ab802602423?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1613478223719-2ab802602423?q=80&w=800&auto=format&fit=crop" alt="Es Jeruk" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Jeruk (Panas/Es)</h3>
                            <span class="text-sm font-medium text-zinc-400">5K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Perasan jeruk segar asli, kaya vitamin C.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-hidden" data-category="minuman" onclick="openOrderModal('Kopi Hitam', '4K', 'https://images.unsplash.com/photo-1551033406-611cf9a28f67?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1551033406-611cf9a28f67?q=80&w=800&auto=format&fit=crop" alt="Kopi Hitam Tubruk" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Kopi Hitam</h3>
                            <span class="text-sm font-medium text-zinc-400">4K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Kopi tubruk tradisional yang pekat dan mantap.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-hidden" data-category="minuman" onclick="openOrderModal('Lemon Tea', '5K', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=800&auto=format&fit=crop" alt="Lemon Tea Segar" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Lemon Tea</h3>
                            <span class="text-sm font-medium text-zinc-400">5K</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Kesegaran teh dengan potongan lemon asli.</p>
                    </div>
                </div>

                <!-- ================= TAMBAHAN ================= -->

                <div class="menu-item menu-item-hidden" data-category="tambahan" onclick="openOrderModal('Kepala Ayam', '5K', 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=800&auto=format&fit=crop" alt="Bacem Kepala Ayam" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Kepala Ayam</h3>
                            <span class="text-sm font-medium text-zinc-400">5K</span>
                        </div>
                         <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Bacem kepala ayam yang gurih manis.</p>
                    </div>
                </div>

                <div class="menu-item menu-item-hidden" data-category="tambahan" onclick="openOrderModal('Sayap Ayam', '5K', 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=800&auto=format&fit=crop" alt="Bacem Sayap Ayam" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Sayap Ayam</h3>
                            <span class="text-sm font-medium text-zinc-400">5K</span>
                        </div>
                         <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Bacem sayap ayam yang empuk.</p>
                    </div>
                </div>
                
                 <div class="menu-item menu-item-hidden" data-category="tambahan" onclick="openOrderModal('Telor Ceplok/Dadar', '4K', 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?q=80&w=800&auto=format&fit=crop')">
                    <div class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1565557623262-b51c2513a641?q=80&w=800&auto=format&fit=crop" alt="Telur Dadar Goreng" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">Telor Ceplok/Dadar</h3>
                            <span class="text-sm font-medium text-zinc-400">4K</span>
                        </div>
                         <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">Tambahan lauk telur goreng sesuai selera.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Location & Footer -->
    <section id="location" class="bg-zinc-950 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Info -->
                <div>
                    <h2 class="text-3xl font-medium tracking-tight text-white mb-6">Lokasi & Jam Buka</h2>
                    <div class="space-y-6">
                        <div class="flex gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-zinc-900 flex items-center justify-center shrink-0 text-amber-500 border border-white/5 group-hover:border-amber-500/50 transition-colors">
                                <span class="iconify" data-icon="lucide:map-pin"></span>
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-1">Alamat</h4>
                                <p class="text-sm text-zinc-400 leading-relaxed">
                                    Jl. Raya Palur, Palur, Kec. Mojolaban, <br>Kabupaten Sukoharjo, Jawa Tengah
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-zinc-900 flex items-center justify-center shrink-0 text-amber-500 border border-white/5 group-hover:border-amber-500/50 transition-colors">
                                <span class="iconify" data-icon="lucide:clock"></span>
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-1">Jam Operasional</h4>
                                <p class="text-sm text-zinc-400">Setiap Hari: 16.00 - 23.00 WIB</p>
                            </div>
                        </div>

                        <div class="flex gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-zinc-900 flex items-center justify-center shrink-0 text-amber-500 border border-white/5 group-hover:border-amber-500/50 transition-colors">
                                <span class="iconify" data-icon="lucide:phone"></span>
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-1">Kontak</h4>
                                <p class="text-sm text-zinc-400">+62 812-3456-7890</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="h-[400px] w-full rounded-3xl overflow-hidden border border-white/10 relative group bg-zinc-900">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.031525997677!2d110.85249157499709!3d-7.5715783924424365!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a16fbd9d0c24b%3A0x6b484501a37c35e!2sPalur%20Plaza!5e0!3m2!1sen!2sid!4v1709623849123!5m2!1sen!2sid" title="Lokasi Bakmi Jowo Palur" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" class="opacity-60 group-hover:opacity-100 transition-opacity duration-500 invert-[.9] grayscale contrast-[.85]">
                    </iframe>
                    <div class="absolute inset-0 pointer-events-none border border-white/5 rounded-3xl z-10"></div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="mt-20 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs text-zinc-500">
                    © 2024 Bakmi Jowo & Mie Lethek Palur. All rights reserved.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="text-zinc-500 hover:text-white transition-colors" aria-label="Instagram">
                        <span class="iconify" data-icon="lucide:instagram" data-width="18"></span>
                    </a>
                    <a href="#" class="text-zinc-500 hover:text-white transition-colors" aria-label="Facebook">
                        <span class="iconify" data-icon="lucide:facebook" data-width="18"></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ORDER MODAL -->
    <div id="orderModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-sm transition-opacity" onclick="closeOrderModal()"></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                
                <!-- Close Button -->
                <button type="button" onclick="closeOrderModal()" class="absolute top-4 right-4 text-zinc-400 hover:text-white transition-colors z-10" aria-label="Close Modal">
                    <span class="iconify" data-icon="lucide:x" data-width="24"></span>
                </button>

                <!-- Content -->
                <div class="p-6">
                    <div class="flex flex-col items-center">
                        <div class="w-full h-48 rounded-xl bg-zinc-800 overflow-hidden mb-4 relative">
                            <img id="modalImage" src="" alt="Menu" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/60 to-transparent"></div>
                        </div>
                        
                        <h3 class="text-xl font-medium text-white mb-1" id="modalTitle">Menu Title</h3>
                        <p class="text-amber-500 font-bold text-lg mb-6" id="modalPrice">0K</p>
                        
                        <p class="text-sm text-zinc-400 mb-6 text-center">
                            Pilih metode pemesanan yang Anda inginkan:
                        </p>

                        <div class="w-full space-y-3">
                            <!-- WhatsApp -->
                            <a id="waLink" href="#" target="_blank" class="flex items-center justify-center gap-3 w-full py-3 bg-[#25D366] hover:bg-[#1fb857] text-white rounded-xl font-medium transition-colors">
                                <span class="iconify" data-icon="lucide:message-circle" data-width="20"></span>
                                Pesan via WhatsApp
                            </a>
                            
                            <!-- GrabFood (Placeholder) -->
                            <a href="#" class="flex items-center justify-center gap-3 w-full py-3 bg-[#00B14F] hover:bg-[#009e46] text-white rounded-xl font-medium transition-colors opacity-80 hover:opacity-100">
                                <span class="iconify" data-icon="simple-icons:grab" data-width="20"></span>
                                Order di GrabFood
                            </a>
                            
                            <!-- ShopeeFood (Placeholder) -->
                            <a href="#" class="flex items-center justify-center gap-3 w-full py-3 bg-[#EE4D2D] hover:bg-[#d63f21] text-white rounded-xl font-medium transition-colors opacity-80 hover:opacity-100">
                                <span class="iconify" data-icon="simple-icons:shopee" data-width="20"></span>
                                Order di ShopeeFood
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal Logic
        const modal = document.getElementById('orderModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalPrice = document.getElementById('modalPrice');
        const modalImage = document.getElementById('modalImage');
        const waLink = document.getElementById('waLink');

        function openOrderModal(title, price, imageUrl) {
            modalTitle.innerText = title;
            modalPrice.innerText = price;
            modalImage.src = imageUrl;
            
            // Create WhatsApp Link with pre-filled message
            const message = `Halo Admin Bakmi Jowo Palur, saya mau pesan ${title}. Apakah tersedia?`;
            waLink.href = `https://wa.me/6281234567890?text=${encodeURIComponent(message)}`;
            
            modal.classList.remove('hidden');
            
            // Simple entry animation logic
            const panel = modal.querySelector('.relative');
            panel.classList.add('modal-enter');
            requestAnimationFrame(() => {
                panel.classList.remove('modal-enter');
                panel.classList.add('modal-enter-active');
            });
        }

        function closeOrderModal() {
             const panel = modal.querySelector('.relative');
             panel.classList.remove('modal-enter-active');
             panel.classList.add('modal-exit-active');
             
             setTimeout(() => {
                 modal.classList.add('hidden');
                 panel.classList.remove('modal-exit-active');
             }, 200);
        }

        // Close on Esc key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape" && !modal.classList.contains('hidden')) {
                closeOrderModal();
            }
        });

        // Filter Menu Logic
        function filterMenu(category) {
            // Get all buttons
            const buttons = document.querySelectorAll('[id^="btn-"]');
            const items = document.querySelectorAll('.menu-item');

            // Reset buttons style
            buttons.forEach(btn => {
                btn.classList.remove('tab-active', 'border-white/20');
                btn.classList.add('tab-inactive', 'border-transparent');
            });

            // Set active button style
            const activeBtn = document.getElementById('btn-' + category);
            activeBtn.classList.remove('tab-inactive', 'border-transparent');
            activeBtn.classList.add('tab-active', 'border-white/20');

            // Show/Hide items
            items.forEach(item => {
                // Reset animation
                item.classList.remove('menu-item-visible');
                void item.offsetWidth; // trigger reflow
                
                if (category === 'all' || item.dataset.category === category) {
                    item.classList.remove('menu-item-hidden');
                    item.classList.add('menu-item-visible');
                } else {
                    item.classList.add('menu-item-hidden');
                }
            });
        }
    </script>

</body>
</html>