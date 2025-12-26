<script setup lang="ts">
import { DrawerDescription } from 'vaul-vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
    class?: string
    asChild?: boolean
}>()
</script>

<template>
    <DrawerDescription :as-child="props.asChild" :class="cn('text-sm text-muted-foreground', props.class)">
        <slot />
    </DrawerDescription>
</template>
