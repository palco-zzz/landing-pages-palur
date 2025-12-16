<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { DollarSign, ShoppingBag, Wallet, QrCode, TrendingUp, Trophy, Store, Smile } from 'lucide-vue-next';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';

// Layout
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Shadcn UI Components
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

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

// Props
const props = defineProps<{
    stats: Stats | null;
    chart: ChartData | null;
    top_items: TopItem[] | null;
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

// Chart config
const chartData = computed(() => {
    if (!props.chart) return { labels: [], datasets: [] };
    return {
        labels: props.chart.labels,
        datasets: [
            {
                label: 'Pendapatan',
                backgroundColor: '#f97316',
                borderRadius: 8,
                data: props.chart.data,
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
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: string | number) => formatPrice(Number(value)),
            },
        },
    },
};
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <!-- Admin Dashboard -->
            <template v-if="isAdmin && stats">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                            Selamat Datang, {{ user?.name }}! 👋
                        </h1>
                        <p class="text-sm text-slate-500">Ringkasan bisnis hari ini</p>
                    </div>
                    <Link :href="route('pos.index')">
                        <Button class="bg-orange-500 hover:bg-orange-600">
                            <Store class="w-4 h-4 mr-2" />
                            Buka POS
                        </Button>
                    </Link>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Today Revenue -->
                    <Card class="bg-gradient-to-br from-orange-500 to-orange-600 text-white border-0">
                        <CardHeader class="pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium opacity-90">Omset Hari Ini</CardTitle>
                                <DollarSign class="w-5 h-5 opacity-75" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold">Rp {{ formatFullPrice(stats.today_revenue) }}</p>
                        </CardContent>
                    </Card>

                    <!-- Today Count -->
                    <Card>
                        <CardHeader class="pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium text-slate-500">Total Pesanan</CardTitle>
                                <ShoppingBag class="w-5 h-5 text-slate-400" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.today_count }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ stats.active_orders }} pesanan aktif
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Cash Stats -->
                    <Card>
                        <CardHeader class="pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium text-slate-500">Tunai</CardTitle>
                                <Wallet class="w-5 h-5 text-green-500" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.cash.count }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                Rp {{ formatPrice(stats.cash.total) }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- QRIS Stats -->
                    <Card>
                        <CardHeader class="pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium text-slate-500">QRIS</CardTitle>
                                <QrCode class="w-5 h-5 text-purple-500" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.qris.count }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                Rp {{ formatPrice(stats.qris.total) }}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Revenue Chart -->
                    <Card class="lg:col-span-2">
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <TrendingUp class="w-5 h-5 text-orange-500" />
                                <CardTitle>Tren Pendapatan Mingguan</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="h-64">
                                <Bar :data="chartData" :options="chartOptions" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Top Items -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <Trophy class="w-5 h-5 text-yellow-500" />
                                <CardTitle>5 Menu Terlaris</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div v-if="!top_items || top_items.length === 0" class="text-center py-8">
                                <p class="text-slate-500">Belum ada data</p>
                            </div>
                            <div v-for="(item, index) in top_items" :key="item.name"
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800">
                                <Badge variant="outline" :class="{
                                    'bg-yellow-100 text-yellow-800 border-yellow-300': index === 0,
                                    'bg-slate-100 text-slate-600 border-slate-300': index > 0,
                                }">
                                    #{{ index + 1 }}
                                </Badge>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-slate-900 dark:text-white truncate">
                                        {{ item.name }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ item.total_qty }} terjual
                                    </p>
                                </div>
                                <p class="text-sm font-medium text-orange-500">
                                    Rp {{ formatPrice(item.total_revenue) }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </template>

            <!-- Cashier Dashboard -->
            <template v-else>
                <div class="flex items-center justify-center min-h-[60vh]">
                    <Card class="max-w-lg w-full bg-gradient-to-br from-slate-900 to-slate-800 text-white border-0">
                        <CardContent class="py-12 text-center">
                            <div
                                class="w-20 h-20 mx-auto mb-6 bg-orange-500/20 rounded-full flex items-center justify-center">
                                <Smile class="w-10 h-10 text-orange-400" />
                            </div>
                            <h1 class="text-3xl font-bold mb-2">
                                Selamat Datang, {{ user?.name }}! 👋
                            </h1>
                            <p class="text-slate-300 mb-8 text-lg">
                                Selamat bekerja! Silakan masuk ke menu POS untuk memulai transaksi.
                            </p>
                            <Link :href="route('pos.index')">
                                <Button size="lg" class="bg-orange-500 hover:bg-orange-600 text-lg px-8 py-6">
                                    <Store class="w-6 h-6 mr-3" />
                                    Buka Aplikasi Kasir (POS)
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
