<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm text-base-content hover:bg-base-200 active:bg-base-300 transition-colors duration-200 rounded-btn focus:outline-none"
    >
        <slot />
    </Link>
</template>
