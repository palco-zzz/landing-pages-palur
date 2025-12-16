<script setup lang="ts">
import { watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Toaster, toast } from 'vue-sonner';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

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
} | undefined);

watch(flash, (newFlash) => {
    // Debug log to check if frontend receives data
    console.log("Flash Message Received:", newFlash);

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
