<script setup lang="ts">
import { DrawerTrigger } from 'vaul-vue'

const props = defineProps<{
    asChild?: boolean
}>()
</script>

<template>
    <DrawerTrigger :as-child="props.asChild">
        <slot />
    </DrawerTrigger>
</template>
