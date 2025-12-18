<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    DollarSign,
    ShoppingBag,
    Wallet,
    QrCode,
    TrendingUp,
    Trophy,
    Store,
    Smile,
    ArrowUpRight,
    Clock,
    CreditCard,
    Calendar,
    ChevronRight,
    Utensils
} from 'lucide-vue-next';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    BarController,
} from 'chart.js';

// Layout
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Shadcn UI Components
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, BarElement, BarController, Title, Tooltip, Legend);

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
];

// Types
interface TopItem {
    name: string;
    total_qty: number;
    total_revenue: number;
}

interface Stats {
    today_revenue: number;
    today_count: number;
    active_orders: number;
    cash: { count: number; total: number };
    qris: { count: number; total: number };
    transfer: { count: number; total: number };
}

interface ChartData {
    labels: string[];
    data: number[];
}

interface Transaction {
    id: number;
    invoice_number: string;
    customer_name: string;
    total_amount: number;
    payment_method: string;
    status: string;
    created_at: string;
    user?: { name: string };
    branch?: { name: string };
}

// Props
const props = defineProps<{
    stats: Stats | null;
    chart: ChartData | null;
    top_items: TopItem[] | null;
    recent_transactions: Transaction[] | null;
}>();

// Current user
const page = usePage();
const user = computed(() => {
    const auth = page.props.auth as unknown as { user: { name: string; role: string } } | undefined;
    return auth?.user;
});
const isAdmin = computed(() => user.value?.role === 'admin');

// Methods
const formatPrice = (price: number): string => {
    if (price >= 1000000000) {
        return `${(price / 1000000000).toFixed(1)}M`;
    }
    if (price >= 1000000) {
        return `${(price / 1000000).toFixed(1)}jt`;
    }
    if (price >= 1000) {
        return `${(price / 1000).toFixed(0)}rb`;
    }
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatFullPrice = (price: number): string => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};

// Chart config
const chartData = computed(() => {
    if (!props.chart) return { labels: [], datasets: [] };
    return {
        labels: props.chart.labels,
        datasets: [
            {
                label: 'Pendapatan',
                backgroundColor: '#f59e0b', // Amber 500
                borderRadius: 6,
                hoverBackgroundColor: '#d97706', // Amber 600
                data: props.chart.data,
                barThickness: 40,
            },
        ],
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            callbacks: {
                label: (context: any) => `Rp ${formatFullPrice(context.raw)}`,
            },
            backgroundColor: 'hsl(var(--card))',
            titleColor: 'hsl(var(--foreground))',
            bodyColor: 'hsl(var(--muted-foreground))',
            borderColor: 'hsl(var(--border))',
            borderWidth: 1,
            padding: 12,
            titleFont: { size: 13, weight: 'bold' as const },
            bodyFont: { size: 12 },
            displayColors: false,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: string | number) => formatPrice(Number(value)),
                color: 'hsl(var(--muted-foreground))',
                font: { size: 11 },
                padding: 10,
            },
            grid: {
                color: 'hsl(var(--border) / 0.3)',
                drawBorder: false,
            },
            border: { display: false }
        },
        x: {
            ticks: {
                color: 'hsl(var(--muted-foreground))',
                font: { size: 11 },
            },
            grid: {
                display: false,
            }
        }
    },
};

