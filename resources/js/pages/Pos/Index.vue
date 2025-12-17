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
        <div class="h-[calc(100vh-4rem)] flex flex-col bg-muted/30 relative">

            <!-- Tab Switcher -->
            <div class="bg-background border-b border-border px-4 py-2 flex-shrink-0">
                <div class="flex gap-2">
                    <Button :variant="currentView === 'menu' ? 'default' : 'outline'" size="sm"
                        class="flex-1 transition-all duration-300"
                        :class="currentView === 'menu' ? 'bg-primary text-primary-foreground hover:bg-primary/90 border-0' : 'hover:bg-accent hover:text-accent-foreground'"
                        @click="currentView = 'menu'">
                        <UtensilsCrossed class="w-4 h-4 mr-2" />
                        Menu
                    </Button>
                    <Button :variant="currentView === 'active' ? 'default' : 'outline'" size="sm"
                        class="flex-1 relative transition-all duration-300"
                        :class="currentView === 'active' ? 'bg-primary text-primary-foreground hover:bg-primary/90 border-0' : 'hover:bg-accent hover:text-accent-foreground'"
                        @click="currentView = 'active'">
                        <ListOrdered class="w-4 h-4 mr-2" />
                        Pesanan Aktif
                        <Badge v-if="activeOrders.length > 0" class="ml-2 bg-background/20 text-primary-foreground">
                            {{ activeOrders.length }}
                        </Badge>
                    </Button>
                </div>

                <!-- Editing Order Indicator -->
                <div v-if="isEditingOrder"
                    class="mt-2 px-3 py-2 bg-orange-100 dark:bg-orange-950/30 border border-orange-500/20 rounded-lg flex items-center justify-between">
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
                <header class="sticky top-0 z-30 bg-background border-b border-border shadow-sm flex-shrink-0">
                    <div class="px-4 py-3">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                            <Input v-model="searchQuery" type="text" placeholder="Cari menu..."
                                class="pl-10 bg-muted/50 border-input focus:ring-primary" />
                        </div>
                    </div>

                    <div class="px-4 pb-3 overflow-x-auto scrollbar-hide">
                        <div class="flex gap-2 min-w-max">
                            <Button v-for="cat in categoryTabs" :key="cat.key"
                                :variant="activeCategory === cat.key ? 'default' : 'outline'" size="sm"
                                class="rounded-full whitespace-nowrap transition-all"
                                :class="activeCategory === cat.key ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'hover:bg-accent hover:text-accent-foreground'"
                                @click="activeCategory = cat.key">
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
                            class="w-16 h-16 rounded-full bg-slate-200 dark:bg-zinc-800 flex items-center justify-center mb-4">
                            <Search class="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 class="font-medium text-slate-600 dark:text-zinc-300 mb-1">Menu tidak ditemukan</h3>
                        <p class="text-sm text-slate-500">Coba ubah kata kunci pencarian</p>
                    </div>

                    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        <Card v-for="menu in filteredMenus" :key="menu.id"
                            class="overflow-hidden bg-card border border-border shadow-sm rounded-lg group hover:border-primary/50 transition-colors duration-300">
                            <div class="aspect-video bg-muted relative overflow-hidden">
                                <img v-if="menu.image" :src="menu.image" :alt="menu.name"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <UtensilsCrossed class="w-8 h-8 text-muted-foreground/50" />
                                </div>

                                <div v-if="getCartQuantity(menu.id) > 0"
                                    class="absolute top-2 right-2 w-6 h-6 rounded-full bg-primary text-primary-foreground text-xs font-bold flex items-center justify-center shadow-lg animate-in zoom-in">
                                    {{ getCartQuantity(menu.id) }}
                                </div>
                            </div>

                            <CardContent class="p-3">
                                <h3 class="font-medium text-foreground line-clamp-2 mb-1">
                                    {{ menu.name }}
                                </h3>
                                <p class="text-lg font-bold text-primary">
                                    Rp {{ formatPrice(menu.price) }}
                                </p>
                            </CardContent>

                            <CardFooter class="p-3 pt-0">
                                <Button size="sm"
                                    class="w-full bg-primary/10 text-primary hover:bg-primary hover:text-primary-foreground transition-colors border-0"
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
                            class="w-16 h-16 rounded-full bg-slate-200 dark:bg-zinc-800 flex items-center justify-center mb-4">
                            <ListOrdered class="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 class="font-medium text-slate-600 dark:text-zinc-300 mb-1">Tidak ada pesanan aktif</h3>
                        <p class="text-sm text-slate-500">Buat pesanan baru dari tab Menu</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Card v-for="order in activeOrders" :key="order.id"
                            class="cursor-pointer bg-card border-border hover:border-primary/50 transition-all duration-300 hover:shadow-md"
                            @click="selectOrder(order)">
                            <CardHeader class="pb-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <User class="w-5 h-5 text-muted-foreground" />
                                        <span class="font-bold text-lg text-card-foreground">
                                            {{ order.customer_name }}
                                        </span>
                                    </div>
                                    <Badge variant="outline"
                                        class="bg-yellow-500/10 text-yellow-500 border-yellow-500/20">
                                        Belum Bayar
                                    </Badge>
                                </div>
                            </CardHeader>

                            <CardContent class="space-y-3">
                                <div class="text-sm text-muted-foreground">
                                    {{ order.items.length }} item
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                        <Clock class="w-4 h-4" />
                                        {{ formatTimeAgo(order.created_at) }}
                                    </div>
                                    <div class="font-bold text-lg text-primary">
                                        Rp {{ formatPrice(order.total_amount) }}
                                    </div>
                                </div>
                            </CardContent>

                            <CardFooter class="pt-0">
                                <Button size="sm" variant="outline"
                                    class="w-full hover:bg-primary/10 hover:text-primary hover:border-primary/50">
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
                class="fixed bottom-20 md:bottom-0 left-0 right-0 z-40 bg-card/95 backdrop-blur-lg text-card-foreground px-4 py-3 shadow-2xl border-t border-border">
                <div class="flex items-center justify-between max-w-4xl mx-auto">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-primary-foreground">
                                <ShoppingCart class="w-4 h-4" />
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground">
                                    {{ isEditingOrder ? customerName : `${cartItemsCount} item` }}
                                </p>
                                <p class="font-bold text-sm">Rp {{ formatPrice(cartTotal) }}</p>
                            </div>
                        </div>
                    </div>

                    <Button
                        class="bg-primary text-primary-foreground hover:bg-primary/90 font-semibold px-6 border-0 shadow-lg"
                        @click="isCartOpen = true">
                        {{ isEditingOrder ? 'Kelola Pesanan' : 'Lihat Pesanan' }}
                    </Button>
                </div>
            </div>

            <!-- Cart Sheet (Order Management Modal) -->
            <Sheet v-model:open="isCartOpen">
                <SheetContent side="bottom"
                    class="h-[85vh] flex flex-col rounded-t-2xl border-t border-border overflow-hidden p-0"
                    :style="{ backgroundColor: 'hsl(var(--card))' }">

                    <!-- Header -->
                    <SheetHeader class="border-b border-border p-4 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <SheetTitle class="text-xl font-bold text-foreground">
                                    {{ isEditingOrder ? `Pesanan: ${customerName}` : 'Pesanan Baru' }}
                                </SheetTitle>
                                <p v-if="isEditingOrder && hasNewItems" class="text-sm text-primary mt-1">
                                    {{ newItemsOnly.length }} item baru ditambahkan
                                </p>
                            </div>
                            <Button variant="ghost" size="icon" class="rounded-full" @click="isCartOpen = false">
                                <X class="w-5 h-5" />
                            </Button>
                        </div>
                    </SheetHeader>

                    <!-- Scrollable Content Area -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-3">
                        <!-- Empty State for New Orders -->
                        <div v-if="isCartEmpty && !isEditingOrder"
                            class="flex flex-col items-center justify-center h-full text-center">
                            <div class="w-20 h-20 rounded-full bg-muted flex items-center justify-center mb-4">
                                <ShoppingCart class="w-10 h-10 text-muted-foreground" />
                            </div>
                            <h3 class="font-medium text-muted-foreground mb-1">Keranjang Kosong</h3>
                            <p class="text-sm text-muted-foreground">Tambahkan menu untuk memulai pesanan</p>
                        </div>

                        <!-- Existing Items (with staged deletion) -->
                        <template v-if="existingItemsOnly.length > 0">
                            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider px-1 mb-2">
                                Item Pesanan
                            </div>
                            <div v-for="(item, index) in cart" :key="`existing-${item.menu.id}`"
                                v-show="item.isExisting" :class="[
                                    'flex items-center gap-3 p-3 rounded-xl transition-all',
                                    isMarkedForDeletion(item.itemId)
                                        ? 'bg-destructive/10 opacity-60'
                                        : 'bg-muted/50'
                                ]">
                                <div class="w-12 h-12 rounded-lg bg-muted overflow-hidden flex-shrink-0">
                                    <img v-if="item.menu.image" :src="item.menu.image" :alt="item.menu.name"
                                        class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <UtensilsCrossed class="w-5 h-5 text-muted-foreground" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 :class="[
                                        'font-medium text-sm truncate',
                                        isMarkedForDeletion(item.itemId)
                                            ? 'line-through text-destructive'
                                            : 'text-foreground'
                                    ]">
                                        {{ item.menu.name }}
                                    </h4>
                                    <p :class="[
                                        'text-sm',
                                        isMarkedForDeletion(item.itemId) ? 'line-through text-destructive/70' : 'text-muted-foreground'
                                    ]">
                                        {{ item.quantity }}x @ Rp {{ formatPrice(item.menu.price) }}
                                    </p>
                                </div>
                                <div :class="[
                                    'text-sm font-medium',
                                    isMarkedForDeletion(item.itemId) ? 'line-through text-destructive/70' : 'text-muted-foreground'
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
                            <div class="text-xs font-semibold text-primary uppercase tracking-wider px-1 mb-2 mt-4">
                                {{ isEditingOrder ? 'Item Baru (Tambahan)' : 'Item Pesanan' }}
                            </div>
                            <div v-for="(item, index) in cart" :key="`new-${item.menu.id}`" v-show="!item.isExisting"
                                class="flex items-center gap-3 p-3 bg-muted/30 rounded-xl">
                                <div class="w-14 h-14 rounded-lg bg-muted overflow-hidden flex-shrink-0">
                                    <img v-if="item.menu.image" :src="item.menu.image" :alt="item.menu.name"
                                        class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <UtensilsCrossed class="w-6 h-6 text-muted-foreground" />
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm text-foreground truncate">
                                        {{ item.menu.name }}
                                    </h4>
                                    <p class="text-sm font-semibold text-primary">
                                        Rp {{ formatPrice(item.menu.price * item.quantity) }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <Button variant="outline" size="icon-sm" class="rounded-full"
                                        @click="decrementQuantity(index)">
                                        <Minus class="w-4 h-4" />
                                    </Button>
                                    <span class="w-8 text-center font-semibold text-foreground">
                                        {{ item.quantity }}
                                    </span>
                                    <Button variant="outline" size="icon-sm" class="rounded-full"
                                        @click="incrementQuantity(index)">
                                        <Plus class="w-4 h-4" />
                                    </Button>
                                </div>
                            </div>
                        </template>

                        <!-- Customer Name (only for new orders) -->
                        <div v-if="!isEditingOrder" class="pt-4">
                            <label class="block text-sm font-medium text-foreground mb-2">
                                Nama Pelanggan <span class="text-destructive">*</span>
                            </label>
                            <Input v-model="customerName" type="text" placeholder="Masukkan nama pelanggan..."
                                class="bg-background border-input" />
                        </div>
                    </div>

                    <!-- Fixed Footer with Total & Actions -->
                    <div class="p-4 border-t border-border bg-card flex-shrink-0">
                        <!-- Total Section -->
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-medium text-muted-foreground">Total</span>
                            <span class="text-2xl font-bold text-primary">Rp {{ formatPrice(cartTotal) }}</span>
                        </div>

                        <!-- Commit Deletions Button (staged) - Full Width when visible -->
                        <Button v-if="itemsToDelete.length > 0"
                            class="w-full h-12 font-bold bg-red-600 hover:bg-red-700 text-white rounded-xl mb-3"
                            :disabled="isVoiding" @click="commitDeletions">
                            <Trash2 class="w-5 h-5 mr-2" />
                            Batalkan {{ itemsToDelete.length }} Item
                        </Button>

                        <!-- Save Add-ons (only if there are new items on existing order) -->
                        <Button v-if="isEditingOrder && hasNewItems"
                            class="w-full h-12 font-bold bg-primary text-primary-foreground hover:bg-primary/90 rounded-xl mb-3"
                            :disabled="isProcessing || itemsToDelete.length > 0" @click="addItemsToOrder">
                            <Plus class="w-5 h-5 mr-2" />
                            Simpan Tambahan ({{ newItemsOnly.length }} Item)
                        </Button>

                        <!-- Button Grid for Main Actions -->
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Button 1: Tambah Lagi (Secondary) -->
                            <Button
                                class="h-12 font-semibold bg-transparent border border-border text-foreground hover:bg-muted rounded-xl"
                                :disabled="itemsToDelete.length > 0" @click="addMoreItems">
                                <Plus class="w-4 h-4 mr-2" />
                                Tambah Lagi
                            </Button>

                            <!-- Button 2: Bayar Sekarang (Primary) -->
                            <Button v-if="isEditingOrder"
                                class="h-12 font-bold bg-primary text-primary-foreground shadow-lg shadow-primary/20 hover:bg-primary/90 rounded-xl"
                                :disabled="isProcessing || hasNewItems || itemsToDelete.length > 0"
                                @click="openCheckout">
                                <CreditCard class="w-4 h-4 mr-2" />
                                Bayar Sekarang
                            </Button>

                            <!-- New Order: Process Button -->
                            <Button v-else
                                class="h-12 font-bold bg-primary text-primary-foreground shadow-lg shadow-primary/20 hover:bg-primary/90 rounded-xl"
                                :disabled="isCartEmpty || !customerName.trim() || isProcessing" @click="processOrder">
                                <ShoppingCart class="w-4 h-4 mr-2" />
                                Proses
                            </Button>
                        </div>
                    </div>
                </SheetContent>
            </Sheet>

            <CheckoutDialog v-if="selectedTransaction" :open="isCheckoutOpen" :transaction="selectedTransaction"
                @update:open="isCheckoutOpen = $event" @success="onCheckoutSuccess" />

        </div>
    </AppLayout>
</template>
