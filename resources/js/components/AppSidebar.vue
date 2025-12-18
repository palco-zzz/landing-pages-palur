<script setup lang="ts">
import { computed } from 'vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Store, History, Utensils, Tags, Users, BarChart3 } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import AppLogo from './AppLogo.vue';

// Get current user
const page = usePage();
const user = computed(() => (page.props.auth as { user: { role?: string } })?.user);
const isAdmin = computed(() => user.value?.role === 'admin');
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="px-2">
            <!-- Operational Group -->
            <div class="py-4">
                <p class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                    Operasional Warung
                </p>
                <nav class="flex flex-col gap-1">
                    <!-- Dashboard -->
                    <Link :href="route('dashboard')" :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group border-l-4',
                        route().current('dashboard')
                            ? 'border-amber-500 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 font-semibold'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
                    ]">
                        <LayoutGrid class="w-5 h-5 shrink-0 transition-colors" :class="route().current('dashboard')
                            ? 'text-amber-600 dark:text-amber-500'
                            : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'" />
                        <span class="text-sm">Dashboard</span>
                    </Link>

                    <!-- POS System -->
                    <Link :href="route('pos.index')" :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group border-l-4',
                        route().current('pos.*')
                            ? 'border-amber-500 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 font-semibold'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
                    ]">
                        <Store class="w-5 h-5 shrink-0 transition-colors" :class="route().current('pos.*')
                            ? 'text-amber-600 dark:text-amber-500'
                            : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'" />
                        <span class="text-sm">POS System</span>
                    </Link>

                    <!-- Riwayat Transaksi -->
                    <Link :href="route('history.index')" :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group border-l-4',
                        route().current('history.*')
                            ? 'border-amber-500 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 font-semibold'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
                    ]">
                        <History class="w-5 h-5 shrink-0 transition-colors" :class="route().current('history.*')
                            ? 'text-amber-600 dark:text-amber-500'
                            : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'" />
                        <span class="text-sm">Riwayat Transaksi</span>
                    </Link>
                </nav>
            </div>

            <!-- Management Group (Admin Only) -->
            <div v-if="isAdmin" class="py-4 border-t border-gray-200 dark:border-gray-700">
                <p class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                    Manajemen & Admin
                </p>
                <nav class="flex flex-col gap-1">
                    <!-- Daftar Menu -->
                    <Link :href="route('menus.index')" :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group border-l-4',
                        route().current('menus.*')
                            ? 'border-amber-500 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 font-semibold'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
                    ]">
                        <Utensils class="w-5 h-5 shrink-0 transition-colors" :class="route().current('menus.*')
                            ? 'text-amber-600 dark:text-amber-500'
                            : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'" />
                        <span class="text-sm">Daftar Menu</span>
                    </Link>

                    <!-- Kategori -->
                    <Link :href="route('categories.index')" :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group border-l-4',
                        route().current('categories.*')
                            ? 'border-amber-500 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 font-semibold'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
                    ]">
                        <Tags class="w-5 h-5 shrink-0 transition-colors" :class="route().current('categories.*')
                            ? 'text-amber-600 dark:text-amber-500'
                            : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'" />
                        <span class="text-sm">Kategori</span>
                    </Link>

                    <!-- Pegawai -->
                    <Link :href="route('users.index')" :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group border-l-4',
                        route().current('users.*')
                            ? 'border-amber-500 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 font-semibold'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
                    ]">
                        <Users class="w-5 h-5 shrink-0 transition-colors" :class="route().current('users.*')
                            ? 'text-amber-600 dark:text-amber-500'
                            : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'" />
                        <span class="text-sm">Pegawai</span>
                    </Link>

                    <!-- Laporan -->
                    <Link :href="route('reports.index')" :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group border-l-4',
                        route().current('reports.*')
                            ? 'border-amber-500 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 font-semibold'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
                    ]">
                        <BarChart3 class="w-5 h-5 shrink-0 transition-colors" :class="route().current('reports.*')
                            ? 'text-amber-600 dark:text-amber-500'
                            : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'" />
                        <span class="text-sm">Laporan</span>
                    </Link>
                </nav>
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
