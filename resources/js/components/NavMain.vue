<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.isActive" :tooltip="item.title"
                    class="transition-all duration-200 border-l-4 border-transparent" :class="[
                        item.isActive
                            ? 'border-primary bg-accent text-primary font-medium'
                            : 'text-muted-foreground hover:text-foreground hover:bg-accent/50'
                    ]">
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
