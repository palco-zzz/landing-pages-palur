<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { FileDown, FileSpreadsheet, TrendingUp, CreditCard, Star, BarChart3 } from 'lucide-vue-next';

// Chart.js
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';
import { Bar, Doughnut } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

// Layout & Components
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Label } from '@/components/ui/label';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Laporan', href: route('reports.index') },
];

// Props
const props = defineProps<{
    hourlyTrend: { hour: string; count: number }[];
    paymentMethods: { method: string; count: number; total: number }[];
    topMenus: { name: string; total_sold: number }[];
    bottomMenus: { name: string; total_sold: number }[];
    summary: { total_transactions: number; total_revenue: number; average_order: number };
    filters: { start_date: string; end_date: string };
}>();

// Local state for filters
const startDate = ref(props.filters.start_date);
const endDate = ref(props.filters.end_date);

// Apply filter
const applyFilter = () => {
    router.get(route('reports.index'), {
        start_date: startDate.value,
        end_date: endDate.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Export functions
const exportPdf = () => {
    window.open(route('reports.export-pdf', {
        start_date: startDate.value,
        end_date: endDate.value,
    }));
};

const exportExcel = () => {
    window.open(route('reports.export-excel', {
        start_date: startDate.value,
        end_date: endDate.value,
    }));
};

// Chart data - Hourly Trend (Bar Chart)
const hourlyChartData = computed(() => ({
    labels: props.hourlyTrend.map(h => h.hour),
    datasets: [
        {
            label: 'Transaksi',
            data: props.hourlyTrend.map(h => h.count),
            backgroundColor: 'rgba(249, 115, 22, 0.8)',
            borderColor: 'rgb(249, 115, 22)',
            borderWidth: 1,
            borderRadius: 4,
        },
    ],
}));

const hourlyChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        title: { display: false },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: { stepSize: 1 },
        },
    },
};

// Chart data - Payment Methods (Doughnut)
const paymentChartData = computed(() => ({
    labels: props.paymentMethods.map(p => p.method.toUpperCase()),
    datasets: [
        {
            data: props.paymentMethods.map(p => p.count),
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(168, 85, 247, 0.8)',
            ],
            borderColor: [
                'rgb(34, 197, 94)',
                'rgb(59, 130, 246)',
                'rgb(168, 85, 247)',
            ],
            borderWidth: 2,
        },
    ],
}));

const paymentChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
        },
    },
};

// Format price
const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('id-ID').format(price);
};
</script>

<template>

    <Head title="Laporan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <!-- Header with Filters & Export -->
            <div class="flex flex-col md:flex-row md:items-end gap-4 justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">
                        Laporan & Analitik
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1">
                        Pantau performa warung dengan data real-time
                    </p>
                </div>

                <div class="flex flex-wrap items-end gap-3">
                    <!-- Date Filters -->
                    <div class="flex items-center gap-2">
                        <div>
                            <Label for="start_date" class="text-xs">Dari</Label>
                            <Input id="start_date" type="date" v-model="startDate" class="w-36" />
                        </div>
                        <div>
                            <Label for="end_date" class="text-xs">Sampai</Label>
                            <Input id="end_date" type="date" v-model="endDate" class="w-36" />
                        </div>
                        <Button @click="applyFilter" class="mt-5">
                            Filter
                        </Button>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex gap-2">
                        <Button variant="destructive" @click="exportPdf">
                            <FileDown class="w-4 h-4 mr-2" />
                            PDF
                        </Button>
                        <Button class="bg-green-600 hover:bg-green-700 text-white" @click="exportExcel">
                            <FileSpreadsheet class="w-4 h-4 mr-2" />
                            Excel
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Transaksi</CardDescription>
                        <CardTitle class="text-3xl font-bold">
                            {{ summary.total_transactions }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-slate-500">Periode terpilih</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Pendapatan</CardDescription>
                        <CardTitle class="text-3xl font-bold text-green-600">
                            Rp {{ formatPrice(summary.total_revenue) }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-slate-500">Periode terpilih</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Rata-rata Pesanan</CardDescription>
                        <CardTitle class="text-3xl font-bold text-orange-600">
                            Rp {{ formatPrice(Math.round(summary.average_order)) }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-slate-500">Per transaksi</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Hourly Trend Chart -->
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <TrendingUp class="w-5 h-5 text-orange-500" />
                        <CardTitle>Tren Jam Sibuk</CardTitle>
                    </div>
                    <CardDescription>Jumlah transaksi per jam (00:00 - 23:00)</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="h-64 md:h-80">
                        <Bar :data="hourlyChartData" :options="hourlyChartOptions" />
                    </div>
                </CardContent>
            </Card>

            <!-- Payment Methods & Menu Performance -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Payment Methods -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <CreditCard class="w-5 h-5 text-blue-500" />
                            <CardTitle>Metode Pembayaran</CardTitle>
                        </div>
                        <CardDescription>Distribusi metode pembayaran</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="h-64 flex items-center justify-center">
                            <Doughnut v-if="paymentMethods.length > 0" :data="paymentChartData"
                                :options="paymentChartOptions" />
                            <p v-else class="text-slate-500">Tidak ada data</p>
                        </div>
                        <!-- Payment Stats -->
                        <div class="mt-4 space-y-2">
                            <div v-for="payment in paymentMethods" :key="payment.method"
                                class="flex items-center justify-between text-sm">
                                <span class="text-slate-600 dark:text-slate-400">
                                    {{ payment.method.toUpperCase() }}
                                </span>
                                <span class="font-medium">
                                    {{ payment.count }} transaksi (Rp {{ formatPrice(payment.total) }})
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Menu Performance -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <BarChart3 class="w-5 h-5 text-purple-500" />
                            <CardTitle>Performa Menu</CardTitle>
                        </div>
                        <CardDescription>Menu terlaris dan paling sedikit terjual</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Top 5 -->
                        <div>
                            <h4 class="flex items-center gap-2 text-sm font-semibold text-green-600 mb-3">
                                <Star class="w-4 h-4" />
                                Top 5 Terlaris
                            </h4>
                            <div class="space-y-2">
                                <div v-for="(menu, index) in topMenus" :key="menu.name"
                                    class="flex items-center justify-between p-2 bg-green-50 dark:bg-green-950 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-6 h-6 flex items-center justify-center bg-green-500 text-white text-xs font-bold rounded-full">
                                            {{ index + 1 }}
                                        </span>
                                        <span class="text-sm font-medium">{{ menu.name }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-green-600">
                                        {{ menu.total_sold }} terjual
                                    </span>
                                </div>
                                <p v-if="topMenus.length === 0" class="text-sm text-slate-500 text-center py-2">
                                    Tidak ada data
                                </p>
                            </div>
                        </div>

                        <!-- Bottom 5 -->
                        <div>
                            <h4 class="text-sm font-semibold text-red-600 mb-3">
                                Bottom 5 Paling Sedikit
                            </h4>
                            <div class="space-y-2">
                                <div v-for="(menu, index) in bottomMenus" :key="menu.name"
                                    class="flex items-center justify-between p-2 bg-red-50 dark:bg-red-950 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-6 h-6 flex items-center justify-center bg-red-500 text-white text-xs font-bold rounded-full">
                                            {{ index + 1 }}
                                        </span>
                                        <span class="text-sm font-medium">{{ menu.name }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-red-600">
                                        {{ menu.total_sold }} terjual
                                    </span>
                                </div>
                                <p v-if="bottomMenus.length === 0" class="text-sm text-slate-500 text-center py-2">
                                    Tidak ada data
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
