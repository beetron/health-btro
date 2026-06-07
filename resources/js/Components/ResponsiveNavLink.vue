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
        ? "block w-full ps-3 pe-4 py-2.5 border-l-4 border-primary text-start text-base font-semibold text-base-content bg-base-200 focus:outline-none transition duration-150 ease-in-out"
        : "block w-full ps-3 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium text-base-content/70 hover:text-base-content hover:bg-base-200 hover:border-base-content/20 focus:outline-none transition duration-150 ease-in-out",
);
</script>

<template>
    <Link :href="href" :class="classes" class="group">
        <div class="flex items-center gap-3">
            <div
                v-if="$slots.icon"
                class="h-5 w-5 shrink-0 opacity-60 group-hover:opacity-100 transition-opacity duration-150"
            >
                <slot name="icon" />
            </div>
            <div>
                <slot />
            </div>
        </div>
    </Link>
</template>
