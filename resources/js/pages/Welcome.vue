<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    UtensilsCrossed,
    Menu,
    ArrowDown,
    MapPin,
    Medal,
    Wheat,
    ShieldCheck,
    Leaf,
    Clock,
    Phone,
    Instagram,
    Facebook,
    X,
    MessageCircle,
    ShoppingBag
} from 'lucide-vue-next';

// --- State ---
const isMobileMenuOpen = ref(false);
const activeCategory = ref('makanan');
const isModalOpen = ref(false);
const selectedItem = ref({
    title: '',
    price: '',
    image: '',
    description: ''
});

// --- Data ---
const menuItems = [
    {
        category: 'makanan',
        title: 'Mie Lethek Godog',
        price: '16K',
        image: 'https://images.unsplash.com/photo-1626804475297-411dbe64fc3b?q=80&w=800&auto=format&fit=crop',
        description: 'Mie singkong rebus dengan kuah kaldu kental gurih, dimasak arang.',
        badge: 'Signature',
        badgeColor: 'text-amber-400 border-amber-500/20'
    },
    {
        category: 'makanan',
        title: 'Mie Lethek Goreng',
        price: '16K',
        image: 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?q=80&w=800&auto=format&fit=crop',
        description: 'Mie singkong goreng dengan bumbu rempah dan kecap manis pas.'
    },
    {
        category: 'makanan',
        title: 'Bakmi Jowo Godog',
        price: '16K',
        image: 'https://images.unsplash.com/photo-1596450523090-e51c88746c87?q=80&w=800&auto=format&fit=crop',
        description: 'Mie kuning telur rebus kuah kaldu ayam kampung segar.',
        badge: 'Best Seller',
        badgeColor: 'text-amber-400 border-amber-500/20'
    },
    {
        category: 'makanan',
        title: 'Bakmi Jowo Goreng',
        price: '16K',
        image: 'https://images.unsplash.com/photo-1555126634-323283e090fa?q=80&w=800&auto=format&fit=crop',
        description: 'Mie kuning goreng nyemek dengan aroma sangit arang yang khas.'
    },
    {
        category: 'makanan',
        title: 'Nasi Goreng Jowo',
        price: '16K',
        image: 'https://images.unsplash.com/photo-1603133872878-684f208fb74b?q=80&w=800&auto=format&fit=crop',
        description: 'Nasi goreng tradisional, dimasak tanpa saos tomat, warna coklat alami.'
    },
    {
        category: 'makanan',
        title: 'Nasi Goreng Mawut',
        price: '16K',
        image: 'https://images.unsplash.com/photo-1512058564366-18510be2db19?q=80&w=800&auto=format&fit=crop',
        description: 'Campuran nasi goreng dan mie lethek, porsi mantap mengenyangkan.'
    },
    {
        category: 'makanan',
        title: 'Nasi Godog',
        price: '16K',
        image: 'https://images.unsplash.com/photo-1543826173-70651703c5a4?q=80&w=800&auto=format&fit=crop',
        description: 'Nasi berkuah kaldu dengan sayuran dan suwiran ayam, hangat di perut.'
    },
    {
        category: 'makanan',
        title: 'Rica-rica Ayam',
        price: '25K',
        image: 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?q=80&w=800&auto=format&fit=crop',
        description: 'Potongan ayam kampung dengan bumbu pedas manis meresap.',
        badge: 'Spesial',
        badgeColor: 'text-amber-400 border-amber-500/20'
    },
    {
        category: 'makanan',
        title: 'Nasi Putih',
        price: '4K',
        image: 'https://images.unsplash.com/photo-1536304993881-ffc02132e7f3?q=80&w=800&auto=format&fit=crop',
        description: 'Nasi putih pulen hangat.'
    },
    {
        category: 'minuman',
        title: 'Wedang Uwuh',
        price: '8K',
        image: 'https://images.unsplash.com/photo-1596710629179-4fdde5938896?q=80&w=800&auto=format&fit=crop',
        description: 'Minuman herbal khas Imogiri: Jahe, Secang, Cengkeh, Kayu Manis.'
    },
    {
        category: 'minuman',
        title: 'Wedang Uwuh Susu',
        price: '10K',
        image: 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?q=80&w=800&auto=format&fit=crop',
        description: 'Kombinasi wedang rempah hangat dengan susu kental manis.'
    },
    {
        category: 'minuman',
        title: 'Teh Manis (Panas/Es)',
        price: '4K',
        image: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=800&auto=format&fit=crop',
        description: 'Teh seduh asli dengan gula batu (Panas/Dingin).'
    },
    {
        category: 'minuman',
        title: 'Jeruk (Panas/Es)',
        price: '5K',
        image: 'https://images.unsplash.com/photo-1613478223719-2ab802602423?q=80&w=800&auto=format&fit=crop',
        description: 'Perasan jeruk segar asli, kaya vitamin C.'
    },
    {
        category: 'minuman',
        title: 'Kopi Hitam',
        price: '4K',
        image: 'https://images.unsplash.com/photo-1551033406-611cf9a28f67?q=80&w=800&auto=format&fit=crop',
        description: 'Kopi tubruk tradisional yang pekat dan mantap.'
    },
    {
        category: 'minuman',
        title: 'Lemon Tea',
        price: '5K',
        image: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=800&auto=format&fit=crop',
        description: 'Kesegaran teh dengan potongan lemon asli.'
    },
    {
        category: 'tambahan',
        title: 'Kepala Ayam',
        price: '5K',
        image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=800&auto=format&fit=crop',
        description: 'Bacem kepala ayam yang gurih manis.'
    },
    {
        category: 'tambahan',
        title: 'Sayap Ayam',
        price: '5K',
        image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=800&auto=format&fit=crop',
        description: 'Bacem sayap ayam yang empuk.'
    },
    {
        category: 'tambahan',
        title: 'Telor Ceplok/Dadar',
        price: '4K',
        image: 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?q=80&w=800&auto=format&fit=crop',
        description: 'Tambahan lauk telur goreng sesuai selera.'
    }
];

