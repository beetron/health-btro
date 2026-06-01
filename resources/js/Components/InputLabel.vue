<script setup lang="ts">
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="label py-1 px-0">
        <span class="label-text font-medium text-base-content">
            <span v-if="value">{{ value }}</span>
            <slot v-else />
        </span>
    </label>
</template>
