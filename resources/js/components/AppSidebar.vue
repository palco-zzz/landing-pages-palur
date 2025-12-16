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
import { LayoutGrid, Store, History, Utensils, Tags, Users } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import AppLogo from './AppLogo.vue';

// Get current user
const page = usePage();
const user = computed(() => (page.props.auth as { user: { role?: string } })?.user);
const isAdmin = computed(() => user.value?.role === 'admin');

// Operational items (visible to all)
const operationalItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'POS System',
        href: route('pos.index'),
        icon: Store,
    },
    {
        title: 'Riwayat Transaksi',
        href: route('history.index'),
        icon: History,
    },
];

// Management items (admin only)
const managementItems: NavItem[] = [
    {
        title: 'Daftar Menu',
        href: route('menus.index'),
        icon: Utensils,
    },
    {
        title: 'Kategori',
        href: route('categories.index'),
        icon: Tags,
    },
    {
        title: 'Pegawai',
        href: route('users.index'),
        icon: Users,
    },
];
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
