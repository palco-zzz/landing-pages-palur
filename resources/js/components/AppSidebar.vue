<script setup lang="ts">
import { computed } from 'vue';
import NavFooter from '@/components/NavFooter.vue';
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
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Store, Utensils, History, Users } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import AppLogo from './AppLogo.vue';

// Get current user
const page = usePage();
const user = computed(() => (page.props.auth as { user: { role?: string } })?.user);
const isAdmin = computed(() => user.value?.role === 'admin');

// Main nav items
const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
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

    // Admin-only items
    if (isAdmin.value) {
        items.push(
            {
                title: 'Manajemen Menu',
                href: route('menus.index'),
                icon: Utensils,
            },
            {
                title: 'Kelola Pegawai',
                href: route('users.index'),
                icon: Users,
            }
        );
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
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
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
