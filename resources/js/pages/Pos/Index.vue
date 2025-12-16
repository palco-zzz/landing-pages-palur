<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Search, Plus, Minus, Trash2, ShoppingCart, UtensilsCrossed } from 'lucide-vue-next';

// Layout
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetFooter,
} from '@/components/ui/sheet';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'POS System',
        href: route('pos.index'),
    },
];

// Types
interface Menu {
    id: number;
    name: string;
    category: 'makanan' | 'minuman' | 'tambahan';
    price: number;
    image: string | null;
    is_available: boolean;
}

interface CartItem {
    menu: Menu;
    quantity: number;
}

// Props
const props = defineProps<{
    menus: Menu[];
}>();

// State
const searchQuery = ref('');
const activeCategory = ref<string>('all');
const cart = ref<CartItem[]>([]);
const isCartOpen = ref(false);
const customerName = ref('');
const isProcessing = ref(false);

// Categories
const categories = [
    { key: 'all', label: 'Semua' },
    { key: 'makanan', label: 'Makanan' },
    { key: 'minuman', label: 'Minuman' },
    { key: 'tambahan', label: 'Tambahan' },
];

// Computed
const filteredMenus = computed(() => {
    let result = props.menus.filter(menu => menu.is_available);

    // Filter by category
    if (activeCategory.value !== 'all') {
        result = result.filter(menu => menu.category === activeCategory.value);
    }

    // Filter by search query
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(menu =>
            menu.name.toLowerCase().includes(query)
        );
    }

    return result;
});

const cartItemsCount = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.quantity, 0);
});

const cartTotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + (item.menu.price * item.quantity), 0);
});

const isCartEmpty = computed(() => cart.value.length === 0);

// Methods
const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const addToCart = (menu: Menu) => {
    const existingItem = cart.value.find(item => item.menu.id === menu.id);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.value.push({ menu, quantity: 1 });
    }
};

const incrementQuantity = (index: number) => {
    cart.value[index].quantity++;
};

const decrementQuantity = (index: number) => {
    if (cart.value[index].quantity > 1) {
        cart.value[index].quantity--;
    } else {
        removeFromCart(index);
    }
};

const removeFromCart = (index: number) => {
    cart.value.splice(index, 1);
};

const clearCart = () => {
    cart.value = [];
    customerName.value = '';
};

