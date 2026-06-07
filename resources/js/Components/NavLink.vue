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
        ? "inline-flex items-center px-4 pt-1 border-b-2 border-primary text-sm font-semibold leading-5 text-base-content focus:outline-none transition-all duration-200 ease-in-out"
        : "inline-flex items-center px-4 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-base-content/70 hover:text-base-content hover:bg-base-200 hover:border-base-content/40 rounded-t-lg focus:outline-none transition-all duration-200 ease-in-out",
);
</script>

<template>
    <Link :href="href" :class="classes" class="group relative overflow-hidden">
        <span class="flex items-center gap-2 h-full py-4 relative z-10">
            <span
                v-if="$slots.icon"
                class="flex shrink-0 w-4 h-4 opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all duration-200"
            >
                <slot name="icon" />
            </span>

            <span
                class="group-hover:scale-[1.02] transition-transform duration-200"
            >
                <slot />
            </span>
        </span>
    </Link>
</template>
