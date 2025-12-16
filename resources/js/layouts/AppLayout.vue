<script setup lang="ts">
import { watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Toaster } from '@/components/ui/sonner';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

// Fallback style import (Shadcn should handle this, but just in case)
import 'vue-sonner/style.css';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// Flash message watcher for toast notifications
const page = usePage();
const flash = computed(() => page.props.flash as {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
    print_job?: unknown;
} | undefined);

watch(flash, (newFlash) => {
    // Debug log - show actual content
    console.log("Flash Content:", newFlash?.success, newFlash?.print_job);

    if (newFlash?.success) toast.success(newFlash.success);
    if (newFlash?.error) toast.error(newFlash.error);
    if (newFlash?.warning) toast.warning(newFlash.warning);
    if (newFlash?.info) toast.info(newFlash.info);
}, { deep: true, immediate: true });
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppSidebarLayout>

    <!-- Global Toast Notifications - MUST be at root level -->
    <Toaster position="top-right" richColors />
</template>
