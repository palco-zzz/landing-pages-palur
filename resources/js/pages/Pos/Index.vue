<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Search, Plus, Minus, Trash2, ShoppingCart, UtensilsCrossed, Clock, User, CreditCard, ListOrdered, X, RotateCcw } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// Layout & Components
import AppLayout from '@/layouts/AppLayout.vue';
import CheckoutDialog from './CheckoutDialog.vue';
import ReceiptHandler from './ReceiptHandler.vue';
import { type BreadcrumbItem } from '@/types';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
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
interface Category {
    id: number;
    name: string;
}

interface Menu {
    id: number;
    name: string;
    category_id: number;
    category: Category | null;
    price: number;
    image: string | null;
}

interface TransactionItem {
    id: number;
    menu: Menu;
    quantity: number;
    price: number;
    subtotal: number;
    is_printed: boolean;
    status?: 'active' | 'void';
}

interface Transaction {
    id: number;
    uuid: string;
    customer_name: string;
    total_amount: number;
    status: 'unpaid' | 'paid' | 'cancelled';
    items: TransactionItem[];
    created_at: string;
}

interface CartItem {
    menu: Menu;
    quantity: number;
    isExisting: boolean;
    itemId?: number; // Original TransactionItem ID for voiding
}

// Props
const props = defineProps<{
    menus: Menu[];
    categories: Category[];
    activeOrders: Transaction[];
}>();

// State
const currentView = ref<'menu' | 'active'>('menu');
const searchQuery = ref('');
const activeCategory = ref<string>('all');
const cart = ref<CartItem[]>([]);
const isCartOpen = ref(false);
const customerName = ref('');
const isProcessing = ref(false);
const selectedTransaction = ref<Transaction | null>(null);
const isCheckoutOpen = ref(false);
const isVoiding = ref(false);
const itemsToDelete = ref<number[]>([]); // Staged deletion IDs

// Dynamic categories with "All" option
const categoryTabs = computed(() => [
    { key: 'all', label: 'Semua' },
    ...props.categories.map(cat => ({ key: cat.name, label: cat.name })),
]);

