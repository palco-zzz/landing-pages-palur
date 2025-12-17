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
                backgroundColor: '#f59e0b', // Amber 500
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
            backgroundColor: 'hsl(var(--card))',
            titleColor: 'hsl(var(--card-foreground))',
            bodyColor: 'hsl(var(--card-foreground))',
            borderColor: 'hsl(var(--border))',
            borderWidth: 1,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: string | number) => formatPrice(Number(value)),
                color: '#71717a', // Zinc 500
            },
            grid: {
                color: 'hsl(var(--border) / 0.5)',
            }
        },
        x: {
            ticks: {
                color: '#71717a', // Zinc 500
            },
            grid: {
                display: false,
            }
        }
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
                        <h1 class="text-2xl font-bold text-foreground">
                            Selamat Datang, <span class="text-primary">{{ user?.name }}</span>! 👋
                        </h1>
                        <p class="text-sm text-muted-foreground">Ringkasan bisnis hari ini</p>
                    </div>
                    <Link :href="route('pos.index')">
                        <Button class="bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm border-0">
                            <Store class="w-4 h-4 mr-2" />
                            Buka POS
                        </Button>
                    </Link>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Today Revenue -->
                    <Card class="bg-card text-card-foreground border border-border shadow-sm rounded-xl p-6">
                        <CardHeader class="p-0 pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium text-muted-foreground">Omset Hari Ini</CardTitle>
                                <DollarSign class="w-5 h-5 text-muted-foreground" />
                            </div>
                        </CardHeader>
                        <CardContent class="p-0">
                            <p class="text-3xl font-bold text-foreground">Rp {{ formatFullPrice(stats.today_revenue) }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Today Count -->
                    <Card class="bg-card text-card-foreground border border-border shadow-sm rounded-xl p-6">
                        <CardHeader class="p-0 pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium text-muted-foreground">Total Pesanan</CardTitle>
                                <ShoppingBag class="w-5 h-5 text-muted-foreground" />
                            </div>
                        </CardHeader>
                        <CardContent class="p-0">
                            <p class="text-3xl font-bold text-foreground">
                                {{ stats.today_count }}
                            </p>
                            <p class="text-xs text-muted-foreground mt-1">
                                {{ stats.active_orders }} pesanan aktif
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Cash Stats -->
                    <Card class="bg-card text-card-foreground border border-border shadow-sm rounded-xl p-6">
                        <CardHeader class="p-0 pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium text-muted-foreground">Tunai</CardTitle>
                                <Wallet class="w-5 h-5 text-muted-foreground" />
                            </div>
                        </CardHeader>
                        <CardContent class="p-0">
                            <p class="text-3xl font-bold text-foreground">{{ stats.cash.count }}</p>
                            <p class="text-xs text-muted-foreground mt-1">
                                Rp {{ formatPrice(stats.cash.total) }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- QRIS Stats -->
                    <Card class="bg-card text-card-foreground border border-border shadow-sm rounded-xl p-6">
                        <CardHeader class="p-0 pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium text-muted-foreground">QRIS</CardTitle>
                                <QrCode class="w-5 h-5 text-muted-foreground" />
                            </div>
                        </CardHeader>
                        <CardContent class="p-0">
                            <p class="text-3xl font-bold text-foreground">{{ stats.qris.count }}</p>
                            <p class="text-xs text-muted-foreground mt-1">
                                Rp {{ formatPrice(stats.qris.total) }}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Revenue Chart -->
                    <Card class="lg:col-span-2 bg-card text-card-foreground border border-border shadow-sm rounded-xl">
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <TrendingUp class="w-5 h-5 text-primary" />
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
                    <Card class="bg-card text-card-foreground border border-border shadow-sm rounded-xl">
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <Trophy class="w-5 h-5 text-amber-500" />
                                <CardTitle>5 Menu Terlaris</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div v-if="!top_items || top_items.length === 0" class="text-center py-8">
                                <p class="text-muted-foreground">Belum ada data</p>
                            </div>
                            <div v-for="(item, index) in top_items" :key="item.name"
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-muted/50 transition-colors">
                                <Badge variant="outline" :class="{
                                    'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800': index === 0,
                                    'bg-muted text-muted-foreground border-border': index > 0,
                                }">
                                    #{{ index + 1 }}
                                </Badge>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-foreground truncate">
                                        {{ item.name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ item.total_qty }} terjual
                                    </p>
                                </div>
                                <p class="text-sm font-medium text-primary">
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
                    <Card
                        class="max-w-lg w-full bg-card text-card-foreground border border-border shadow-sm rounded-xl">
                        <CardContent class="py-12 text-center">
                            <div
                                class="w-20 h-20 mx-auto mb-6 bg-primary/10 rounded-full flex items-center justify-center">
                                <Smile class="w-10 h-10 text-primary" />
                            </div>
                            <h1 class="text-3xl font-bold mb-2 text-foreground">
                                Selamat Datang, <span class="text-primary">{{ user?.name }}</span>! 👋
                            </h1>
                            <p class="text-muted-foreground mb-8 text-lg">
                                Selamat bekerja! Silakan masuk ke menu POS untuk memulai transaksi.
                            </p>
                            <Link :href="route('pos.index')">
                                <Button size="lg"
                                    class="bg-primary text-primary-foreground hover:bg-primary/90 text-lg px-8 py-6 shadow-sm border-0">
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