const currentDate = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-7xl mx-auto p-6 space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Dashboard
                    </h1>
                    <p class="text-muted-foreground mt-1 flex items-center gap-2">
                        <Calendar class="w-4 h-4" />
                        {{ currentDate }}
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <Link :href="route('pos.index')">
                        <Button size="lg" class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white shadow-lg shadow-orange-500/20 border-0 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <Store class="w-5 h-5 mr-2" />
                            Buka Aplikasi Kasir
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Admin Dashboard -->
            <template v-if="isAdmin && stats">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Revenue Card -->
                    <div class="relative overflow-hidden rounded-2xl p-6 bg-card border border-border/60 shadow-sm hover:shadow-md transition-all group">
                        <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.06] transition-opacity">
                            <DollarSign class="w-24 h-24 text-primary" />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <div class="p-2 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400">
                                    <DollarSign class="w-4 h-4" />
                                </div>
                                Omset Hari Ini
                            </p>
                            <h3 class="text-3xl font-bold text-foreground mt-4 tracking-tight">
                                Rp {{ formatFullPrice(stats.today_revenue) }}
                            </h3>
                            <div class="flex items-center gap-1 mt-2 text-xs text-orange-600 dark:text-orange-400 font-medium">
                                <ArrowUpRight class="w-3 h-3" />
                                <span>Total kotor</span>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Card -->
                    <div class="relative overflow-hidden rounded-2xl p-6 bg-card border border-border/60 shadow-sm hover:shadow-md transition-all group">
                        <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.06] transition-opacity">
                            <ShoppingBag class="w-24 h-24 text-blue-500" />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <div class="p-2 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    <ShoppingBag class="w-4 h-4" />
                                </div>
                                Total Pesanan
                            </p>
                            <h3 class="text-3xl font-bold text-foreground mt-4 tracking-tight">
                                {{ stats.today_count }}
                            </h3>
                            <div class="flex items-center gap-1 mt-2 text-xs text-blue-600 dark:text-blue-400 font-medium">
                                <Clock class="w-3 h-3" />
                                <span>{{ stats.active_orders }} belum bayar</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Card -->
                    <div class="relative overflow-hidden rounded-2xl p-6 bg-card border border-border/60 shadow-sm hover:shadow-md transition-all group">
                        <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.06] transition-opacity">
                            <Wallet class="w-24 h-24 text-emerald-500" />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                    <Wallet class="w-4 h-4" />
                                </div>
                                Tunai
                            </p>
                            <h3 class="text-3xl font-bold text-foreground mt-4 tracking-tight">
                                {{ stats.cash.count }}
                            </h3>
                            <div class="flex items-center gap-1 mt-2 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                <span>Rp {{ formatPrice(stats.cash.total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- QRIS Card -->
                    <div class="relative overflow-hidden rounded-2xl p-6 bg-card border border-border/60 shadow-sm hover:shadow-md transition-all group">
                        <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.06] transition-opacity">
                            <QrCode class="w-24 h-24 text-purple-500" />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <div class="p-2 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                    <QrCode class="w-4 h-4" />
                                </div>
                                QRIS
                            </p>
                            <h3 class="text-3xl font-bold text-foreground mt-4 tracking-tight">
                                {{ stats.qris.count }}
                            </h3>
                            <div class="flex items-center gap-1 mt-2 text-xs text-purple-600 dark:text-purple-400 font-medium">
                                <span>Rp {{ formatPrice(stats.qris.total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts & Top Items Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Revenue Chart -->
                    <Card class="lg:col-span-2 shadow-sm border-border/60 bg-card hover:shadow-md transition-all">
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div class="space-y-1">
                                    <CardTitle class="text-lg font-bold flex items-center gap-2">
                                        <TrendingUp class="w-5 h-5 text-orange-500" />
                                        Tren Penjualan Mingguan
                                    </CardTitle>
                                    <CardDescription>
                                        Grafik pendapatan dalam 7 hari terakhir
                                    </CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="h-[300px] w-full">
                                <Bar :data="chartData" :options="chartOptions" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Top Items -->
                    <Card class="shadow-sm border-border/60 bg-card hover:shadow-md transition-all">
                        <CardHeader>
                            <div class="space-y-1">
                                <CardTitle class="text-lg font-bold flex items-center gap-2">
                                    <Trophy class="w-5 h-5 text-yellow-500 hover:text-yellow-600 transition-colors" />
                                    Menu Terlaris
                                </CardTitle>
                                <CardDescription>
                                    5 menu dengan penjualan tertinggi
                                </CardDescription>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-if="!top_items || top_items.length === 0" class="text-center py-12">
                                    <div class="bg-muted/50 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                                        <Utensils class="w-6 h-6 text-muted-foreground" />
                                    </div>
                                    <p class="text-sm text-muted-foreground">Belum ada data penjualan</p>
                                </div>
                                
                                <div v-for="(item, index) in top_items" :key="item.name"
                                    class="group flex items-center gap-4 p-3 rounded-xl hover:bg-muted/50 transition-all cursor-default border border-transparent hover:border-border/50">
                                    <div class="flex-shrink-0 relative">
                                        <div 
                                            class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm"
                                            :class="{
                                                'bg-yellow-100 text-yellow-700 ring-2 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-800': index === 0,
                                                'bg-zinc-100 text-zinc-600 ring-1 ring-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:ring-zinc-700': index > 0
                                            }"
                                        >
                                            {{ index + 1 }}
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-sm text-foreground truncate group-hover:text-primary transition-colors">
                                            {{ item.name }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <Badge variant="secondary" class="h-5 px-1.5 text-[10px] bg-muted text-muted-foreground">
                                                {{ item.total_qty }} terjual
                                            </Badge>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-foreground">
                                            {{ formatPrice(item.total_revenue) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Recent Transactions -->
                 <Card class="shadow-sm border-border/60 bg-card hover:shadow-md transition-all overflow-hidden">
                    <CardHeader class="border-b border-border/50 bg-muted/20">
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <CardTitle class="text-lg font-bold flex items-center gap-2">
                                    <CreditCard class="w-5 h-5 text-blue-500" />
                                    Transaksi Terakhir
                                </CardTitle>
                                <CardDescription>
                                    5 transaksi terbaru yang berhasil
                                </CardDescription>
                            </div>
                            <Link :href="route('reports.index')" class="text-sm font-medium text-primary hover:text-primary/80 flex items-center gap-1 transition-colors">
                                Lihat Semua <ChevronRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </CardHeader>
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow class="hover:bg-transparent">
                                    <TableHead>Invoice</TableHead>
                                    <TableHead>Waktu</TableHead>
                                    <TableHead>Pelanggan</TableHead>
                                    <TableHead>Kasir</TableHead>
                                    <TableHead>Metode</TableHead>
                                    <TableHead class="text-right">Total</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="!recent_transactions || recent_transactions.length === 0">
                                    <TableCell colspan="6" class="text-center py-12 text-muted-foreground">
                                        Belum ada transaksi hari ini
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="trx in recent_transactions" :key="trx.id" class="cursor-default hover:bg-muted/50">
                                    <TableCell class="font-mono text-xs font-medium">
                                        {{ trx.invoice_number }}
                                    </TableCell>
                                    <TableCell class="text-muted-foreground text-xs">
                                        {{ formatDate(trx.created_at) }}
                                    </TableCell>
                                    <TableCell class="font-medium">
                                        {{ trx.customer_name || 'Umum' }}
                                    </TableCell>
                                    <TableCell class="text-muted-foreground">
                                        {{ trx.user?.name || '-' }}
                                    </TableCell>
                                    <TableCell>
                                        <Badge variant="outline" class="capitalize">
                                            {{ trx.payment_method }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right font-bold">
                                        Rp {{ formatFullPrice(trx.total_amount) }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </Card>

            </template>

            <!-- Cashier Dashboard -->
            <template v-else>
                <div class="flex flex-col items-center justify-center min-h-[60vh] gap-8">
                    <Card class="max-w-xl w-full bg-gradient-to-b from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 border border-border shadow-2xl relative overflow-hidden">
                        <!-- Decorative background elements -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                        <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-500/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
                        
                        <CardContent class="py-16 text-center relative z-10">
                            <div class="w-24 h-24 mx-auto mb-8 bg-gradient-to-br from-orange-100 to-amber-100 dark:from-orange-900/30 dark:to-amber-900/30 rounded-3xl rotate-3 flex items-center justify-center shadow-inner ring-1 ring-orange-200 dark:ring-orange-800">
                                <Store class="w-12 h-12 text-orange-600 dark:text-orange-400 -rotate-3" />
                            </div>
                            
                            <h1 class="text-3xl font-bold mb-3 text-foreground tracking-tight">
                                Halo, <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-amber-600">{{ user?.name }}</span>! 👋
                            </h1>
                            <p class="text-muted-foreground mb-10 text-lg leading-relaxed max-w-sm mx-auto">
                                Siap untuk mencatat transaksi hari ini? Pastikan saldo awal sudah sesuai.
                            </p>
                            
                            <div class="space-y-4">
                                <Link :href="route('pos.index')">
                                    <Button size="lg" class="w-full sm:w-auto min-w-[200px] h-14 text-lg bg-primary hover:bg-primary/90 shadow-xl shadow-primary/20 rounded-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                                        <Store class="w-6 h-6 mr-2.5" />
                                        Buka Kasir
                                    </Button>
                                </Link>
                                
                                <p class="text-xs text-muted-foreground pt-4">
                                    Tekan tombol di atas untuk masuk ke mode POS
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
