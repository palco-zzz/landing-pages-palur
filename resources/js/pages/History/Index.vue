<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Search, Printer, Calendar } from 'lucide-vue-next';
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
        date: string;
        search: string;
    };
}>();

// Printer
const { print } = usePrinter();

// Filter state
const dateFilter = ref(props.filters.date);
const searchFilter = ref(props.filters.search);

// Methods
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
        date: dateFilter.value,
        search: searchFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debouncedSearch = useDebounceFn(() => {
    applyFilters();
}, 500);

// Watch for date changes (immediate)
watch(dateFilter, () => {
    applyFilters();
});

// Watch for search changes (debounced)
watch(searchFilter, () => {
    debouncedSearch();
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
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Riwayat Transaksi</h1>
                <p class="text-sm text-slate-500">Lihat dan cetak ulang transaksi yang sudah selesai</p>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <div class="relative flex-1 sm:max-w-xs">
                    <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <Input v-model="dateFilter" type="date" class="pl-10" />
                </div>
                <div class="relative flex-1 sm:max-w-sm">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <Input v-model="searchFilter" type="text" placeholder="Cari nama pelanggan..." class="pl-10" />
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
                                    <p class="text-slate-500">Tidak ada transaksi pada tanggal ini</p>
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
