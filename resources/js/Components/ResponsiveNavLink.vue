<script setup lang="ts">
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? "block w-full ps-3 pe-4 py-2 border-l-4 border-primary text-start text-base font-semibold text-base-content bg-base-200 transition duration-150 ease-in-out focus:outline-none"
        : "block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-base-content/70 hover:text-base-content hover:bg-base-200/50 transition duration-150 ease-in-out focus:outline-none",
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
