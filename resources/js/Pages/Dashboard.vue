// Dashboard.vue
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import type { DailyStat } from "@/Types/dailystat";
import type { PageProps } from "@/Types/user";
import StatCard from "@/Pages/StatCard/StatCard.vue";
import StatCardModal from "@/Pages/StatCard/StatCardModal.vue";

const props = defineProps<{
    dailyStats: DailyStat[];
}>();

const page = usePage<PageProps>();

// Safely access current user authentication info from Inertia global state context
const user = computed(() => page.props.auth.user);

const isModalOpen = ref(false);
const activeEditingStat = ref<DailyStat | null>(null);

function openCreateModal(): void {
    activeEditingStat.value = null;
    isModalOpen.value = true;
}

function handleEditInitiated(stat: DailyStat): void {
    activeEditingStat.value = stat;
    isModalOpen.value = true;
}

function handleDeleteInitiated(id: number): void {
    // Prompt for user confirmation before executing data destruction
    if (confirm("Are you sure you want to delete this health log entry?")) {
        router.delete(route("daily-stats.destroy", id), {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="flex items-center justify-between mb-8 px-4 sm:px-0"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                        >
                            Health Tracker
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Manage your daily physical metrics
                        </p>
                    </div>

                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors duration-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4.5v15m7.5-7.5h-15"
                            />
                        </svg>
                        <span>Add</span>
                    </button>
                </div>

                <div
                    v-if="props.dailyStats.length > 0"
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 px-4 sm:px-0"
                >
                    <StatCard
                        v-for="stat in props.dailyStats"
                        :key="stat.id"
                        :stat="stat"
                        :username="user.username"
                        :avatar-path="user.avatar_path"
                        @edit="handleEditInitiated"
                        @delete="handleDeleteInitiated"
                    />
                </div>

                <div
                    v-else
                    class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 mx-4 sm:mx-0"
                >
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                        />
                    </svg>
                    <h3
                        class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100"
                    >
                        No logs recorded
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Get started by adding your health stats for today.
                    </p>
                </div>
            </div>
        </div>

        <!-- Integrated Interactive Entry Manipulation Layer -->
        <StatCardModal
            :is-open="isModalOpen"
            :stat="activeEditingStat"
            @close="isModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
