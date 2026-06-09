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
        class="flex flex-col gap-4 rounded-3xl bg-[#12141c] p-5 text-white shadow-xl border border-gray-800/40 w-full max-w-sm mx-auto"
    >
        <!-- Top Section: Profile info & Top-Aligned Primary Edit Action -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img
                    v-if="avatarPath"
                    :src="avatarPath"
                    alt="User Avatar"
                    class="h-10 w-10 rounded-full object-cover border border-gray-700"
                />
                <div
                    v-else
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white uppercase border border-indigo-500/30 shadow-inner"
                >
                    {{ avatarInitial }}
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-400"
                        >@{{ username }}</span
                    >
                    <span class="text-sm font-medium text-gray-200">{{
                        formattedDate
                    }}</span>
                </div>
            </div>

            <!-- Prominent High-Frequency Edit Action Button -->
            <button
                @click="emit('edit', stat)"
                class="flex items-center gap-1 text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors bg-indigo-500/10 px-2.5 py-1 rounded-xl"
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

        <!-- Weight & Body Fat Segment -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#4d394e] to-[#2b2235] p-4 border border-purple-500/10"
        >
            <span
                class="text-xs font-bold uppercase tracking-wider text-purple-300/70"
                >Weight & Body Fat</span
            >
            <div class="mt-2 flex items-baseline justify-between">
                <div>
                    <span class="text-3xl font-extrabold tracking-tight">{{
                        stat.body_weight
                    }}</span>
                    <span class="ml-1 text-sm font-semibold text-purple-300"
                        >kg</span
                    >
                </div>
                <div>
                    <span
                        class="text-2xl font-bold tracking-tight text-purple-200"
                        >{{ stat.body_fat }}</span
                    >
                    <span class="ml-0.5 text-sm font-semibold text-purple-300"
                        >%</span
                    >
                </div>
            </div>
        </div>

        <!-- Sleep Tracking Segment -->
        <div
            class="rounded-2xl bg-gradient-to-br from-[#243547] to-[#16222f] p-4 border border-blue-500/10"
        >
            <span
                class="text-xs font-bold uppercase tracking-wider text-blue-300/70"
                >Sleep Duration</span
            >
            <div class="mt-1">
                <span
                    class="text-3xl font-extrabold tracking-tight text-[#62bbf4]"
                    >{{ formattedSleep }}</span
                >
            </div>
        </div>

        <!-- Mood Indicator Segment -->
        <div
            class="rounded-2xl bg-gradient-to-br from-[#1b342b] to-[#12221c] p-4 border border-emerald-500/10"
        >
            <span
                class="text-xs font-bold uppercase tracking-wider text-emerald-300/70"
                >Mood</span
            >
            <div class="mt-3 flex items-center justify-around">
                <div
                    :class="[
                        'p-1.5 rounded-xl transition-all duration-200',
                        stat.mood === 1
                            ? 'bg-emerald-500/20 scale-110 text-emerald-400'
                            : 'text-gray-600',
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
                            ? 'bg-emerald-500/20 scale-110 text-emerald-400'
                            : 'text-gray-600',
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
                            ? 'bg-emerald-500/20 scale-110 text-emerald-400'
                            : 'text-gray-600',
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
                            ? 'bg-emerald-500/20 scale-110 text-emerald-400'
                            : 'text-gray-600',
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

        <!-- Activity Toggles Status Display -->
        <div class="flex flex-col gap-2.5 px-1 py-1">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400 font-medium">Workout</span>
                <span
                    :class="
                        stat.workout
                            ? 'text-emerald-400 font-semibold'
                            : 'text-gray-500 font-medium'
                    "
                >
                    {{ stat.workout ? "Yes" : "No" }}
                </span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400 font-medium">Cardio</span>
                <span
                    :class="
                        stat.cardio
                            ? 'text-emerald-400 font-semibold'
                            : 'text-gray-500 font-medium'
                    "
                >
                    {{ stat.cardio ? "Yes" : "No" }}
                </span>
            </div>
        </div>

        <!-- Notes Layer with Text Layout Elements only -->
        <div
            class="mt-1 flex flex-col gap-1.5 rounded-2xl bg-gray-900/60 p-3.5 border border-gray-800/60"
        >
            <div
                class="text-xs font-bold uppercase tracking-wider text-gray-500"
            >
                <span>Notes</span>
            </div>
            <p
                class="text-sm text-gray-300 leading-relaxed line-clamp-3 whitespace-pre-line"
            >
                {{ stat.notes || "No notes added for this day." }}
            </p>
        </div>

        <!-- Bottom Actions Row: Subdued Low-Frequency Delete Action -->
        <div class="flex justify-end mt-1 px-1">
            <button
                @click="emit('delete', stat.id)"
                class="flex items-center gap-1 text-xs font-semibold text-rose-500/70 hover:text-rose-400 transition-colors bg-rose-500/5 hover:bg-rose-500/10 px-2.5 py-1.5 rounded-xl border border-rose-500/10"
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
                        d="M14.74 9l-.34 6.84m-4.773 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                    />
                </svg>
                <span>Delete Entry</span>
            </button>
        </div>
    </div>
</template>
