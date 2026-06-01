<script setup lang="ts">
import { onMounted, ref } from "vue";

const model = defineModel<string>({
    required: true,
});

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="input input-bordered w-full focus:input-primary transition-colors duration-200"
        v-model="model"
        ref="input"
    />
</template>
