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
    qris: 'bg-purple-100 text-purple-800 border-purple-300',
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
        store_name: 'Mie Lethek Palur',
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
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Riwayat Transaksi</h1>
                <p class="text-sm text-slate-500">Lihat dan cetak ulang transaksi yang sudah selesai</p>
            </div>

            <!-- Smart Filter Bar -->
            <div
                class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-zinc-100 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <!-- Preset Dropdown -->
                <div class="relative">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">Filter
                        Cepat</label>
                    <div class="relative">
                        <select v-model="presetFilter"
                            class="w-full h-10 pl-3 pr-10 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 appearance-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            <option value="hari_ini">Hari Ini</option>
                            <option value="kemarin">Kemarin</option>
                            <option value="7_hari">7 Hari Terakhir</option>
                            <option value="bulan_ini">Bulan Ini</option>
                            <option value="custom">Custom</option>
                        </select>
                        <ChevronDown
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400 pointer-events-none" />
                    </div>
                </div>

                <!-- Start Date -->
                <div class="relative">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">Dari
                        Tanggal</label>
                    <div class="relative">
                        <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
                        <input v-model="startDate" type="date" :disabled="presetFilter !== 'custom'"
                            class="w-full h-10 pl-10 pr-3 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors disabled:opacity-60 disabled:cursor-not-allowed" />
                    </div>
                </div>

                <!-- End Date -->
                <div class="relative">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">Sampai
                        Tanggal</label>
                    <div class="relative">
                        <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
                        <input v-model="endDate" type="date" :disabled="presetFilter !== 'custom'"
                            class="w-full h-10 pl-10 pr-3 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors disabled:opacity-60 disabled:cursor-not-allowed" />
                    </div>
                </div>

                <!-- Search Input -->
                <div class="relative">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">Cari
                        Pelanggan</label>
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
                        <input v-model="searchFilter" type="text" placeholder="Nama pelanggan..."
                            class="w-full h-10 pl-10 pr-3 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors" />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-16">#</TableHead>
                                <TableHead>Waktu</TableHead>
                                <TableHead>Pelanggan</TableHead>
                                <TableHead>Kasir</TableHead>
                                <TableHead>Metode</TableHead>
                                <TableHead class="text-right">Total</TableHead>
                                <TableHead class="text-right w-28">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="transactions.data.length === 0">
                                <TableCell colspan="7" class="text-center py-12">
                                    <p class="text-slate-500">Tidak ada transaksi pada rentang tanggal ini</p>
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="tx in transactions.data" :key="tx.id">
                                <TableCell class="font-mono text-sm text-slate-500">
                                    {{ tx.uuid ? tx.uuid.substring(0, 8) : tx.id }}
                                </TableCell>
                                <TableCell class="text-sm">
                                    {{ formatDateTime(tx.created_at) }}
                                </TableCell>
                                <TableCell class="font-medium">
                                    {{ tx.customer_name }}
                                </TableCell>
                                <TableCell class="text-sm text-slate-500">
                                    {{ tx.user?.name || '-' }}
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline" :class="paymentMethodColors[tx.payment_method]">
                                        {{ paymentMethodLabels[tx.payment_method] || tx.payment_method }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right font-medium">
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
                    class="flex items-center justify-between px-4 py-3 border-t border-slate-200 dark:border-slate-800">
                    <p class="text-sm text-slate-500">
                        Menampilkan {{ transactions.data.length }} dari {{ transactions.total }} transaksi
                    </p>
                    <div class="flex gap-1">
                        <Button v-for="link in transactions.links" :key="link.label" variant="outline" size="sm"
                            :disabled="!link.url || link.active"
                            :class="{ 'bg-orange-500 text-white border-orange-500': link.active }"
                            @click="goToPage(link.url)" v-html="link.label" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
