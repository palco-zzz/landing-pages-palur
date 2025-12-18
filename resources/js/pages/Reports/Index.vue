<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    FileDown,
    FileSpreadsheet,
    TrendingUp,
    CreditCard,
    Star,
    BarChart3,
    CalendarRange,
    Calendar,
    Download
} from 'lucide-vue-next';

// VueDatePicker
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

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

// --- Date Range Picker State ---
// Parse initial dates from props (YYYY-MM-DD strings)
const parseDate = (dateStr: string): Date => {
    const [year, month, day] = dateStr.split('-').map(Number);
    return new Date(year, month - 1, day);
};

const formatDateToYMD = (date: Date): string => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Initialize dateRange with [startDate, endDate] as Date objects
const dateRange = ref<[Date, Date] | null>([
    parseDate(props.filters.start_date),
    parseDate(props.filters.end_date),
]);

// Track active preset button
const activePreset = ref<'today' | 'this_month' | 'last_7_days' | null>('today');

// Helper to apply presets
const applyPreset = (type: 'today' | 'this_month' | 'last_7_days') => {
    activePreset.value = type; // Track the active button

    const today = new Date();
    let start = new Date();
    let end = new Date();

    if (type === 'today') {
        // [today, today]
        // No change needed for start/end as they are already today
    } else if (type === 'this_month') {
        // [startOfMonth, today]
        start = new Date(today.getFullYear(), today.getMonth(), 1);
    } else if (type === 'last_7_days') {
        // [today - 7, today]
        start.setDate(today.getDate() - 7);
    }

    dateRange.value = [start, end];
};

// Helper for trigger display
const formatDateTrigger = (dates: Date[]) => {
    if (!dates || dates.length < 2) return 'Pilih Tanggal';
    const start = dates[0];
    const end = dates[1];
    const options: Intl.DateTimeFormatOptions = { day: '2-digit', month: 'short', year: 'numeric' };
    return `${start.toLocaleDateString('id-ID', options)} - ${end.toLocaleDateString('id-ID', options)}`;
};

// Watch for date range changes and apply filter
watch(dateRange, (newRange) => {
    if (newRange && newRange[0] && newRange[1]) {
        const startDate = formatDateToYMD(newRange[0]);
        const endDate = formatDateToYMD(newRange[1]);

        router.get(route('reports.index'), {
            start_date: startDate,
            end_date: endDate,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    }
});

// Export functions (use current range values)
const exportPdf = () => {
    if (dateRange.value) {
        window.open(route('reports.export-pdf', {
            start_date: formatDateToYMD(dateRange.value[0]),
            end_date: formatDateToYMD(dateRange.value[1]),
        }));
    }
};

const exportExcel = () => {
    if (dateRange.value) {
        window.open(route('reports.export-excel', {
            start_date: formatDateToYMD(dateRange.value[0]),
            end_date: formatDateToYMD(dateRange.value[1]),
        }));
    }
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
            <!-- Header Title -->
            <div class="flex flex-col">
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">
                    Laporan & Analitik
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">
                    Pantau performa warung dengan data real-time
                </p>
            </div>

            <!-- CONTROL BAR (Filter & Actions) -->
            <div
                class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                <!-- Left: Filter Logic -->
                <div class="flex flex-col gap-3 w-full md:w-auto">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <Label class="text-sm font-medium text-gray-500 dark:text-zinc-400">Periode Laporan</Label>
                        <!-- Quick Chips -->
                        <div class="flex gap-2">
                            <button @click="applyPreset('today')" type="button" :class="[
                                'px-3 py-1 text-xs font-medium rounded-full border transition-colors cursor-pointer',
                                activePreset === 'today'
                                    ? 'bg-amber-100 text-amber-700 border-amber-300 ring-1 ring-amber-200 dark:bg-amber-500/20 dark:text-amber-400 dark:border-amber-500/30'
                                    : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-700'
                            ]">
                                Hari Ini
                            </button>
                            <button @click="applyPreset('this_month')" type="button" :class="[
                                'px-3 py-1 text-xs font-medium rounded-full border transition-colors cursor-pointer',
                                activePreset === 'this_month'
                                    ? 'bg-amber-100 text-amber-700 border-amber-300 ring-1 ring-amber-200 dark:bg-amber-500/20 dark:text-amber-400 dark:border-amber-500/30'
                                    : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-700'
                            ]">
                                Bulan Ini
                            </button>
                        </div>
                    </div>

                    <!-- Date Picker -->
                    <VueDatePicker v-model="dateRange" range :auto-apply="true" :close-on-auto-apply="true"
                        :enable-time-picker="false" :teleport="true" format="dd MMM yyyy" :dark="false"
                        class="w-full md:w-auto">
                        <template #trigger>
                            <div
                                class="flex items-center justify-between w-full md:w-[280px] px-4 py-2.5 bg-white dark:bg-black/40 border border-gray-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:border-amber-400 dark:hover:border-amber-500 transition-colors group">
                                <span v-if="dateRange && dateRange[0] && dateRange[1]"
                                    class="text-sm font-medium text-gray-700 dark:text-zinc-200">
                                    {{ formatDateTrigger(dateRange) }}
                                </span>
                                <span v-else class="text-sm text-gray-400 dark:text-zinc-500">Pilih Periode...</span>
                                <Calendar
                                    class="w-4 h-4 text-gray-400 dark:text-zinc-500 group-hover:text-amber-500 transition-colors" />
                            </div>
                        </template>
                    </VueDatePicker>
                </div>

                <!-- Right: Actions -->
                <div class="flex flex-row gap-3 w-full md:w-auto">
                    <button @click="exportPdf" type="button"
                        class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 font-medium transition-colors dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 dark:hover:bg-red-500/20">
                        <Download class="w-4 h-4" />
                        PDF
                    </button>
                    <button @click="exportExcel" type="button"
                        class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-100 font-medium transition-colors dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 dark:hover:bg-emerald-500/20">
                        <FileSpreadsheet class="w-4 h-4" />
                        Excel
                    </button>
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

<style>
/* VueDatePicker Dark Mode Overrides */
.dp__theme_dark {
    --dp-background-color: #27272a;
    --dp-text-color: #f4f4f5;
    --dp-hover-color: #3f3f46;
    --dp-hover-text-color: #f4f4f5;
    --dp-primary-color: #f97316;
    --dp-primary-text-color: #fff;
    --dp-secondary-color: #52525b;
    --dp-border-color: #52525b;
    --dp-menu-border-color: #3f3f46;
    --dp-input-border-color: #52525b;
    --dp-success-color: #22c55e;
    --dp-success-color-disabled: #16a34a;
    --dp-icon-color: #a1a1aa;
    --dp-range-between-dates-background-color: rgba(249, 115, 22, 0.2);
    --dp-range-between-dates-text-color: #f4f4f5;
}
</style>
