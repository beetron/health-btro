// StatCard.vue
<script setup lang="ts">
import type { DailyStat } from "@/Types/dailystat";
import { computed } from "vue";

const props = defineProps<{
    stat: DailyStat;
    username: string;
    avatarPath?: string | null;
}>();

const emit = defineEmits<{
    (e: "edit", stat: DailyStat): void;
    (e: "delete", id: number): void;
}>();

// Formats the YYYY-MM-DD date string into standard layout: Mon, Oct 28, 2026
const formattedDate = computed((): string => {
    if (!props.stat.log_date) return "";
    const date = new Date(props.stat.log_date);
    return date.toLocaleDateString("en-US", {
        weekday: "short",
        year: "numeric",
        month: "short",
        day: "numeric",
        timeZone: "UTC", // Enforce UTC alignment to prevent local timezone shifting offsets
    });
});

// Converts raw minutes into formatted hours and minutes string
const formattedSleep = computed((): string => {
    const minutes = props.stat.sleep_duration || 0;
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    return `${hours}h ${remainingMinutes}m`;
});

// Extracts the first character of the username for the fallback avatar graphic
const avatarInitial = computed((): string => {
    return props.username ? props.username.charAt(0).toUpperCase() : "?";
});
</script>

<template>
    <div
        class="flex flex-col gap-4 rounded-3xl bg-base-100 p-5 text-base-content shadow-md border border-base-300 w-full max-w-sm mx-auto transition-all duration-200"
    >
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img
                    v-if="avatarPath"
                    :src="avatarPath"
                    alt="User Avatar"
                    class="h-10 w-10 rounded-full object-cover border border-base-300"
                />
                <div
                    v-else
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-content uppercase border border-primary/20 shadow-inner"
                >
                    {{ avatarInitial }}
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-base-content/50"
                        >@{{ username }}</span
                    >
                    <span class="text-sm font-medium text-base-content">{{
                        formattedDate
                    }}</span>
                </div>
            </div>

            <button
                @click="emit('edit', stat)"
                class="flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary/80 transition-colors bg-primary/10 px-2.5 py-1 rounded-xl"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="h-3.5 w-3.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                    />
                </svg>
                <span>Edit</span>
            </button>
        </div>

        <div
            class="relative overflow-hidden rounded-2xl bg-base-200 p-4 border border-base-300 border-l-4 border-l-secondary transition-colors duration-200"
        >
            <span
                class="text-xs font-bold uppercase tracking-wider text-secondary"
                >Weight & Body Fat</span
            >
            <div class="mt-2 flex items-baseline justify-between">
                <div>
                    <span
                        class="text-3xl font-extrabold tracking-tight text-base-content"
                        >{{ stat.body_weight }}</span
                    >
                    <span class="ml-1 text-sm font-semibold text-secondary"
                        >kg</span
                    >
                </div>
                <div>
                    <span
                        class="text-2xl font-bold tracking-tight text-base-content"
                        >{{ stat.body_fat }}</span
                    >
                    <span class="ml-0.5 text-sm font-semibold text-secondary"
                        >%</span
                    >
                </div>
            </div>
        </div>

        <div
            class="rounded-2xl bg-base-200 p-4 border border-base-300 border-l-4 border-l-info transition-colors duration-200"
        >
            <span class="text-xs font-bold uppercase tracking-wider text-info"
                >Sleep Duration</span
            >
            <div class="mt-1">
                <span
                    class="text-3xl font-extrabold tracking-tight text-info"
                    >{{ formattedSleep }}</span
                >
            </div>
        </div>

        <div
            class="rounded-2xl bg-base-200 p-4 border border-base-300 border-l-4 border-l-success transition-colors duration-200"
        >
            <span
                class="text-xs font-bold uppercase tracking-wider text-success"
                >Mood</span
            >
            <div class="mt-3 flex items-center justify-around">
                <div
                    :class="[
                        'p-1.5 rounded-xl transition-all duration-200',
                        stat.mood === 1
                            ? 'bg-success/20 scale-110 text-success'
                            : 'text-base-content/20',
                    ]"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="h-7 w-7"
                    >
                        <circle cx="12" cy="12" r="9" />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 10h.01M15 10h.01M16 16.5a4 4 0 0 0-8 0"
                        />
                    </svg>
                </div>
                <div
                    :class="[
                        'p-1.5 rounded-xl transition-all duration-200',
                        stat.mood === 2
                            ? 'bg-success/20 scale-110 text-success'
                            : 'text-base-content/20',
                    ]"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="h-7 w-7"
                    >
                        <circle cx="12" cy="12" r="9" />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 10h.01M15 10h.01M8 15h8"
                        />
                    </svg>
                </div>
                <div
                    :class="[
                        'p-1.5 rounded-xl transition-all duration-200',
                        stat.mood === 3
                            ? 'bg-success/20 scale-110 text-success'
                            : 'text-base-content/20',
                    ]"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="h-7 w-7"
                    >
                        <circle cx="12" cy="12" r="9" />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 10h.01M15 10h.01M8 14.5a4 4 0 0 0 8 0"
                        />
                    </svg>
                </div>
                <div
                    :class="[
                        'p-1.5 rounded-xl transition-all duration-200',
                        stat.mood === 4
                            ? 'bg-success/20 scale-110 text-success'
                            : 'text-base-content/20',
                    ]"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="h-7 w-7"
                    >
                        <circle cx="12" cy="12" r="9" />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 11q1.5-2 3 0M13 11q1.5-2 3 0"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            fill="currentColor"
                            d="M8 14h8c0 2.5-1.8 4.5-4 4.5S8 16.5 8 14z"
                        />
                    </svg>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-2.5 px-1 py-1">
            <div class="flex items-center justify-between text-sm">
                <span class="text-base-content/60 font-medium">Workout</span>
                <span
                    :class="
                        stat.workout
                            ? 'text-success font-semibold'
                            : 'text-base-content/40 font-medium'
                    "
                >
                    {{ stat.workout ? "Yes" : "No" }}
                </span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-base-content/60 font-medium">Cardio</span>
                <span
                    :class="
                        stat.cardio
                            ? 'text-success font-semibold'
                            : 'text-base-content/40 font-medium'
                    "
                >
                    {{ stat.cardio ? "Yes" : "No" }}
                </span>
            </div>
        </div>

        <div
            class="mt-1 flex flex-col gap-1.5 rounded-2xl bg-base-200 p-3.5 border border-base-300 transition-colors duration-200"
        >
            <div
                class="text-xs font-bold uppercase tracking-wider text-base-content/40"
            >
                <span>Notes</span>
            </div>
            <p
                class="text-sm text-base-content/80 leading-relaxed line-clamp-3 whitespace-pre-line"
            >
                {{ stat.notes || "" }}
            </p>
        </div>

        <div class="flex justify-end mt-1 px-1">
            <button
                @click="emit('delete', stat.id)"
                class="flex items-center gap-1 text-xs font-semibold text-error/80 hover:text-error transition-colors bg-error/5 hover:bg-error/10 px-2.5 py-1.5 rounded-xl border border-error/10"
            >
                <svg
                    xmlns="http://www.w3.org/2000/xl"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="h-3.5 w-3.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14.74 9l-.34 6.84m-4.773 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                    />
                </svg>
                <span>Delete Entry</span>
            </button>
        </div>
    </div>
</template>
