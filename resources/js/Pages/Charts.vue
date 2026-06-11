<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, onMounted, watch, computed } from "vue";
import { Chart, registerables } from "chart.js";
import type { DailyStat } from "@/Types/dailystat";

Chart.register(...registerables);

interface AvailableMonth {
    value: string;
    label: string;
}

const props = defineProps<{
    dailyStats: DailyStat[];
    filters: {
        start_month: string;
        end_month: string;
    };
    availableMonths: AvailableMonth[];
}>();

const chartCanvasRef = ref<HTMLCanvasElement | null>(null);
let chartInstance: Chart | null = null;

const startMonthFilter = ref(props.filters.start_month);
const endMonthFilter = ref(props.filters.end_month);

// Track visualization visibility states independently across variables
const visibilitySettings = ref({
    weight: true,
    bodyFat: true,
    sleep: true,
    mood: true,
    workout: false,
    cardio: false,
});

// Chronologically sorted records ensuring line charts read oldest to newest
const sortedDailyStats = computed(() => {
    return [...props.dailyStats].sort((a, b) => {
        if (!a.log_date || !b.log_date) return 0;
        return new Date(a.log_date).getTime() - new Date(b.log_date).getTime();
    });
});

// Chronological target labels mapped from sorted telemetry records
const chartLabels = computed(() => {
    return sortedDailyStats.value.map((stat) => {
        if (!stat.log_date) return "";
        const date = new Date(stat.log_date);
        return date.toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
            year: "2-digit",
            timeZone: "UTC",
        });
    });
});

// Update criteria query values back to the backend service
function handleFilterChange(): void {
    router.get(
        route("charts"),
        {
            start_month: startMonthFilter.value,
            end_month: endMonthFilter.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ["dailyStats", "filters"],
        },
    );
}

// Generate the operational visual mapping array rules
function generateDatasets() {
    return [
        {
            label: "Weight (kg)",
            data: sortedDailyStats.value.map((s) => s.body_weight),
            borderColor: "rgb(249, 115, 22)",
            backgroundColor: "rgba(249, 115, 22, 0.1)",
            yAxisID: "yWeight",
            tension: 0.1,
            hidden: !visibilitySettings.value.weight,
        },
        {
            label: "Body Fat (%)",
            data: sortedDailyStats.value.map((s) => s.body_fat),
            borderColor: "rgb(236, 72, 153)",
            backgroundColor: "rgba(236, 72, 153, 0.1)",
            yAxisID: "yPercent",
            tension: 0.1,
            hidden: !visibilitySettings.value.bodyFat,
        },
        {
            label: "Sleep (Hours)",
            data: sortedDailyStats.value.map((s) =>
                s.sleep_duration ? s.sleep_duration / 60 : 0,
            ),
            borderColor: "rgb(59, 130, 246)",
            backgroundColor: "rgba(59, 130, 246, 0.1)",
            yAxisID: "yHours",
            tension: 0.1,
            hidden: !visibilitySettings.value.sleep,
        },
        {
            label: "Mood Level",
            data: sortedDailyStats.value.map((s) => s.mood),
            borderColor: "rgb(34, 197, 94)",
            backgroundColor: "rgba(34, 197, 94, 0.1)",
            yAxisID: "yMood",
            tension: 0.1,
            hidden: !visibilitySettings.value.mood,
        },
        {
            label: "Workout Completed",
            data: sortedDailyStats.value.map((s) => (s.workout ? 1 : 0)),
            borderColor: "rgb(168, 85, 247)",
            backgroundColor: "rgba(168, 85, 247, 0.2)",
            yAxisID: "yBinary",
            type: "bar" as const,
            hidden: !visibilitySettings.value.workout,
        },
        {
            label: "Cardio Completed",
            data: sortedDailyStats.value.map((s) => (s.cardio ? 1 : 0)),
            borderColor: "rgb(6, 182, 212)",
            backgroundColor: "rgba(6, 182, 212, 0.2)",
            yAxisID: "yBinary",
            type: "bar" as const,
            hidden: !visibilitySettings.value.cardio,
        },
    ];
}

function initializeChart(): void {
    if (!chartCanvasRef.value) return;

    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(chartCanvasRef.value, {
        type: "line",
        data: {
            labels: chartLabels.value,
            datasets: generateDatasets(),
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                },
                yWeight: {
                    type: "linear",
                    position: "left",
                    title: { display: true, text: "Weight (kg)" },
                },
                yPercent: {
                    type: "linear",
                    position: "right",
                    title: { display: true, text: "Body Fat (%)" },
                    grid: { drawOnChartArea: false },
                },
                yHours: {
                    type: "linear",
                    position: "left",
                    title: { display: true, text: "Sleep (h)" },
                    grid: { drawOnChartArea: false },
                },
                yMood: {
                    type: "linear",
                    position: "right",
                    min: 1,
                    max: 4,
                    title: { display: true, text: "Mood (1-4)" },
                    grid: { drawOnChartArea: false },
                    ticks: { stepSize: 1 },
                },
                yBinary: {
                    type: "linear",
                    position: "right",
                    min: 0,
                    max: 1,
                    display: false,
                    grid: { drawOnChartArea: false },
                },
            },
        },
    });
}

// Redraw layout properties smoothly when visibility properties change
function syncVisibility(): void {
    if (!chartInstance) return;
    chartInstance.data.datasets[0].hidden = !visibilitySettings.value.weight;
    chartInstance.data.datasets[1].hidden = !visibilitySettings.value.bodyFat;
    chartInstance.data.datasets[2].hidden = !visibilitySettings.value.sleep;
    chartInstance.data.datasets[3].hidden = !visibilitySettings.value.mood;
    chartInstance.data.datasets[4].hidden = !visibilitySettings.value.workout;
    chartInstance.data.datasets[5].hidden = !visibilitySettings.value.cardio;
    chartInstance.update();
}

