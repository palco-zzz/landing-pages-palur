<script setup lang="ts">
import { computed } from 'vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroup,
    SidebarGroupLabel,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Store, History, Utensils, Tags, Users, BarChart3 } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import AppLogo from './AppLogo.vue';

// Get current user
const page = usePage();
const user = computed(() => (page.props.auth as { user: { role?: string } })?.user);
const isAdmin = computed(() => user.value?.role === 'admin');

// Operational items (visible to all)
const operationalItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
        isActive: route().current('dashboard'),
    },
    {
        title: 'POS System',
        href: route('pos.index'),
        icon: Store,
        isActive: route().current('pos.*'),
    },
    {
        title: 'Riwayat Transaksi',
        href: route('history.index'),
        icon: History,
        isActive: route().current('history.*'),
    },
]);

// Management items (admin only)
const managementItems = computed<NavItem[]>(() => [
    {
        title: 'Daftar Menu',
        href: route('menus.index'),
        icon: Utensils,
        isActive: route().current('menus.*'),
    },
    {
        title: 'Kategori',
        href: route('categories.index'),
        icon: Tags,
        isActive: route().current('categories.*'),
    },
    {
        title: 'Pegawai',
        href: route('users.index'),
        icon: Users,
        isActive: route().current('users.*'),
    },
    {
        title: 'Laporan',
        href: route('reports.index'),
        icon: BarChart3,
        isActive: route().current('reports.*'),
    },
]);
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

        <SidebarContent>
            <!-- Operational Group -->
            <SidebarGroup>
                <SidebarGroupLabel>Operasional Warung</SidebarGroupLabel>
                <NavMain :items="operationalItems" />
            </SidebarGroup>

            <!-- Management Group (Admin Only) -->
            <SidebarGroup v-if="isAdmin">
                <SidebarGroupLabel>Manajemen & Admin</SidebarGroupLabel>
                <NavMain :items="managementItems" />
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
