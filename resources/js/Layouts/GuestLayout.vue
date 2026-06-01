<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import ThemeToggle from "@/Components/ThemeToggle.vue";
</script>

<template>
    <div
        class="min-h-screen bg-base-200 text-base-content transition-colors duration-200"
    >
        <header
            class="navbar bg-base-100 border-b border-base-300/80 px-4 sm:px-6 shadow-sm"
        >
            <div class="flex-1">
                <Link
                    href="/"
                    class="btn btn-ghost text-xl normal-case font-bold text-base-content"
                >
                    Health Stats Tracker
                </Link>
            </div>
            <div class="flex-none">
                <ThemeToggle />
            </div>
        </header>

        <main class="py-6">
            <slot />
        </main>
    </div>
</template>
