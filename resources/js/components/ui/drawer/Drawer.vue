<script setup lang="ts">
import { DrawerRoot } from 'vaul-vue'

const props = withDefaults(defineProps<{
    open?: boolean
    modal?: boolean
    activeSnapPoint?: number | string | null
    snapPoints?: (number | string)[]
    fadeFromIndex?: number
    shouldScaleBackground?: boolean
    dismissible?: boolean
    direction?: 'top' | 'bottom' | 'left' | 'right'
}>(), {
    shouldScaleBackground: true,
})

const emit = defineEmits<{
    'update:open': [value: boolean]
    'update:activeSnapPoint': [value: string | number]
}>()
</script>

<template>
    <DrawerRoot :open="props.open" :modal="props.modal" :active-snap-point="props.activeSnapPoint"
        :snap-points="props.snapPoints" :fade-from-index="props.fadeFromIndex"
        :should-scale-background="props.shouldScaleBackground" :dismissible="props.dismissible"
        :direction="props.direction" @update:open="emit('update:open', $event)"
        @update:active-snap-point="emit('update:activeSnapPoint', $event)">
        <slot />
    </DrawerRoot>
</template>