// --- Computed ---
const filteredItems = computed(() => {
    if (activeCategory.value === 'all') return menuItems;
    return menuItems.filter(item => item.category === activeCategory.value);
});

// --- Methods ---
const setCategory = (category: string) => {
    activeCategory.value = category;
};

const openModal = (item: any) => {
    selectedItem.value = { ...item };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const getWhatsAppUrl = (title: string) => {
    const message = `Halo Admin Mie Lethek Palur, saya mau pesan ${title}. Apakah tersedia?`;
    return `https://wa.me/6289652431182?text=${encodeURIComponent(message)}`;
};

const scrollTo = (id: string) => {
    isMobileMenuOpen.value = false; // Close mobile menu if open
    const element = document.getElementById(id);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};
</script>

<template>

    <Head title="Bakmi Jowo & Mie Lethek Palur - Pertama di Surakarta">
        <meta name="description"
            content="Nikmati Bakmi Jowo & Mie Lethek Palur pertama di Surakarta. Resep otentik, dimasak arang, 100% tanpa micin/MSG." />
    </Head>

    <div class="min-h-screen bg-zinc-950 text-zinc-200 selection:bg-amber-500/30 selection:text-amber-200 font-sans">

        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 glass-nav transition-all duration-300">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <!-- Logo -->
                <a href="#" class="text-lg font-medium tracking-tighter text-white flex items-center gap-2"
                    aria-label="Mie Lethek Palur Home">
                    <UtensilsCrossed class="w-5 h-5 text-amber-500" />
                    PALUR.
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-zinc-400">
                    <button @click="scrollTo('about')"
                        class="hover:text-white transition-colors duration-300">Tentang</button>
                    <button @click="scrollTo('menu')"
                        class="hover:text-white transition-colors duration-300">Menu</button>
                    <button @click="scrollTo('location')"
                        class="hover:text-white transition-colors duration-300">Lokasi</button>
                </div>

                <!-- CTA & Mobile Toggle -->
                <div class="flex items-center gap-4">
                    <a href="https://maps.app.goo.gl/hLQKBBJxnbiwNn3i9" target="_blank"
                        class="hidden md:flex text-xs font-medium bg-white text-black px-4 py-2 rounded-full hover:bg-zinc-200 transition-colors">
                        Kunjungi Kami
                    </a>

                    <!-- Mobile Menu Button -->
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="md:hidden text-zinc-400 hover:text-white" aria-label="Toggle Menu">
                        <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Dropdown -->
            <transition enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform -translate-y-2 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="transform translate-y-0 opacity-100"
                leave-to-class="transform -translate-y-2 opacity-0">
                <div v-if="isMobileMenuOpen"
                    class="md:hidden bg-zinc-900 border-b border-white/5 absolute w-full left-0 top-16 shadow-xl">
                    <div class="px-6 py-4 flex flex-col gap-4 text-sm font-medium text-zinc-400">
                        <button @click="scrollTo('about')"
                            class="text-left hover:text-white transition-colors py-2 border-b border-white/5">Tentang</button>
                        <button @click="scrollTo('menu')"
                            class="text-left hover:text-white transition-colors py-2 border-b border-white/5">Menu</button>
                        <button @click="scrollTo('location')"
                            class="text-left hover:text-white transition-colors py-2 border-b border-white/5">Lokasi</button>
                        <a href="https://maps.app.goo.gl/hLQKBBJxnbiwNn3i9" target="_blank"
                            class="text-white py-2 block">Buka Peta</a>
                    </div>
                </div>
            </transition>
        </nav>

        <!-- Hero Section -->
        <header class="relative pt-32 pb-20 md:pt-48 md:pb-32 overflow-hidden">
            <!-- Background Glow -->
            <div
                class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-amber-600/10 rounded-full blur-[100px] pointer-events-none">
            </div>

            <div class="max-w-6xl mx-auto px-6 relative z-10 text-center">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-green-500/20 bg-green-500/5 backdrop-blur-sm mb-6 animate-fade-in">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-zinc-300 tracking-wide uppercase">Sehat • Tanpa Micin •
                        Otentik</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-medium tracking-tight mb-6 text-white leading-[1.1]">
                    Bakmi Jowo <br class="md:hidden">
                    <span class="text-zinc-500">Mie Lethek Palur.</span>
                </h1>

                <p class="text-lg md:text-xl text-zinc-400 max-w-2xl mx-auto mb-10 font-light leading-relaxed px-4">
                    Pelopor kuliner <strong>Mie Lethek asli Palur</strong> pertama di Surakarta. Menghadirkan cita rasa
                    otentik yang legendaris, <span class="text-amber-500 font-medium">dijamin sehat tanpa MSG.</span>
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 px-6">
                    <button @click="scrollTo('menu')"
                        class="w-full sm:w-auto px-8 py-3 bg-white text-zinc-950 font-medium text-sm rounded-full hover:bg-zinc-200 transition-all flex items-center justify-center gap-2">
                        Lihat Menu
                        <ArrowDown class="w-4 h-4" />
                    </button>
                    <a href="https://maps.app.goo.gl/hLQKBBJxnbiwNn3i9" target="_blank"
                        class="w-full sm:w-auto px-8 py-3 glass-panel text-white font-medium text-sm rounded-full hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                        <MapPin class="w-4 h-4" />
                        Petunjuk Arah
                    </a>
                </div>
            </div>
        </header>

        <!-- Features (Bento Grid) -->
        <section id="about" class="py-20 bg-zinc-950">
            <div class="max-w-6xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Card 1: First in Solo -->
                    <div
                        class="md:col-span-2 glass-panel p-8 rounded-3xl relative overflow-hidden group min-h-[300px] flex flex-col justify-between">
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-amber-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                        </div>
                        <div class="relative z-10">
                            <Medal class="text-amber-500 mb-4 w-8 h-8" />
                            <h2 class="text-2xl font-medium text-white mb-2 tracking-tight">Pertama di Surakarta</h2>
                            <p class="text-zinc-400 text-sm leading-relaxed max-w-md">
                                Kami adalah pelopor yang membawa cita rasa asli <strong>Mie Lethek Palur</strong> ke
                                wilayah Surakarta (Solo). Rasakan keaslian resep turun-temurun yang legendaris, kini
                                lebih dekat dengan Anda.
                            </p>
                        </div>
                        <div
                            class="mt-8 w-full h-48 rounded-xl bg-zinc-900 overflow-hidden border border-white/5 relative">
                            <img src="https://images.unsplash.com/photo-1552611052-33e04de081de?q=80&w=1600&auto=format&fit=crop"
                                alt="Suasana Makan Mie Lethek Palur"
                                class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700">
                        </div>
                    </div>

                    <!-- Card 2: Ingredients -->
                    <div class="glass-panel p-8 rounded-3xl relative overflow-hidden group min-h-[300px]">
                        <div class="relative z-10">
                            <Wheat class="text-amber-500 mb-4 w-8 h-8" />
                            <h3 class="text-2xl font-medium text-white mb-2 tracking-tight">Mie Lethek Asli</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed mb-6">
                                Terbuat dari tepung tapioka dan singkong, berwarna keruh alami tanpa pemutih,
                                menghasilkan tekstur kenyal yang khas.
                            </p>
                        </div>
                        <div class="absolute bottom-0 right-0 w-32 h-32 bg-amber-500/10 blur-[50px]"></div>
                    </div>

                    <!-- Card 3: Tanpa Micin -->
                    <div
                        class="glass-panel p-8 rounded-3xl relative overflow-hidden group md:col-span-1 min-h-[200px] border border-green-500/20 bg-green-900/10 hover:border-green-500/40 transition-colors">
                        <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                            <Leaf class="text-green-500 w-20 h-20" />
                        </div>
                        <ShieldCheck class="text-green-500 mb-4 w-8 h-8" />
                        <h3 class="text-2xl font-medium text-white mb-2 tracking-tight">100% Tanpa Micin</h3>
                        <p class="text-zinc-400 text-sm leading-relaxed">
                            Nikmati rasa gurih alami yang berasal dari kaldu ayam kampung murni. Dijamin sehat, tanpa
                            MSG, dan aman dikonsumsi setiap hari oleh keluarga.
                        </p>
                    </div>

                    <!-- Card 4: Atmosphere -->
                    <div
                        class="md:col-span-2 glass-panel p-8 rounded-3xl relative overflow-hidden flex flex-col md:flex-row items-center gap-8">
                        <div class="flex-1">
                            <h3 class="text-2xl font-medium text-white mb-2 tracking-tight">Suasana Hangat</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">
                                Nikmati hidangan di tempat yang nyaman, cocok untuk makan malam bersama keluarga atau
                                teman dengan nuansa Jawa yang kental.
                            </p>
                        </div>
                        <div class="flex-1 w-full">
                            <div class="grid grid-cols-2 gap-2">
                                <div class="h-24 rounded-lg bg-zinc-800 border border-white/5 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=800&auto=format&fit=crop"
                                        alt="Interior Warung Bakmi"
                                        class="w-full h-full object-cover opacity-70 hover:opacity-100 transition-opacity">
                                </div>
                                <div class="h-24 rounded-lg bg-zinc-800 border border-white/5 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1555126634-323283e090fa?q=80&w=800&auto=format&fit=crop"
                                        alt="Memasak Bakmi Jowo"
                                        class="w-full h-full object-cover opacity-70 hover:opacity-100 transition-opacity">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Menu Section -->
        <section id="menu" class="py-24 relative bg-zinc-950">
            <div class="max-w-6xl mx-auto px-6">

                <!-- Menu Header & Filters -->
                <div class="flex flex-col items-center text-center mb-12 space-y-8">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-medium tracking-tight text-white mb-3">Daftar Menu</h2>
                        <p class="text-zinc-400 text-sm max-w-lg mx-auto">Kami menyajikan hidangan berkualitas dengan
                            bahan pilihan. Klik menu untuk melakukan pemesanan.</p>
                    </div>

                    <!-- Filters -->
                    <div
                        class="flex flex-wrap justify-center gap-2 p-1 bg-white/5 rounded-full border border-white/5 backdrop-blur-sm">
                        <button @click="setCategory('makanan')" :class="[
                            'px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-300 border',
                            activeCategory === 'makanan'
                                ? 'bg-white/10 text-white border-white/20'
                                : 'text-zinc-400 border-transparent hover:text-white hover:bg-white/5'
                        ]">
                            Makanan
                        </button>
                        <button @click="setCategory('minuman')" :class="[
                            'px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-300 border',
                            activeCategory === 'minuman'
                                ? 'bg-white/10 text-white border-white/20'
                                : 'text-zinc-400 border-transparent hover:text-white hover:bg-white/5'
                        ]">
                            Minuman
                        </button>
                        <button @click="setCategory('tambahan')" :class="[
                            'px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-300 border',
                            activeCategory === 'tambahan'
                                ? 'bg-white/10 text-white border-white/20'
                                : 'text-zinc-400 border-transparent hover:text-white hover:bg-white/5'
                        ]">
                            Tambahan
                        </button>
                    </div>
                </div>

                <!-- Menu Grid -->
                <!-- Use transition-group for filtering animation -->
                <TransitionGroup name="menu-list" tag="div"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="item in filteredItems" :key="item.title" @click="openModal(item)"
                        class="group cursor-pointer h-full rounded-2xl bg-zinc-900/50 border border-white/5 p-4 hover:border-amber-500/30 transition-all duration-300 hover:bg-zinc-900 hover:-translate-y-1">
                        <div class="aspect-video w-full rounded-xl bg-zinc-800 mb-4 overflow-hidden relative">
                            <img :src="item.image" :alt="item.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                            <div v-if="item.badge"
                                class="absolute top-2 right-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded-lg text-xs font-medium border"
                                :class="item.badgeColor">
                                {{ item.badge }}
                            </div>
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-white group-hover:text-amber-400 transition-colors">{{
                                item.title }}</h3>
                            <span class="text-sm font-medium text-zinc-400">{{ item.price }}</span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-2">{{ item.description }}</p>
                    </div>
                </TransitionGroup>

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
                                <div
                                    class="w-10 h-10 rounded-full bg-zinc-900 flex items-center justify-center shrink-0 text-amber-500 border border-white/5 group-hover:border-amber-500/50 transition-colors">
                                    <MapPin class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="text-white font-medium mb-1">Alamat</h4>
                                    <p class="text-sm text-zinc-400 leading-relaxed">
                                        JL. Raya Solo-tawangmangu no 48<br>Kabupaten Karanganyar, Jawa Tengah
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-zinc-900 flex items-center justify-center shrink-0 text-amber-500 border border-white/5 group-hover:border-amber-500/50 transition-colors">
                                    <Clock class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="text-white font-medium mb-1">Jam Operasional</h4>
                                    <p class="text-sm text-zinc-400">Setiap Hari: 15.00 - 22.00 WIB</p>
                                </div>
                            </div>

                            <div class="flex gap-4 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-zinc-900 flex items-center justify-center shrink-0 text-amber-500 border border-white/5 group-hover:border-amber-500/50 transition-colors">
                                    <Phone class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="text-white font-medium mb-1">Kontak</h4>
                                    <p class="text-sm text-zinc-400">+62 896-5243-1182</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div
                        class="h-[400px] w-full rounded-3xl overflow-hidden border border-white/10 relative group bg-zinc-900">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.031525997677!2d110.85249157499709!3d-7.5715783924424365!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a16fbd9d0c24b%3A0x6b484501a37c35e!2sPalur%20Plaza!5e0!3m2!1sen!2sid!4v1709623849123!5m2!1sen!2sid"
                            title="Lokasi Mie Lethek Palur" width="100%" height="100%" style="border:0;"
                            allowfullscreen="true" loading="lazy"
                            class="opacity-60 group-hover:opacity-100 transition-opacity duration-500 invert-[.9] grayscale contrast-[.85]"></iframe>
                        <div class="absolute inset-0 pointer-events-none border border-white/5 rounded-3xl z-10"></div>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div
                    class="mt-20 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-zinc-500">
                        © 2024 Mie Lethek Palur. All rights reserved.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="text-zinc-500 hover:text-white transition-colors" aria-label="Instagram">
                            <Instagram class="w-5 h-5" />
                        </a>
                        <a href="#" class="text-zinc-500 hover:text-white transition-colors" aria-label="Facebook">
                            <Facebook class="w-5 h-5" />
                        </a>
                        <a href="https://www.tiktok.com/@bakmimielethekpalur?is_from_webapp=1&sender_device=pc"
                            target="_blank" class="text-zinc-500 hover:text-white transition-colors"
                            aria-label="TikTok">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Order Modal -->
        <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="isModalOpen" class="fixed inset-0 z-[100] z-50" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-sm transition-opacity" @click="closeModal">
                </div>

                <!-- Modal Panel -->
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <Transition enter-active-class="ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="ease-in duration-200"
                        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <div v-if="isModalOpen"
                            class="relative transform overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">

                            <!-- Close Button -->
                            <button type="button" @click="closeModal"
                                class="absolute top-4 right-4 text-zinc-400 hover:text-white transition-colors z-10 p-1 bg-black/20 rounded-full"
                                aria-label="Close Modal">
                                <X class="w-5 h-5" />
                            </button>

                            <!-- Content -->
                            <div class="p-6">
                                <div class="flex flex-col items-center">
                                    <div class="w-full h-48 rounded-xl bg-zinc-800 overflow-hidden mb-4 relative">
                                        <img :src="selectedItem.image" :alt="selectedItem.title"
                                            class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/60 to-transparent">
                                        </div>
                                    </div>

                                    <h3 class="text-xl font-medium text-white mb-1">{{ selectedItem.title }}</h3>
                                    <p class="text-amber-500 font-bold text-lg mb-6">{{ selectedItem.price }}</p>

                                    <p class="text-sm text-zinc-400 mb-6 text-center">
                                        Pilih metode pemesanan yang Anda inginkan:
                                    </p>

                                    <div class="w-full space-y-3">
                                        <!-- WhatsApp -->
                                        <a :href="getWhatsAppUrl(selectedItem.title)" target="_blank"
                                            class="flex items-center justify-center gap-3 w-full py-3 bg-[#25D366] hover:bg-[#1fb857] text-white rounded-xl font-medium transition-colors">
                                            <MessageCircle class="w-5 h-5" />
                                            Pesan via WhatsApp
                                        </a>

                                        <!-- GrabFood (Placeholder) -->
                                        <a href="#"
                                            class="flex items-center justify-center gap-3 w-full py-3 bg-[#00B14F] hover:bg-[#009e46] text-white rounded-xl font-medium transition-colors opacity-80 hover:opacity-100">
                                            <ShoppingBag class="w-5 h-5" />
                                            Order di GrabFood
                                        </a>

                                        <!-- ShopeeFood (Placeholder) -->
                                        <a href="#"
                                            class="flex items-center justify-center gap-3 w-full py-3 bg-[#EE4D2D] hover:bg-[#d63f21] text-white rounded-xl font-medium transition-colors opacity-80 hover:opacity-100">
                                            <ShoppingBag class="w-5 h-5" />
                                            Order di ShopeeFood
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>

    </div>
</template>

<style scoped>
/* Glass Effects */
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

/* Animations */
.animate-fade-in {
    animation: fadeIn 0.8s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Menu List Transition */
.menu-list-move,
.menu-list-enter-active,
.menu-list-leave-active {
    transition: all 0.5s ease;
}

.menu-list-enter-from,
.menu-list-leave-to {
    opacity: 0;
    transform: translateY(30px);
}

.menu-list-leave-active {
    position: absolute;
    /* Ensures smooth reflow when removing items */
}
</style>