const processOrder = () => {
    if (!customerName.value.trim()) {
        alert('Mohon isi nama pelanggan!');
        return;
    }

    if (isCartEmpty.value) {
        alert('Keranjang masih kosong!');
        return;
    }

    isProcessing.value = true;

    // Prepare order data
    const orderData = {
        customer_name: customerName.value,
        items: cart.value.map(item => ({
            menu_id: item.menu.id,
            quantity: item.quantity,
            price: item.menu.price,
            subtotal: item.menu.price * item.quantity,
        })),
    };

    router.post('/pos/orders', orderData, {
        onSuccess: () => {
            clearCart();
            isCartOpen.value = false;
        },
        onError: (errors) => {
            console.error('Order failed:', errors);
            alert('Gagal membuat pesanan. Silakan coba lagi.');
        },
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};

// Get item quantity in cart for display on menu cards
const getCartQuantity = (menuId: number): number => {
    const item = cart.value.find(item => item.menu.id === menuId);
    return item?.quantity || 0;
};
</script>

<template>

    <Head title="POS System" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Full Height POS Container -->
        <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-100 dark:bg-slate-950 relative">

            <!-- Sticky Header (Search + Categories) -->
            <header
                class="sticky top-0 z-30 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm flex-shrink-0">
                <!-- Search Bar -->
                <div class="px-4 py-3">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                        <Input v-model="searchQuery" type="text" placeholder="Cari menu..."
                            class="pl-10 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700" />
                    </div>
                </div>

                <!-- Category Pills -->
                <div class="px-4 pb-3 overflow-x-auto scrollbar-hide">
                    <div class="flex gap-2 min-w-max">
                        <Button v-for="cat in categories" :key="cat.key"
                            :variant="activeCategory === cat.key ? 'default' : 'outline'" size="sm"
                            class="rounded-full whitespace-nowrap" @click="activeCategory = cat.key">
                            {{ cat.label }}
                        </Button>
                    </div>
                </div>
            </header>

            <!-- Menu Grid (Scrollable) -->
            <main class="flex-1 overflow-y-auto p-4 pb-24">
                <!-- Empty State -->
                <div v-if="filteredMenus.length === 0"
                    class="flex flex-col items-center justify-center py-16 text-center">
                    <div
                        class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center mb-4">
                        <Search class="w-8 h-8 text-slate-400" />
                    </div>
                    <h3 class="font-medium text-slate-600 dark:text-slate-300 mb-1">Menu tidak ditemukan</h3>
                    <p class="text-sm text-slate-500">Coba ubah kata kunci pencarian</p>
                </div>

                <!-- Menu Cards Grid -->
                <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <Card v-for="menu in filteredMenus" :key="menu.id"
                        class="overflow-hidden border-slate-200 dark:border-slate-800 hover:shadow-md transition-shadow">
                        <!-- Image -->
                        <div class="aspect-video bg-slate-200 dark:bg-slate-800 relative overflow-hidden">
                            <img v-if="menu.image" :src="menu.image" :alt="menu.name"
                                class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <UtensilsCrossed class="w-8 h-8 text-slate-400" />
                            </div>

                            <!-- Quantity Badge -->
                            <div v-if="getCartQuantity(menu.id) > 0"
                                class="absolute top-2 right-2 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center shadow-lg">
                                {{ getCartQuantity(menu.id) }}
                            </div>
                        </div>

                        <CardContent class="p-3">
                            <h3 class="font-medium text-sm text-slate-900 dark:text-white line-clamp-2 mb-1">
                                {{ menu.name }}
                            </h3>
                            <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">
                                Rp {{ formatPrice(menu.price) }}
                            </p>
                        </CardContent>

                        <CardFooter class="p-3 pt-0">
                            <Button size="sm" class="w-full bg-orange-500 hover:bg-orange-600 text-white"
                                @click="addToCart(menu)">
                                <Plus class="w-4 h-4 mr-1" />
                                Tambah
                            </Button>
                        </CardFooter>
                    </Card>
                </div>
            </main>

            <!-- Floating Cart Summary Bar -->
            <div v-if="!isCartEmpty"
                class="absolute bottom-0 left-0 right-0 z-40 bg-slate-900 dark:bg-slate-800 text-white px-4 py-3 shadow-2xl border-t border-slate-700">
                <div class="flex items-center justify-between max-w-4xl mx-auto">
                    <!-- Left: Items & Total -->
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                                <ShoppingCart class="w-4 h-4" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">{{ cartItemsCount }} item</p>
                                <p class="font-bold text-sm">Rp {{ formatPrice(cartTotal) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: View Order Button -->
                    <Button class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6"
                        @click="isCartOpen = true">
                        Lihat Pesanan
                    </Button>
                </div>
            </div>

            <!-- Cart Sheet (Bottom Drawer) -->
            <Sheet v-model:open="isCartOpen">
                <SheetContent side="bottom" class="h-[85vh] flex flex-col rounded-t-3xl">
                    <SheetHeader class="border-b border-slate-200 dark:border-slate-700 pb-4">
                        <div class="flex items-center justify-between">
                            <SheetTitle class="text-xl font-bold">Pesanan Saat Ini</SheetTitle>
                            <Button v-if="!isCartEmpty" variant="ghost" size="sm"
                                class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950"
                                @click="clearCart">
                                <Trash2 class="w-4 h-4 mr-1" />
                                Hapus Semua
                            </Button>
                        </div>
                    </SheetHeader>

                    <!-- Cart Items List -->
                    <div class="flex-1 overflow-y-auto py-4 space-y-3">
                        <div v-if="isCartEmpty" class="flex flex-col items-center justify-center h-full text-center">
                            <div
                                class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                                <ShoppingCart class="w-10 h-10 text-slate-400" />
                            </div>
                            <h3 class="font-medium text-slate-600 dark:text-slate-300 mb-1">Keranjang Kosong</h3>
                            <p class="text-sm text-slate-500">Tambahkan menu untuk memulai pesanan</p>
                        </div>

                        <div v-for="(item, index) in cart" :key="item.menu.id"
                            class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                            <!-- Item Image -->
                            <div
                                class="w-16 h-16 rounded-lg bg-slate-200 dark:bg-slate-700 overflow-hidden flex-shrink-0">
                                <img v-if="item.menu.image" :src="item.menu.image" :alt="item.menu.name"
                                    class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <UtensilsCrossed class="w-6 h-6 text-slate-400" />
                                </div>
                            </div>

                            <!-- Item Details -->
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-sm text-slate-900 dark:text-white truncate">
                                    {{ item.menu.name }}
                                </h4>
                                <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">
                                    Rp {{ formatPrice(item.menu.price * item.quantity) }}
                                </p>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-2">
                                <Button variant="outline" size="icon-sm" class="rounded-full"
                                    @click="decrementQuantity(index)">
                                    <Minus class="w-4 h-4" />
                                </Button>
                                <span class="w-8 text-center font-semibold text-slate-900 dark:text-white">
                                    {{ item.quantity }}
                                </span>
                                <Button variant="outline" size="icon-sm" class="rounded-full"
                                    @click="incrementQuantity(index)">
                                    <Plus class="w-4 h-4" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Name Input & Total -->
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-4">
                        <!-- Customer Name -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Nama Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <Input v-model="customerName" type="text" placeholder="Masukkan nama pelanggan..."
                                class="bg-slate-50 dark:bg-slate-800" />
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-slate-100 dark:bg-slate-800 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-slate-600 dark:text-slate-400">Subtotal</span>
                                <span class="font-medium text-slate-900 dark:text-white">Rp {{ formatPrice(cartTotal)
                                    }}</span>
                            </div>
                            <div class="flex items-center justify-between text-lg">
                                <span class="font-bold text-slate-900 dark:text-white">Total</span>
                                <span class="font-bold text-orange-600 dark:text-orange-400">Rp {{
                                    formatPrice(cartTotal) }}</span>
                            </div>
                        </div>
                    </div>

                    <SheetFooter class="pt-4">
                        <Button
                            class="w-full h-14 text-lg font-bold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-lg"
                            :disabled="isCartEmpty || !customerName.trim() || isProcessing" @click="processOrder">
                            <ShoppingCart class="w-5 h-5 mr-2" />
                            Proses Pesanan
                        </Button>
                    </SheetFooter>
                </SheetContent>
            </Sheet>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Hide scrollbar for category pills */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Smooth transitions */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
