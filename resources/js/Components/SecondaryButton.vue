<script setup lang="ts">
interface Props {
    type?: "button" | "submit" | "reset";
}

withDefaults(defineProps<Props>(), {
    type: "button",
});
</script>

<template>
    <button
        :type="type"
        class="btn btn-outline text-xs font-semibold uppercase tracking-widest disabled:opacity-25"
    >
        <slot />
    </button>
</template>
