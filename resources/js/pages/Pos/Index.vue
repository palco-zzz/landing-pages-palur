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
const getGradientClass = (id: number): string => {
    const gradients = [
        'bg-gradient-to-br from-orange-400 to-red-500',
        'bg-gradient-to-br from-blue-400 to-indigo-500',
        'bg-gradient-to-br from-emerald-400 to-teal-500',
        'bg-gradient-to-br from-purple-400 to-pink-500',
        'bg-gradient-to-br from-amber-400 to-orange-500',
        'bg-gradient-to-br from-cyan-400 to-blue-500',
        'bg-gradient-to-br from-rose-400 to-red-500',
        'bg-gradient-to-br from-lime-400 to-green-500',
    ];
    return gradients[id % gradients.length];
};

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
                <header class="sticky top-0 z-30 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 border-b border-border shadow-sm flex-shrink-0">
                    <div class="px-4 py-3">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                            <Input v-model="searchQuery" type="text" placeholder="Cari menu..."
                                class="pl-10 bg-muted/50 border-transparent focus:border-primary focus:ring-primary/20 transition-all" />
                        </div>
                    </div>

                    <div class="px-4 pb-3 overflow-x-auto scrollbar-hide">
                        <div class="flex gap-2 min-w-max">
                            <Button v-for="cat in categoryTabs" :key="cat.key"
                                size="sm"
                                class="rounded-full whitespace-nowrap transition-all duration-300 font-medium border"
                                :class="activeCategory === cat.key 
                                    ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white border-transparent shadow-md hover:shadow-lg scale-105' 
                                    : 'bg-card hover:bg-muted text-muted-foreground border-border hover:border-border/80'"
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
                            class="w-16 h-16 rounded-full bg-slate-100 dark:bg-zinc-800/50 flex items-center justify-center mb-4">
                            <Search class="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 class="font-medium text-slate-600 dark:text-zinc-300 mb-1">Menu tidak ditemukan</h3>
                        <p class="text-sm text-slate-500">Coba ubah kata kunci pencarian</p>
                    </div>

                    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <Card v-for="(menu, index) in filteredMenus" :key="menu.id"
                            class="overflow-hidden bg-card border border-border/60 shadow-sm rounded-xl group hover:shadow-md hover:border-primary/30 transition-all duration-300 cursor-pointer"
                            @click="addToCart(menu)">
                            
                            <!-- Image / Gradient Placeholder -->
                            <div class="aspect-[4/3] relative overflow-hidden">
                                <img v-if="menu.image" :src="menu.image" :alt="menu.name"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                
                                <div v-else class="w-full h-full flex flex-col items-center justify-center p-4 transition-transform duration-500 group-hover:scale-105"
                                    :class="getGradientClass(menu.id)">
                                    <UtensilsCrossed class="w-10 h-10 text-white/80 mb-2 drop-shadow-sm" />
                                    <!-- Optional: Show short name on placeholder if desired -->
                                </div>

                                <!-- Quantity Badge -->
                                <div v-if="getCartQuantity(menu.id) > 0"
                                    class="absolute top-2 right-2 min-w-[1.5rem] h-6 px-1.5 rounded-full bg-primary text-primary-foreground text-xs font-bold flex items-center justify-center shadow-lg animate-in zoom-in border border-white/20">
                                    {{ getCartQuantity(menu.id) }}
                                </div>
                                
                                <!-- Add Overlay on Hover (Desktop) -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-[1px]">
                                    <div class="bg-white/20 backdrop-blur-md rounded-full p-2 text-white">
                                        <Plus class="w-6 h-6" />
                                    </div>
                                </div>
                            </div>

                            <CardContent class="p-4">
                                <h3 class="font-medium text-foreground line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                                    {{ menu.name }}
                                </h3>
                                <div class="flex items-center justify-between mt-auto">
                                    <p class="text-base font-bold text-foreground">
                                        Rp {{ formatPrice(menu.price) }}
                                    </p>
                                    <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-primary-foreground transition-colors">
                                        <Plus class="w-4 h-4" />
                                    </div>
                                </div>
                            </CardContent>
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
                            class="w-16 h-16 rounded-full bg-slate-100 dark:bg-zinc-800/50 flex items-center justify-center mb-4">
                            <ListOrdered class="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 class="font-medium text-slate-600 dark:text-zinc-300 mb-1">Tidak ada pesanan aktif</h3>
                        <p class="text-sm text-slate-500">Buat pesanan baru dari tab Menu</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Card v-for="order in activeOrders" :key="order.id"
                            class="cursor-pointer bg-card border-border/60 hover:border-primary/50 transition-all duration-300 hover:shadow-md group relative overflow-hidden"
                            @click="selectOrder(order)">
                            <!-- Decoration Line -->
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-400 to-amber-500"></div>

                            <CardHeader class="pb-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                            <User class="w-4 h-4" />
                                        </div>
                                        <span class="font-bold text-lg text-card-foreground">
                                            {{ order.customer_name }}
                                        </span>
                                    </div>
                                    <Badge variant="outline"
                                        class="bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800">
                                        Belum Bayar
                                    </Badge>
                                </div>
                            </CardHeader>

                            <CardContent class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-muted-foreground">{{ order.items.length }} item</span>
                                    <div class="flex items-center gap-1 text-muted-foreground">
                                        <Clock class="w-3 h-3" />
                                        {{ formatTimeAgo(order.created_at) }}
                                    </div>
                                </div>

                                <div class="p-3 bg-muted/30 rounded-lg flex justify-between items-center">
                                    <span class="text-sm font-medium text-muted-foreground">Total</span>
                                    <div class="font-bold text-xl text-primary">
                                        Rp {{ formatPrice(order.total_amount) }}
                                    </div>
                                </div>
                            </CardContent>

                            <CardFooter class="pt-0">
                                <Button size="sm" variant="outline"
                                    class="w-full bg-transparent hover:bg-primary hover:text-primary-foreground hover:border-primary transition-colors">
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
                    class="h-[85vh] flex flex-col rounded-t-3xl border-t border-border overflow-hidden p-0 shadow-2xl"
                    :style="{ backgroundColor: 'hsl(var(--card))' }">
                    
                    <!-- Decorative Top Bar (Slate) -->
                    <div class="h-2 w-full bg-slate-200 dark:bg-slate-800 flex-shrink-0"></div>

                    <!-- Header -->
                    <SheetHeader class="border-b border-border/60 p-5 bg-muted/10 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <SheetTitle class="text-2xl font-bold text-foreground">
                                    {{ isEditingOrder ? `Pesanan: ${customerName}` : 'Pesanan Baru' }}
                                </SheetTitle>
                                <p v-if="isEditingOrder && hasNewItems" class="text-sm text-primary font-medium mt-1 flex items-center gap-1">
                                    <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                                    {{ newItemsOnly.length }} item baru ditambahkan
                                </p>
                            </div>
                            <Button variant="ghost" size="icon" class="rounded-full hover:bg-destructive/10 hover:text-destructive transition-colors" @click="isCartOpen = false">
                                <X class="w-6 h-6" />
                            </Button>
                        </div>
                    </SheetHeader>

                    <!-- Scrollable Content Area -->
                    <div class="flex-1 overflow-y-auto p-5 space-y-4">
                        <!-- Empty State for New Orders -->
                        <div v-if="isCartEmpty && !isEditingOrder"
                            class="flex flex-col items-center justify-center h-full text-center py-12">
                            <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-6 animate-in zoom-in duration-300">
                                <ShoppingCart class="w-12 h-12 text-slate-400" />
                            </div>
                            <h3 class="text-xl font-bold text-foreground mb-2">Keranjang Kosong</h3>
                            <p class="text-muted-foreground max-w-xs mx-auto">
                                Belum ada menu yang dipilih. Silakan pilih menu untuk memulai pesanan.
                            </p>
                            <Button variant="outline" class="mt-6 border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800" @click="isCartOpen = false">
                                Kembali ke Menu
                            </Button>
                        </div>

                        <!-- Existing Items (with staged deletion) -->
                        <template v-if="existingItemsOnly.length > 0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="h-4 w-1 bg-slate-400 rounded-full"></div>
                                <div class="text-sm font-bold text-foreground uppercase tracking-wider">
                                    Item Tersimpan
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div v-for="(item, index) in cart" :key="`existing-${item.menu.id}`"
                                    v-show="item.isExisting" :class="[
                                        'flex items-center gap-4 p-4 rounded-2xl border transition-all duration-300',
                                        isMarkedForDeletion(item.itemId)
                                            ? 'bg-destructive/5 border-destructive/20 opacity-70'
                                            : 'bg-card border-border/60 shadow-sm hover:border-slate-300 dark:hover:border-slate-600'
                                    ]">
                                    <!-- Image / Placeholder -->
                                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 shadow-sm">
                                        <img v-if="item.menu.image" :src="item.menu.image" :alt="item.menu.name"
                                            class="w-full h-full object-cover" />
                                        <div v-else class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                                            <UtensilsCrossed class="w-6 h-6 text-slate-400" />
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h4 :class="[
                                            'font-bold text-base mb-1 truncate',
                                            isMarkedForDeletion(item.itemId) ? 'line-through text-destructive' : 'text-foreground'
                                        ]">
                                            {{ item.menu.name }}
                                        </h4>
                                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                            <Badge variant="secondary" class="bg-muted px-2 py-0.5 h-6">
                                                {{ item.quantity }}x
                                            </Badge>
                                            <span>@ Rp {{ formatPrice(item.menu.price) }}</span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <div :class="[
                                            'font-bold text-base',
                                            isMarkedForDeletion(item.itemId) ? 'line-through text-destructive' : 'text-primary'
                                        ]">
                                            Rp {{ formatPrice(item.menu.price * item.quantity) }}
                                        </div>
                                    </div>

                                    <!-- Toggle Delete Mark Button -->
                                    <Button v-if="item.itemId" variant="ghost" size="icon" :class="[
                                        'rounded-full ml-2 shrink-0',
                                        isMarkedForDeletion(item.itemId)
                                            ? 'bg-green-100 text-green-600 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400'
                                            : 'hover:bg-destructive/10 hover:text-destructive text-muted-foreground'
                                    ]" :disabled="isVoiding" @click="toggleDeleteMark(item)">
                                        <RotateCcw v-if="isMarkedForDeletion(item.itemId)" class="w-5 h-5" />
                                        <Trash2 v-else class="w-5 h-5" />
                                    </Button>
                                </div>
                            </div>
                        </template>

                        <!-- New Items (editable) -->
                        <template v-if="newItemsOnly.length > 0">
                            <div class="flex items-center gap-2 mb-3 mt-6">
                                <div class="h-4 w-1 bg-slate-500 rounded-full"></div>
                                <div class="text-sm font-bold text-foreground uppercase tracking-wider">
                                    {{ isEditingOrder ? 'Item Baru (Tambahan)' : 'Akan Dipesan' }}
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div v-for="(item, index) in cart" :key="`new-${item.menu.id}`" v-show="!item.isExisting"
                                    class="flex items-center gap-4 p-4 bg-muted/30 border border-transparent hover:border-border/60 rounded-2xl transition-all">
                                    
                                    <!-- Image / Placeholder -->
                                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 shadow-sm relative group">
                                        <img v-if="item.menu.image" :src="item.menu.image" :alt="item.menu.name"
                                            class="w-full h-full object-cover" />
                                        <div v-else class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                                            <UtensilsCrossed class="w-6 h-6 text-slate-400" />
                                        </div>
                                        
                                        <!-- Remove Overlay -->
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer"
                                            @click="removeFromCart(index)">
                                            <Trash2 class="w-6 h-6 text-white" />
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-base text-foreground truncate mb-1">
                                            {{ item.menu.name }}
                                        </h4>
                                        <p class="text-sm font-semibold text-primary">
                                            Rp {{ formatPrice(item.menu.price * item.quantity) }}
                                        </p>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex items-center gap-3 bg-background rounded-full p-1 border border-border shadow-sm">
                                        <Button variant="ghost" size="icon-sm" class="h-8 w-8 rounded-full hover:bg-muted text-foreground"
                                            @click="decrementQuantity(index)">
                                            <Minus class="w-4 h-4" />
                                        </Button>
                                        <span class="w-8 text-center font-bold text-foreground text-sm">
                                            {{ item.quantity }}
                                        </span>
                                        <Button variant="ghost" size="icon-sm" class="h-8 w-8 rounded-full hover:bg-primary hover:text-primary-foreground text-primary transition-colors"
                                            @click="incrementQuantity(index)">
                                            <Plus class="w-4 h-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Customer Name (only for new orders) -->
                        <div v-if="!isEditingOrder && !isCartEmpty" class="pt-6 pb-2">
                            <div class="bg-card border border-border/80 rounded-2xl p-5 shadow-sm">
                                <label class="block text-sm font-bold text-foreground mb-3 flex items-center gap-2">
                                    <User class="w-4 h-4 text-primary" />
                                    Nama Pelanggan <span class="text-destructive">*</span>
                                </label>
                                <Input v-model="customerName" type="text" placeholder="Contoh: Meja 5 / Bpk. Budi"
                                    class="bg-muted/30 border-input focus:ring-primary focus:border-primary h-12 text-lg px-4" />
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Footer with Total & Actions -->
                    <div class="p-5 border-t border-border bg-card/95 backdrop-blur supports-[backdrop-filter]:bg-card/80 flex-shrink-0 space-y-4">
                        <!-- Total Section -->
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-sm text-muted-foreground font-medium mb-1">Total Pembayaran</p>
                                <div class="text-3xl font-extrabold bg-gradient-to-r from-primary to-orange-600 bg-clip-text text-transparent">
                                    Rp {{ formatPrice(cartTotal) }}
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-muted-foreground mb-1">{{ isEditingOrder ? 'Total Item Pesanan' : 'Total Item' }}</p>
                                <Badge variant="outline" class="text-base px-3 py-1 border-primary/20 bg-primary/5 text-primary">
                                    {{ cartItemsCount }} Item
                                </Badge>
                            </div>
                        </div>

                        <!-- Action Buttons Grid -->
                        <div class="space-y-3">
                            <!-- Commit Deletions Button (staged) -->
                            <Button v-if="itemsToDelete.length > 0"
                                class="w-full h-12 font-bold bg-red-600 hover:bg-red-700 text-white rounded-xl shadow-lg shadow-red-500/20"
                                :disabled="isVoiding" @click="commitDeletions">
                                <Trash2 class="w-5 h-5 mr-2" />
                                Batalkan {{ itemsToDelete.length }} Item Terpilih
                            </Button>

                            <!-- Save Add-ons Button -->
                            <Button v-if="isEditingOrder && hasNewItems"
                                class="w-full h-12 font-bold bg-gradient-to-r from-primary to-orange-600 text-primary-foreground hover:opacity-90 rounded-xl shadow-lg shadow-primary/20"
                                :disabled="isProcessing || itemsToDelete.length > 0" @click="addItemsToOrder">
                                <Plus class="w-5 h-5 mr-2" />
                                Simpan Tambahan ({{ newItemsOnly.length }} Item)
                            </Button>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Add More Button -->
                                <Button
                                    class="h-12 font-bold bg-transparent border-2 border-border text-foreground hover:bg-muted/50 hover:border-primary/30 rounded-xl transition-all"
                                    :disabled="itemsToDelete.length > 0" @click="addMoreItems">
                                    <Plus class="w-5 h-5 mr-2" />
                                    Tambah Lagi
                                </Button>

                                <!-- Pay/Process Button -->
                                <Button v-if="isEditingOrder"
                                    class="h-12 font-bold bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/20 hover:opacity-90 rounded-xl transition-all"
                                    :disabled="isProcessing || hasNewItems || itemsToDelete.length > 0"
                                    @click="openCheckout">
                                    <CreditCard class="w-5 h-5 mr-2" />
                                    Bayar Sekarang
                                </Button>

                                <Button v-else
                                    class="h-12 font-bold bg-gradient-to-r from-primary to-orange-600 text-primary-foreground shadow-lg shadow-primary/25 hover:opacity-90 rounded-xl transition-all"
                                    :disabled="isCartEmpty || !customerName.trim() || isProcessing" @click="processOrder">
                                    <ShoppingCart class="w-5 h-5 mr-2" />
                                    Proses Pesanan
                                </Button>
                            </div>
                        </div>
                    </div>
                </SheetContent>
            </Sheet>

            <CheckoutDialog v-if="selectedTransaction" :open="isCheckoutOpen" :transaction="selectedTransaction"
                @update:open="isCheckoutOpen = $event" @success="onCheckoutSuccess" />

        </div>
    </AppLayout>
</template>