watch(visibilitySettings, syncVisibility, { deep: true });

watch(
    () => props.dailyStats,
    () => {
        if (chartInstance) {
            chartInstance.data.labels = chartLabels.value;
            chartInstance.data.datasets = generateDatasets();
            chartInstance.update();
        }
    },
    { deep: true },
);

onMounted(() => {
    initializeChart();
});
</script>

<template>
    <Head title="Metrics Charts" />

    <AuthenticatedLayout>
        <div
            class="py-12 bg-base-200 min-h-screen transition-colors duration-200"
        >
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 px-4 sm:px-0"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold text-base-content transition-colors duration-200"
                        >
                            Analytics Charts
                        </h1>
                        <p
                            class="text-sm text-base-content/60 transition-colors duration-200"
                        >
                            Visualize trends and shifts in historical metrics
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <div class="form-control">
                            <label
                                class="label py-1 text-xs font-semibold text-base-content/50"
                                >From</label
                            >
                            <select
                                v-model="startMonthFilter"
                                @change="handleFilterChange"
                                class="select select-bordered rounded-xl bg-base-100 shadow-sm font-medium text-sm text-center pl-6 pr-10 min-w-[155px] focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option
                                    v-for="item in props.availableMonths"
                                    :key="'start-' + item.value"
                                    :value="item.value"
                                >
                                    {{ item.label }}
                                </option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label
                                class="label py-1 text-xs font-semibold text-base-content/50"
                                >To</label
                            >
                            <select
                                v-model="endMonthFilter"
                                @change="handleFilterChange"
                                class="select select-bordered rounded-xl bg-base-100 shadow-sm font-medium text-sm text-center pl-6 pr-10 min-w-[155px] focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option
                                    v-for="item in props.availableMonths"
                                    :key="'end-' + item.value"
                                    :value="item.value"
                                >
                                    {{ item.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 px-4 sm:px-0">
                    <div
                        class="lg:col-span-1 bg-base-100 rounded-3xl border border-base-300 p-6 shadow-md h-fit flex flex-col gap-4"
                    >
                        <h3
                            class="text-xs font-bold uppercase tracking-wider text-base-content/40 mb-2"
                        >
                            Toggle Visibility
                        </h3>

                        <label
                            class="flex items-center gap-3 cursor-pointer p-2 rounded-xl hover:bg-base-200 transition-colors"
                        >
                            <input
                                type="checkbox"
                                v-model="visibilitySettings.weight"
                                class="checkbox checkbox-secondary rounded-lg"
                            />
                            <span class="text-sm font-medium text-base-content"
                                >Weight</span
                            >
                        </label>

                        <label
                            class="flex items-center gap-3 cursor-pointer p-2 rounded-xl hover:bg-base-200 transition-colors"
                        >
                            <input
                                type="checkbox"
                                v-model="visibilitySettings.bodyFat"
                                class="checkbox checkbox-secondary rounded-lg"
                            />
                            <span class="text-sm font-medium text-base-content"
                                >Body Fat</span
                            >
                        </label>

                        <label
                            class="flex items-center gap-3 cursor-pointer p-2 rounded-xl hover:bg-base-200 transition-colors"
                        >
                            <input
                                type="checkbox"
                                v-model="visibilitySettings.sleep"
                                class="checkbox checkbox-info rounded-lg"
                            />
                            <span class="text-sm font-medium text-base-content"
                                >Sleep Duration</span
                            >
                        </label>

                        <label
                            class="flex items-center gap-3 cursor-pointer p-2 rounded-xl hover:bg-base-200 transition-colors"
                        >
                            <input
                                type="checkbox"
                                v-model="visibilitySettings.mood"
                                class="checkbox checkbox-success rounded-lg"
                            />
                            <span class="text-sm font-medium text-base-content"
                                >Mood Index</span
                            >
                        </label>

                        <label
                            class="flex items-center gap-3 cursor-pointer p-2 rounded-xl hover:bg-base-200 transition-colors"
                        >
                            <input
                                type="checkbox"
                                v-model="visibilitySettings.workout"
                                class="checkbox checkbox-primary rounded-lg"
                            />
                            <span class="text-sm font-medium text-base-content"
                                >Workout Logs</span
                            >
                        </label>

                        <label
                            class="flex items-center gap-3 cursor-pointer p-2 rounded-xl hover:bg-base-200 transition-colors"
                        >
                            <input
                                type="checkbox"
                                v-model="visibilitySettings.cardio"
                                class="checkbox checkbox-accent rounded-lg"
                            />
                            <span class="text-sm font-medium text-base-content"
                                >Cardio Logs</span
                            >
                        </label>
                    </div>

                    <div
                        class="lg:col-span-3 bg-base-100 rounded-3xl border border-base-300 p-6 shadow-md min-h-[500px] flex flex-col justify-center"
                    >
                        <div
                            v-show="props.dailyStats.length > 0"
                            class="relative w-full h-[450px]"
                        >
                            <canvas ref="chartCanvasRef"></canvas>
                        </div>

                        <div
                            v-if="props.dailyStats.length === 0"
                            class="text-center py-16"
                        >
                            <svg
                                class="mx-auto h-12 w-12 text-base-content/30"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"
                                />
                            </svg>
                            <h3
                                class="mt-2 text-sm font-semibold text-base-content"
                            >
                                No data within selected timeframe
                            </h3>
                            <p class="mt-1 text-sm text-base-content/50">
                                Try widening your monthly filters to map active
                                entries.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
