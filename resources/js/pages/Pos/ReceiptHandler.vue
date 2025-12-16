<script setup lang="ts">
/**
 * Receipt Handler Component
 * Headless component that watches for print_job flash data and triggers printing
 */
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { usePrinter, type ReceiptData } from '@/composables/usePrinter';

const page = usePage();
const { print } = usePrinter();

// Watch for flash.print_job changes
watch(
    () => {
        const flash = page.props.flash as Record<string, unknown> | undefined;
        return flash?.print_job as ReceiptData | undefined;
    },
    (printJob) => {
        if (printJob && printJob.type && printJob.items) {
            // Small delay to ensure page has fully loaded
            setTimeout(() => {
                print(printJob);
            }, 500);
        }
    },
    { immediate: true }
);
</script>

<template>
    <!-- Headless component - no visible UI -->
</template>
