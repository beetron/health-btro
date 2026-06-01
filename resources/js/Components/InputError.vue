<script setup lang="ts">
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-error font-medium">
            {{ message }}
        </p>
    </div>
</template>