// Computed
const filteredMenus = computed(() => {
    let result = props.menus;

    if (activeCategory.value !== 'all') {
        result = result.filter(menu => menu.category?.name === activeCategory.value);
    }

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

const newItemsOnly = computed(() => {
    return cart.value.filter(item => !item.isExisting);
});

const existingItemsOnly = computed(() => {
    return cart.value.filter(item => item.isExisting);
});

const hasNewItems = computed(() => newItemsOnly.value.length > 0);

const isCartEmpty = computed(() => cart.value.length === 0);

const isEditingOrder = computed(() => selectedTransaction.value !== null);

// Methods
const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatTimeAgo = (dateString: string): string => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'Baru saja';
    if (diffMins < 60) return `${diffMins} menit lalu`;

    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours} jam lalu`;

    return date.toLocaleDateString('id-ID');
};

const addToCart = (menu: Menu) => {
    const existingItem = cart.value.find(item => item.menu.id === menu.id && !item.isExisting);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.value.push({ menu, quantity: 1, isExisting: false });
    }
};

const incrementQuantity = (index: number) => {
    if (!cart.value[index].isExisting) {
        cart.value[index].quantity++;
    }
};

const decrementQuantity = (index: number) => {
    if (cart.value[index].isExisting) return;

    if (cart.value[index].quantity > 1) {
        cart.value[index].quantity--;
    } else {
        removeFromCart(index);
    }
};

const removeFromCart = (index: number) => {
    if (cart.value[index].isExisting) return;
    cart.value.splice(index, 1);
};

const clearCart = () => {
    cart.value = [];
    customerName.value = '';
    selectedTransaction.value = null;
    itemsToDelete.value = [];
};

const clearNewItems = () => {
    cart.value = cart.value.filter(item => item.isExisting);
};

// Select an active order - UX FIX: Don't auto-open sheet
const selectOrder = (transaction: Transaction) => {
    selectedTransaction.value = transaction;
    customerName.value = transaction.customer_name;
    itemsToDelete.value = []; // Clear any previous deletion marks

    // Load existing items into cart (only active items)
    cart.value = transaction.items
        .filter(item => item.status !== 'void')
        .map(item => ({
            menu: item.menu,
            quantity: item.quantity,
            isExisting: true,
            itemId: item.id,
        }));

    // Switch to menu view and open cart immediately for managing
    currentView.value = 'menu';
    isCartOpen.value = true;
};

// Toggle delete mark for staged deletion
const toggleDeleteMark = (item: CartItem) => {
    if (!item.itemId) return;

    const index = itemsToDelete.value.indexOf(item.itemId);
    if (index > -1) {
        // Already marked, unmark it (undo)
        itemsToDelete.value.splice(index, 1);
    } else {
        // Mark for deletion
        itemsToDelete.value.push(item.itemId);
    }
};

// Check if an item is marked for deletion
const isMarkedForDeletion = (itemId: number | undefined): boolean => {
    if (!itemId) return false;
    return itemsToDelete.value.includes(itemId);
};

// Commit all staged deletions (batch void)
const commitDeletions = () => {
    if (itemsToDelete.value.length === 0) return;

    if (!confirm(`Batalkan ${itemsToDelete.value.length} item? Struk void akan dicetak.`)) {
        return;
    }

    isVoiding.value = true;

    router.post(route('pos.items.batch-void'), {
        item_ids: itemsToDelete.value,
    }, {
        preserveScroll: true,
        preserveState: false, // Refresh to get updated data
        onSuccess: () => {
            const count = itemsToDelete.value.length;

            // Remove voided items from local cart
            cart.value = cart.value.filter(i => !i.itemId || !itemsToDelete.value.includes(i.itemId));
            itemsToDelete.value = [];

            toast.success(`${count} Item Dibatalkan`, {
                description: 'Struk void telah dicetak.',
            });

            // If all existing items are voided, close and clear
            if (existingItemsOnly.value.length === 0 && newItemsOnly.value.length === 0) {
                isCartOpen.value = false;
                clearCart();
            }
        },
        onError: (errors) => {
            console.error('Batch void failed:', errors);
            toast.error('Gagal Membatalkan', {
                description: 'Terjadi kesalahan saat membatalkan item.',
            });
        },
        onFinish: () => {
            isVoiding.value = false;
        },
    });
};

// Add more items - closes modal, keeps transaction context
const addMoreItems = () => {
    isCartOpen.value = false;
    // selectedTransaction and cart remain active, so user can add more
    toast.info('Tambah Menu', {
        description: `Pilih menu untuk ditambahkan ke pesanan ${customerName.value}.`,
    });
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

    const orderData = {
        customer_name: customerName.value,
        items: cart.value.filter(item => !item.isExisting).map(item => ({
            menu_id: item.menu.id,
            quantity: item.quantity,
            price: item.menu.price,
            subtotal: item.menu.price * item.quantity,
        })),
    };

    router.post(route('pos.orders.store'), orderData, {
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

const addItemsToOrder = () => {
    if (!selectedTransaction.value) return;
    if (!hasNewItems.value) {
        alert('Tidak ada item baru untuk ditambahkan.');
        return;
    }

    isProcessing.value = true;

    const orderData = {
        items: newItemsOnly.value.map(item => ({
            menu_id: item.menu.id,
            quantity: item.quantity,
            price: item.menu.price,
            subtotal: item.menu.price * item.quantity,
        })),
    };

    router.post(route('pos.orders.add', { transaction: selectedTransaction.value.id }), orderData, {
        onSuccess: () => {
            clearCart();
            isCartOpen.value = false;
            toast.success('Berhasil', { description: 'Item baru telah ditambahkan ke pesanan.' });
        },
        onError: (errors) => {
            console.error('Add items failed:', errors);
            alert('Gagal menambah pesanan. Silakan coba lagi.');
        },
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};

const openCheckout = () => {
    isCartOpen.value = false;
    isCheckoutOpen.value = true;
};

const onCheckoutSuccess = () => {
    clearCart();
    isCheckoutOpen.value = false;
    currentView.value = 'active';
};

const getCartQuantity = (menuId: number): number => {
    const item = cart.value.find(item => item.menu.id === menuId && !item.isExisting);
    return item?.quantity || 0;
};

// Close modal (just closes, changes already saved)
const closeOrderSheet = () => {
    isCartOpen.value = false;
};
</script>

<template>

    <Head title="POS System" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-100 dark:bg-slate-950 relative">

            <!-- Tab Switcher -->
            <div
                class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-2 flex-shrink-0">
                <div class="flex gap-2">
                    <Button :variant="currentView === 'menu' ? 'default' : 'outline'" size="sm" class="flex-1"
                        @click="currentView = 'menu'">
                        <UtensilsCrossed class="w-4 h-4 mr-2" />
                        Menu
                    </Button>
                    <Button :variant="currentView === 'active' ? 'default' : 'outline'" size="sm"
                        class="flex-1 relative" @click="currentView = 'active'">
                        <ListOrdered class="w-4 h-4 mr-2" />
                        Pesanan Aktif
                        <Badge v-if="activeOrders.length > 0" class="ml-2 bg-orange-500 text-white">
                            {{ activeOrders.length }}
                        </Badge>
                    </Button>
                </div>

                <!-- Editing Order Indicator -->
                <div v-if="isEditingOrder"
                    class="mt-2 px-3 py-2 bg-orange-100 dark:bg-orange-950 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <User class="w-4 h-4 text-orange-600" />
                        <span class="text-sm font-medium text-orange-700 dark:text-orange-300">
                            {{ customerName }}
                        </span>
                    </div>
                    <Button variant="ghost" size="sm" class="text-orange-600 hover:text-orange-700 h-7 px-2"
                        @click="clearCart">
                        Batal
                    </Button>
                </div>
            </div>

            <!-- Menu View -->
            <template v-if="currentView === 'menu'">
                <!-- Search & Categories -->
                <header
                    class="sticky top-0 z-30 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm flex-shrink-0">
                    <div class="px-4 py-3">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <Input v-model="searchQuery" type="text" placeholder="Cari menu..."
                                class="pl-10 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700" />
                        </div>
                    </div>

                    <div class="px-4 pb-3 overflow-x-auto scrollbar-hide">
                        <div class="flex gap-2 min-w-max">
                            <Button v-for="cat in categoryTabs" :key="cat.key"
                                :variant="activeCategory === cat.key ? 'default' : 'outline'" size="sm"
                                class="rounded-full whitespace-nowrap" @click="activeCategory = cat.key">
                                {{ cat.label }}
                            </Button>
                        </div>
                    </div>
                </header>

                <!-- Menu Grid -->
                <main class="flex-1 overflow-y-auto p-4 pb-24">
                    <div v-if="filteredMenus.length === 0"
                        class="flex flex-col items-center justify-center py-16 text-center">
                        <div
                            class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center mb-4">
                            <Search class="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 class="font-medium text-slate-600 dark:text-slate-300 mb-1">Menu tidak ditemukan</h3>
                        <p class="text-sm text-slate-500">Coba ubah kata kunci pencarian</p>
                    </div>

                    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        <Card v-for="menu in filteredMenus" :key="menu.id"
                            class="overflow-hidden border-slate-200 dark:border-slate-800 hover:shadow-md transition-shadow">
                            <div class="aspect-video bg-slate-200 dark:bg-slate-800 relative overflow-hidden">
                                <img v-if="menu.image" :src="menu.image" :alt="menu.name"
                                    class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <UtensilsCrossed class="w-8 h-8 text-slate-400" />
                                </div>

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
            </template>

            <!-- Active Orders View -->
            <template v-else>
                <main class="flex-1 overflow-y-auto p-4">
                    <div v-if="activeOrders.length === 0"
                        class="flex flex-col items-center justify-center py-16 text-center">
                        <div
                            class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center mb-4">
                            <ListOrdered class="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 class="font-medium text-slate-600 dark:text-slate-300 mb-1">Tidak ada pesanan aktif</h3>
                        <p class="text-sm text-slate-500">Buat pesanan baru dari tab Menu</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Card v-for="order in activeOrders" :key="order.id"
                            class="cursor-pointer hover:shadow-lg transition-shadow border-slate-200 dark:border-slate-800"
                            @click="selectOrder(order)">
                            <CardHeader class="pb-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <User class="w-5 h-5 text-slate-500" />
                                        <span class="font-bold text-lg text-slate-900 dark:text-white">
                                            {{ order.customer_name }}
                                        </span>
                                    </div>
                                    <Badge variant="outline" class="bg-yellow-100 text-yellow-800 border-yellow-300">
                                        Belum Bayar
                                    </Badge>
                                </div>
                            </CardHeader>

                            <CardContent class="space-y-3">
                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ order.items.length }} item
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1 text-sm text-slate-500">
                                        <Clock class="w-4 h-4" />
                                        {{ formatTimeAgo(order.created_at) }}
                                    </div>
                                    <div class="font-bold text-lg text-orange-600 dark:text-orange-400">
                                        Rp {{ formatPrice(order.total_amount) }}
                                    </div>
                                </div>
                            </CardContent>

                            <CardFooter class="pt-0">
                                <Button size="sm" variant="outline" class="w-full">
                                    <Plus class="w-4 h-4 mr-2" />
                                    Kelola Pesanan
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>
                </main>
            </template>

            <!-- Floating Cart Summary Bar -->
            <div v-if="!isCartEmpty || isEditingOrder"
                class="absolute bottom-0 left-0 right-0 z-40 bg-slate-900 dark:bg-slate-800 text-white px-4 py-3 shadow-2xl border-t border-slate-700">
                <div class="flex items-center justify-between max-w-4xl mx-auto">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                                <ShoppingCart class="w-4 h-4" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">
                                    {{ isEditingOrder ? customerName : `${cartItemsCount} item` }}
                                </p>
                                <p class="font-bold text-sm">Rp {{ formatPrice(cartTotal) }}</p>
                            </div>
                        </div>
                    </div>

                    <Button class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6"
                        @click="isCartOpen = true">
                        {{ isEditingOrder ? 'Kelola Pesanan' : 'Lihat Pesanan' }}
                    </Button>
                </div>
            </div>

            <!-- Cart Sheet (Order Management Modal) -->
            <Sheet v-model:open="isCartOpen">
                <SheetContent side="bottom" class="h-[85vh] flex flex-col rounded-t-3xl">
                    <SheetHeader class="border-b border-slate-200 dark:border-slate-700 pb-4">
                        <div>
                            <SheetTitle class="text-xl font-bold">
                                {{ isEditingOrder ? `Pesanan: ${customerName}` : 'Pesanan Baru' }}
                            </SheetTitle>
                            <p v-if="isEditingOrder && hasNewItems" class="text-sm text-orange-600 mt-1">
                                {{ newItemsOnly.length }} item baru ditambahkan
                            </p>
                        </div>
                    </SheetHeader>

                    <div class="flex-1 overflow-y-auto py-4 space-y-3">
                        <!-- Empty State for New Orders -->
                        <div v-if="isCartEmpty && !isEditingOrder"
                            class="flex flex-col items-center justify-center h-full text-center">
                            <div
                                class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                                <ShoppingCart class="w-10 h-10 text-slate-400" />
                            </div>
                            <h3 class="font-medium text-slate-600 dark:text-slate-300 mb-1">Keranjang Kosong</h3>
                            <p class="text-sm text-slate-500">Tambahkan menu untuk memulai pesanan</p>
                        </div>

                        <!-- Existing Items (with staged deletion) -->
                        <template v-if="existingItemsOnly.length > 0">
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1 mb-2">
                                Item Pesanan
                            </div>
                            <div v-for="(item, index) in cart" :key="`existing-${item.menu.id}`"
                                v-show="item.isExisting" :class="[
                                    'flex items-center gap-3 p-3 rounded-xl transition-all',
                                    isMarkedForDeletion(item.itemId)
                                        ? 'bg-red-100 dark:bg-red-950 opacity-60'
                                        : 'bg-slate-100 dark:bg-slate-700'
                                ]">
                                <div
                                    class="w-12 h-12 rounded-lg bg-slate-200 dark:bg-slate-600 overflow-hidden flex-shrink-0">
                                    <img v-if="item.menu.image" :src="item.menu.image" :alt="item.menu.name"
                                        class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <UtensilsCrossed class="w-5 h-5 text-slate-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 :class="[
                                        'font-medium text-sm truncate',
                                        isMarkedForDeletion(item.itemId)
                                            ? 'line-through text-red-500'
                                            : 'text-slate-600 dark:text-slate-300'
                                    ]">
                                        {{ item.menu.name }}
                                    </h4>
                                    <p :class="[
                                        'text-sm',
                                        isMarkedForDeletion(item.itemId) ? 'line-through text-red-400' : 'text-slate-500'
                                    ]">
                                        {{ item.quantity }}x @ Rp {{ formatPrice(item.menu.price) }}
                                    </p>
                                </div>
                                <div :class="[
                                    'text-sm font-medium',
                                    isMarkedForDeletion(item.itemId) ? 'line-through text-red-400' : 'text-slate-500'
                                ]">
                                    Rp {{ formatPrice(item.menu.price * item.quantity) }}
                                </div>
                                <!-- Toggle Delete Mark Button -->
                                <Button v-if="item.itemId" variant="ghost" size="icon-sm" :class="[
                                    'rounded-full',
                                    isMarkedForDeletion(item.itemId)
                                        ? 'text-green-600 hover:text-green-700 hover:bg-green-100 dark:hover:bg-green-950'
                                        : 'text-red-500 hover:text-red-600 hover:bg-red-100 dark:hover:bg-red-950'
                                ]" :disabled="isVoiding" @click="toggleDeleteMark(item)">
                                    <RotateCcw v-if="isMarkedForDeletion(item.itemId)" class="w-4 h-4" />
                                    <Trash2 v-else class="w-4 h-4" />
                                </Button>
                            </div>
                        </template>

                        <!-- New Items (editable) -->
                        <template v-if="newItemsOnly.length > 0">
                            <div class="text-xs font-semibold text-orange-600 uppercase tracking-wider px-1 mb-2 mt-4">
                                {{ isEditingOrder ? 'Item Baru (Tambahan)' : 'Item Pesanan' }}
                            </div>
                            <div v-for="(item, index) in cart" :key="`new-${item.menu.id}`" v-show="!item.isExisting"
                                class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <div
                                    class="w-16 h-16 rounded-lg bg-slate-200 dark:bg-slate-700 overflow-hidden flex-shrink-0">
                                    <img v-if="item.menu.image" :src="item.menu.image" :alt="item.menu.name"
                                        class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <UtensilsCrossed class="w-6 h-6 text-slate-400" />
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm text-slate-900 dark:text-white truncate">
                                        {{ item.menu.name }}
                                    </h4>
                                    <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">
                                        Rp {{ formatPrice(item.menu.price * item.quantity) }}
                                    </p>
                                </div>

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
                        </template>
                    </div>

                    <!-- Customer Name & Total -->
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-4">
                        <!-- Customer Name (only for new orders) -->
                        <div v-if="!isEditingOrder">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Nama Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <Input v-model="customerName" type="text" placeholder="Masukkan nama pelanggan..."
                                class="bg-slate-50 dark:bg-slate-800" />
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-slate-100 dark:bg-slate-800 rounded-xl p-4">
                            <div v-if="isEditingOrder && existingItemsOnly.length > 0"
                                class="flex items-center justify-between mb-2 text-sm">
                                <span class="text-slate-500">Item sebelumnya</span>
                                <span class="text-slate-500">
                                    Rp {{formatPrice(existingItemsOnly.reduce((sum, item) => sum + (item.menu.price *
                                        item.quantity), 0))}}
                                </span>
                            </div>
                            <div v-if="hasNewItems" class="flex items-center justify-between mb-2 text-sm">
                                <span class="text-orange-600 font-medium">{{ isEditingOrder ? 'Tambahan baru' :
                                    'Subtotal' }}</span>
                                <span class="text-orange-600 font-medium">
                                    Rp {{formatPrice(newItemsOnly.reduce((sum, item) => sum + (item.menu.price *
                                        item.quantity),
                                        0))}}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between text-lg border-t border-slate-200 dark:border-slate-700 pt-2 mt-2">
                                <span class="font-bold text-slate-900 dark:text-white">Total</span>
                                <span class="font-bold text-orange-600 dark:text-orange-400">Rp {{
                                    formatPrice(cartTotal) }}</span>
                            </div>
                        </div>
                    </div>

                    <SheetFooter class="pt-4 gap-2">
                        <!-- New Order: Process -->
                        <template v-if="!isEditingOrder">
                            <Button
                                class="w-full h-14 text-lg font-bold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-lg"
                                :disabled="isCartEmpty || !customerName.trim() || isProcessing" @click="processOrder">
                                <ShoppingCart class="w-5 h-5 mr-2" />
                                Proses Pesanan
                            </Button>
                        </template>

                        <!-- Editing Order -->
                        <template v-else>
                            <div class="w-full space-y-2">
                                <!-- Commit Deletions Button (staged) -->
                                <Button v-if="itemsToDelete.length > 0"
                                    class="w-full h-12 font-bold bg-red-600 hover:bg-red-700 text-white rounded-xl"
                                    :disabled="isVoiding" @click="commitDeletions">
                                    <Trash2 class="w-5 h-5 mr-2" />
                                    Batalkan {{ itemsToDelete.length }} Item
                                </Button>

                                <!-- Add More Items Button -->
                                <Button
                                    class="w-full h-12 font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl"
                                    :disabled="itemsToDelete.length > 0" @click="addMoreItems">
                                    <Plus class="w-5 h-5 mr-2" />
                                    Tambah Menu
                                </Button>

                                <!-- Save Add-ons (only if there are new items) -->
                                <Button v-if="hasNewItems"
                                    class="w-full h-12 font-bold bg-orange-500 hover:bg-orange-600 text-white rounded-xl"
                                    :disabled="isProcessing || itemsToDelete.length > 0" @click="addItemsToOrder">
                                    <ShoppingCart class="w-5 h-5 mr-2" />
                                    Simpan Tambahan
                                </Button>

                                <!-- Checkout Button (always visible when editing) -->
                                <Button
                                    class="w-full h-12 font-bold bg-green-600 hover:bg-green-700 text-white rounded-xl"
                                    :disabled="isProcessing || hasNewItems || itemsToDelete.length > 0"
                                    @click="openCheckout">
                                    <CreditCard class="w-5 h-5 mr-2" />
                                    Bayar / Checkout
                                </Button>

                                <p v-if="hasNewItems" class="text-xs text-center text-slate-500">
                                    Simpan tambahan terlebih dahulu sebelum bayar
                                </p>
                                <p v-else-if="itemsToDelete.length > 0" class="text-xs text-center text-red-500">
                                    Konfirmasi pembatalan item terlebih dahulu
                                </p>
                            </div>
                        </template>
                    </SheetFooter>
                </SheetContent>
            </Sheet>

            <!-- Checkout Dialog -->
            <CheckoutDialog v-model:open="isCheckoutOpen" :transaction="selectedTransaction"
                @success="onCheckoutSuccess" />

            <!-- Receipt Printer Handler -->
            <ReceiptHandler />
        </div>
    </AppLayout>
</template>

<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
