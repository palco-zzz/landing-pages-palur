<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Search, Printer, Calendar, ChevronDown } from 'lucide-vue-next';
import { usePrinter, type ReceiptData } from '@/composables/usePrinter';
import { useDebounceFn } from '@vueuse/core';

// Layout
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Riwayat Transaksi', href: route('history.index') },
];

// Types
interface Menu {
    id: number;
    name: string;
    price: number;
}

interface TransactionItem {
    id: number;
    menu: Menu;
    quantity: number;
    price: number;
    subtotal: number;
}

interface User {
    id: number;
    name: string;
}

interface Transaction {
    id: number;
    uuid: string;
    customer_name: string;
    total_amount: number;
    status: string;
    payment_method: string;
    items: TransactionItem[];
    user: User | null;
    created_at: string;
}

interface PaginatedData {
    data: Transaction[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

// Props
const props = defineProps<{
    transactions: PaginatedData;
    filters: {
        start_date: string;
        end_date: string;
        search: string;
    };
}>();

// Printer
const { print } = usePrinter();

// --- Helper Functions for Date Calculations ---
const formatDate = (date: Date): string => {
    return date.toISOString().split('T')[0];
};

const getToday = () => formatDate(new Date());

const getYesterday = () => {
    const d = new Date();
    d.setDate(d.getDate() - 1);
    return formatDate(d);
};

const getLast7Days = () => {
    const d = new Date();
    d.setDate(d.getDate() - 7);
    return formatDate(d);
};

const getFirstDayOfMonth = () => {
    const d = new Date();
    return formatDate(new Date(d.getFullYear(), d.getMonth(), 1));
};

// --- Filter State ---
const presetFilter = ref('hari_ini');
const startDate = ref(props.filters.start_date);
const endDate = ref(props.filters.end_date);
const searchFilter = ref(props.filters.search);

// Detect initial preset based on incoming filter values
const detectInitialPreset = () => {
    const today = getToday();
    const yesterday = getYesterday();
    const last7 = getLast7Days();
    const firstOfMonth = getFirstDayOfMonth();

    if (startDate.value === today && endDate.value === today) {
        return 'hari_ini';
    } else if (startDate.value === yesterday && endDate.value === yesterday) {
        return 'kemarin';
    } else if (startDate.value === last7 && endDate.value === today) {
        return '7_hari';
    } else if (startDate.value === firstOfMonth && endDate.value === today) {
        return 'bulan_ini';
    } else {
        return 'custom';
    }
};

presetFilter.value = detectInitialPreset();

// --- Methods ---
const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatDateTime = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const paymentMethodLabels: Record<string, string> = {
    cash: 'Tunai',
    qris: 'QRIS',
    transfer: 'Transfer',
};

const paymentMethodColors: Record<string, string> = {
    cash: 'bg-green-100 text-green-800 border-green-300',
    qris: 'bg-primary/10 text-primary border-primary/20',
    transfer: 'bg-blue-100 text-blue-800 border-blue-300',
};

const applyFilters = () => {
    router.get(route('history.index'), {
        start_date: startDate.value,
        end_date: endDate.value,
        search: searchFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debouncedApplyFilters = useDebounceFn(() => {
    applyFilters();
}, 500);

// --- Watchers ---

// Watch preset dropdown
watch(presetFilter, (newPreset) => {
    const today = getToday();

    switch (newPreset) {
        case 'hari_ini':
            startDate.value = today;
            endDate.value = today;
            break;
        case 'kemarin':
            startDate.value = getYesterday();
            endDate.value = getYesterday();
            break;
        case '7_hari':
            startDate.value = getLast7Days();
            endDate.value = today;
            break;
        case 'bulan_ini':
            startDate.value = getFirstDayOfMonth();
            endDate.value = today;
            break;
        case 'custom':
            // Don't change dates, let user pick manually
            return;
    }
    // Immediately apply for presets
    applyFilters();
});

// Watch date inputs (for custom mode) with debounce
watch([startDate, endDate], () => {
    if (presetFilter.value === 'custom') {
        debouncedApplyFilters();
    }
});

// Watch search input with debounce
watch(searchFilter, () => {
    debouncedApplyFilters();
});

// Reprint receipt
const reprintReceipt = (transaction: Transaction) => {
    const receiptData: ReceiptData = {
        type: 'customer',
        title: 'STRUK PEMBAYARAN',
        subtitle: '(CETAK ULANG)',
        store_name: 'Bakmi Jowo Palur',
        date: formatDateTime(transaction.created_at),
        cashier: transaction.user?.name || 'Kasir',
        customer_name: transaction.customer_name,
        order_number: transaction.uuid ? transaction.uuid.substring(0, 8) : String(transaction.id),
        items: transaction.items.map(item => ({
            name: item.menu?.name || 'Item',
            qty: item.quantity,
            price: item.price,
            subtotal: item.subtotal,
        })),
        total: transaction.total_amount,
        payment_method: transaction.payment_method,
    };

    print(receiptData);
};

// Pagination
const goToPage = (url: string | null) => {
    if (url) {
        router.get(url, {}, { preserveState: true, preserveScroll: true });
    }
};
</script>

<template>

    <Head title="Riwayat Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-foreground">Riwayat Transaksi</h1>
                <p class="text-sm text-muted-foreground">Lihat dan cetak ulang transaksi yang sudah selesai</p>
            </div>

            <!-- Smart Filter Bar -->
            <div
                class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-5 bg-card rounded-xl border border-border shadow-sm">
                <!-- Preset Dropdown -->
                <div class="relative">
                    <label class="block text-sm font-medium text-muted-foreground mb-1.5">Filter Cepat</label>
                    <div class="relative">
                        <select v-model="presetFilter"
                            class="w-full h-10 pl-3 pr-10 bg-background border border-input text-foreground rounded-lg text-sm appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                            <option value="hari_ini">Hari Ini</option>
                            <option value="kemarin">Kemarin</option>
                            <option value="7_hari">7 Hari Terakhir</option>
                            <option value="bulan_ini">Bulan Ini</option>
                            <option value="custom">Custom</option>
                        </select>
                        <ChevronDown
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none" />
                    </div>
                </div>

                <!-- Start Date -->
                <div class="relative">
                    <label class="block text-sm font-medium text-muted-foreground mb-1.5">Dari Tanggal</label>
                    <div class="relative">
                        <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <input v-model="startDate" type="date" :disabled="presetFilter !== 'custom'"
                            class="w-full h-10 pl-10 pr-3 bg-background border border-input text-foreground rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors disabled:opacity-60 disabled:cursor-not-allowed" />
                    </div>
                </div>

                <!-- End Date -->
                <div class="relative">
                    <label class="block text-sm font-medium text-muted-foreground mb-1.5">Sampai Tanggal</label>
                    <div class="relative">
                        <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <input v-model="endDate" type="date" :disabled="presetFilter !== 'custom'"
                            class="w-full h-10 pl-10 pr-3 bg-background border border-input text-foreground rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors disabled:opacity-60 disabled:cursor-not-allowed" />
                    </div>
                </div>

                <!-- Search Input -->
                <div class="relative">
                    <label class="block text-sm font-medium text-muted-foreground mb-1.5">Cari Pelanggan</label>
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <input v-model="searchFilter" type="text" placeholder="Nama pelanggan..."
                            class="w-full h-10 pl-10 pr-3 bg-background border border-input text-foreground placeholder:text-muted-foreground rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm mt-6">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader class="bg-muted/40 border-b border-border">
                            <TableRow>
                                <TableHead
                                    class="w-16 text-xs font-semibold text-muted-foreground uppercase tracking-wider">#
                                </TableHead>
                                <TableHead class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                    Waktu</TableHead>
                                <TableHead class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                    Pelanggan</TableHead>
                                <TableHead class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                    Kasir</TableHead>
                                <TableHead class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                    Metode</TableHead>
                                <TableHead
                                    class="text-right text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                    Total</TableHead>
                                <TableHead
                                    class="text-right w-28 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                    Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="transactions.data.length === 0">
                                <TableCell colspan="7" class="text-center py-12">
                                    <p class="text-muted-foreground">Tidak ada transaksi pada rentang tanggal ini</p>
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="tx in transactions.data" :key="tx.id"
                                class="border-b border-border hover:bg-muted/20 transition-colors">
                                <TableCell class="font-mono text-sm text-muted-foreground">
                                    {{ tx.uuid ? tx.uuid.substring(0, 8) : tx.id }}
                                </TableCell>
                                <TableCell class="text-sm text-foreground">
                                    {{ formatDateTime(tx.created_at) }}
                                </TableCell>
                                <TableCell class="font-medium text-foreground">
                                    {{ tx.customer_name }}
                                </TableCell>
                                <TableCell class="text-sm text-muted-foreground">
                                    {{ tx.user?.name || '-' }}
                                </TableCell>
                                <TableCell>
                                    <span
                                        class="bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full px-2 py-0.5 text-xs font-medium">
                                        {{ paymentMethodLabels[tx.payment_method] || tx.payment_method }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-right font-medium text-foreground">
                                    Rp {{ formatPrice(tx.total_amount) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button variant="outline" size="sm" @click="reprintReceipt(tx)">
                                        <Printer class="w-4 h-4 mr-1" />
                                        Cetak
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Pagination -->
                <div v-if="transactions.last_page > 1"
                    class="flex items-center justify-between px-4 py-3 border-t border-border">
                    <p class="text-sm text-muted-foreground">
                        Menampilkan {{ transactions.data.length }} dari {{ transactions.total }} transaksi
                    </p>
                    <div class="flex gap-1">
                        <Button v-for="link in transactions.links" :key="link.label" variant="outline" size="sm"
                            :disabled="!link.url || link.active"
                            :class="{ 'bg-primary text-primary-foreground border-primary': link.active }"
                            @click="goToPage(link.url)" v-html="link.label" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
