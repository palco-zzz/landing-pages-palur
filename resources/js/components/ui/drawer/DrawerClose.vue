<script setup lang="ts">
import { DrawerClose } from 'vaul-vue'

const props = defineProps<{
    asChild?: boolean
}>()
</script>

<template>
    <DrawerClose :as-child="props.asChild">
        <slot />
    </DrawerClose>
</template>
