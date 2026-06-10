// StatCardModal.vue
<script setup lang="ts">
import { watch, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import type { DailyStat } from "@/Types/dailystat";

const props = defineProps<{
    isOpen: boolean;
    stat: DailyStat | null;
}>();

const emit = defineEmits<{
    (e: "close"): void;
}>();

// Form tracking instance mapping directly to Laravel FormRequest validation rules
const form = useForm({
    log_date: "",
    body_weight: 0,
    body_fat: 0,
    sleep_duration: 0,
    mood: 2,
    workout: false,
    cardio: false,
    notes: "",
});

// Presentation helper fields for splitting total sleep minutes into separate inputs
const sleepHours = ref(0);
const sleepMinutes = ref(0);

// Get a clean YYYY-MM-DD format for local time zone assignment
function getTodayDateString(): string {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, "0");
    const day = String(today.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
}

// Synchronize incoming prop values whenever the modal opens or shifts edit context
watch(
    () => props.isOpen,
    (shouldOpen) => {
        if (shouldOpen) {
            if (props.stat) {
                // Slices the incoming string to isolate only the YYYY-MM-DD portion for native input elements
                form.log_date = props.stat.log_date
                    ? props.stat.log_date.substring(0, 10)
                    : "";

                form.body_weight = props.stat.body_weight;
                form.body_fat = props.stat.body_fat;
                form.sleep_duration = props.stat.sleep_duration;
                form.mood = props.stat.mood;
                form.workout = props.stat.workout;
                form.cardio = props.stat.cardio;
                form.notes = props.stat.notes || "";

                // Calculate display metrics from database tracking integer
                sleepHours.value = Math.floor(props.stat.sleep_duration / 60);
                sleepMinutes.value = props.stat.sleep_duration % 60;
            } else {
                // Initialize clean default values for new recording workflows
                form.reset();
                form.log_date = getTodayDateString();
                sleepHours.value = 7;
                sleepMinutes.value = 0;
            }
        }
    },
);

function submitForm(): void {
    // Compile interactive time presentation adjustments back into uniform tracking integer
    form.sleep_duration = sleepHours.value * 60 + sleepMinutes.value;

    if (props.stat) {
        form.put(route("daily-stats.update", props.stat.id), {
            onSuccess: () => emit("close"),
        });
    } else {
        form.post(route("daily-stats.store"), {
            onSuccess: () => emit("close"),
        });
    }
}

function handleWeightInput(event: Event): void {
    const target = event.target as HTMLInputElement;
    // Strip out all characters except digits and solitary decimal points
    let validatedValue = target.value.replace(/[^0-9.]/g, "");

    // Prevent entry of multiple decimal points
    const numericSegments = validatedValue.split(".");
    if (numericSegments.length > 2) {
        validatedValue =
            numericSegments[0] + "." + numericSegments.slice(1).join("");
    }

    // Enforce a maximum of 3 digits for the whole number section
    if (numericSegments[0].length > 3) {
        numericSegments[0] = numericSegments[0].slice(0, 3);
    }

    // Enforce a maximum of 1 digit for the fractional section
    if (numericSegments[1] !== undefined && numericSegments[1].length > 1) {
        numericSegments[1] = numericSegments[1].slice(0, 1);
    }

    // Reassemble the sanitized string layout
    validatedValue =
        numericSegments[1] !== undefined
            ? `${numericSegments[0]}.${numericSegments[1]}`
            : numericSegments[0];

    // Constrain absolute upper ceiling limits to 999.9
    if (parseFloat(validatedValue) > 999.9) {
        validatedValue = "999.9";
    }

    // Synchronize both native input element view state and Inertia form tracking properties
    target.value = validatedValue;
    form.body_weight = validatedValue === "" ? 0 : parseFloat(validatedValue);
}
</script>

<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity"
    >
        <div
            class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-base-100 text-base-content shadow-2xl border border-base-300 animate-in fade-in zoom-in-95 duration-200"
        >
            <div
                class="flex items-center justify-between border-b border-base-300 p-6"
            >
                <h2 class="text-xl font-bold tracking-tight">
                    {{ stat ? "Edit Daily Log" : "Add Daily Log" }}
                </h2>
                <button
                    @click="emit('close')"
                    class="rounded-xl p-1.5 text-base-content/60 hover:bg-base-200 hover:text-base-content transition-colors duration-200"
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
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <form
                @submit.prevent="submitForm"
                class="p-6 space-y-5 max-h-[80vh] overflow-y-auto custom-scrollbar bg-base-100"
            >
                <div>
                    <label
                        class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-base-content/70 mb-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-4 w-4 text-primary"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                            />
                        </svg>
                        Log Date
                    </label>
                    <input
                        type="date"
                        v-model="form.log_date"
                        class="w-full rounded-xl border border-base-300 bg-base-200 px-4 py-3 text-sm text-base-content focus:border-primary focus:ring-1 focus:ring-primary transition-colors duration-200"
                        required
                    />
                    <p
                        v-if="form.errors.log_date"
                        class="mt-1.5 text-xs text-error font-medium"
                    >
                        {{ form.errors.log_date }}
                    </p>
                </div>

                <div
                    class="grid grid-cols-2 gap-4 rounded-2xl bg-base-200 p-4 border border-base-300 border-l-4 border-l-secondary shadow-sm transition-colors duration-200"
                >
                    <div>
                        <label
                            class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-secondary mb-2"
                        >
                            <span>Weight (kg)</span>
                        </label>
                        <input
                            type="text"
                            inputmode="decimal"
                            :value="form.body_weight || ''"
                            @input="handleWeightInput"
                            class="w-full rounded-xl border border-base-300 bg-base-100 px-4 py-2.5 text-sm font-semibold text-base-content focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors duration-200"
                            placeholder="0.0"
                            required
                        />
                        <p
                            v-if="form.errors.body_weight"
                            class="mt-1.5 text-xs text-error font-medium"
                        >
                            {{ form.errors.body_weight }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-secondary mb-2"
                        >
                            <span>Body Fat (%)</span>
                        </label>
                        <input
                            type="number"
                            step="0.1"
                            v-model.number="form.body_fat"
                            class="w-full rounded-xl border border-base-300 bg-base-100 px-4 py-2.5 text-sm font-semibold text-base-content focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors duration-200"
                            required
                        />
                        <p
                            v-if="form.errors.body_fat"
                            class="mt-1.5 text-xs text-error font-medium"
                        >
                            {{ form.errors.body_fat }}
                        </p>
                    </div>
                </div>

                <div
                    class="rounded-2xl bg-base-200 p-4 border border-base-300 border-l-4 border-l-info shadow-sm transition-colors duration-200"
                >
                    <label
                        class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-info mb-3"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-4 w-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"
                            />
                        </svg>
                        Sleep Duration
                    </label>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 flex-1">
                            <input
                                type="number"
                                min="0"
                                max="24"
                                v-model.number="sleepHours"
                                class="w-full rounded-xl border border-base-300 bg-base-100 px-4 py-2.5 text-sm text-center font-bold text-info focus:border-info focus:ring-1 focus:ring-info transition-colors duration-200"
                            />
                            <span
                                class="text-xs font-semibold text-base-content/60 uppercase tracking-wider"
                                >Hrs</span
                            >
                        </div>
                        <div class="flex items-center gap-2 flex-1">
                            <input
                                type="number"
                                min="0"
                                max="59"
                                v-model.number="sleepMinutes"
                                class="w-full rounded-xl border border-base-300 bg-base-100 px-4 py-2.5 text-sm text-center font-bold text-info focus:border-info focus:ring-1 focus:ring-info transition-colors duration-200"
                            />
                            <span
                                class="text-xs font-semibold text-base-content/60 uppercase tracking-wider"
                                >Min</span
                            >
                        </div>
                    </div>
                    <p
                        v-if="form.errors.sleep_duration"
                        class="mt-1.5 text-xs text-error font-medium"
                    >
                        {{ form.errors.sleep_duration }}
                    </p>
                </div>

                <div
                    class="rounded-2xl bg-base-200 p-4 border border-base-300 border-l-4 border-l-success shadow-sm transition-colors duration-200"
                >
                    <label
                        class="text-xs font-bold uppercase tracking-wider text-success block mb-3"
                    >
                        Select Mood Status
                    </label>
                    <div class="flex items-center justify-between px-2">
                        <button
                            type="button"
                            @click="form.mood = 1"
                            :class="[
                                'p-2 rounded-2xl border transition-all duration-200',
                                form.mood === 1
                                    ? 'border-success bg-success/20 scale-110 text-success shadow-md'
                                    : 'border-transparent text-base-content/30 hover:text-base-content/60',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="h-8 w-8"
                            >
                                <circle cx="12" cy="12" r="9" />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 10h.01M15 10h.01M16 16.5a4 4 0 0 0-8 0"
                                />
                            </svg>
                        </button>

                        <button
                            type="button"
                            @click="form.mood = 2"
                            :class="[
                                'p-2 rounded-2xl border transition-all duration-200',
                                form.mood === 2
                                    ? 'border-success bg-success/20 scale-110 text-success shadow-md'
                                    : 'border-transparent text-base-content/30 hover:text-base-content/60',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="h-8 w-8"
                            >
                                <circle cx="12" cy="12" r="9" />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 10h.01M15 10h.01M8 15h8"
                                />
                            </svg>
                        </button>

                        <button
                            type="button"
                            @click="form.mood = 3"
                            :class="[
                                'p-2 rounded-2xl border transition-all duration-200',
                                form.mood === 3
                                    ? 'border-success bg-success/20 scale-110 text-success shadow-md'
                                    : 'border-transparent text-base-content/30 hover:text-base-content/60',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="h-8 w-8"
                            >
                                <circle cx="12" cy="12" r="9" />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 10h.01M15 10h.01M8 14.5a4 4 0 0 0 8 0"
                                />
                            </svg>
                        </button>

                        <button
                            type="button"
                            @click="form.mood = 4"
                            :class="[
                                'p-2 rounded-2xl border transition-all duration-200',
                                form.mood === 4
                                    ? 'border-success bg-success/20 scale-110 text-success shadow-md'
                                    : 'border-transparent text-base-content/30 hover:text-base-content/60',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="h-8 w-8"
                            >
                                <circle cx="12" cy="12" r="9" />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8 10a2 2 0 0 1 3 0M13 10a2 2 0 0 1 3 0M8 14h8c0 2.5-1.8 4.5-4 4.5S8 16.5 8 14z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-6 px-1">
                    <div
                        class="flex items-center justify-between flex-1 bg-base-200 border border-base-300 rounded-2xl p-4 transition-colors duration-200"
                    >
                        <span class="text-sm font-medium text-base-content/80"
                            >Workout</span
                        >
                        <button
                            type="button"
                            @click="form.workout = !form.workout"
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                form.workout ? 'bg-success' : 'bg-base-300',
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    form.workout
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                ]"
                            />
                        </button>
                    </div>

                    <div
                        class="flex items-center justify-between flex-1 bg-base-200 border border-base-300 rounded-2xl p-4 transition-colors duration-200"
                    >
                        <span class="text-sm font-medium text-base-content/80"
                            >Cardio</span
                        >
                        <button
                            type="button"
                            @click="form.cardio = !form.cardio"
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                form.cardio ? 'bg-success' : 'bg-base-300',
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    form.cardio
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                ]"
                            />
                        </button>
                    </div>
                </div>

                <div>
                    <label
                        class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-base-content/70 mb-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-4 w-4 text-base-content/40"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                            />
                        </svg>
                        Notes
                    </label>
                    <textarea
                        rows="3"
                        v-model="form.notes"
                        class="w-full rounded-xl border border-base-300 bg-base-200 px-4 py-3 text-sm text-base-content focus:border-primary focus:ring-1 focus:ring-primary transition-colors duration-200 placeholder-base-content/40 resize-none"
                        placeholder="Sore muscles, felt good after tracking..."
                    />
                    <p
                        v-if="form.errors.notes"
                        class="mt-1.5 text-xs text-error font-medium"
                    >
                        {{ form.errors.notes }}
                    </p>
                </div>

                <div
                    class="flex items-center justify-end gap-3 pt-4 border-t border-base-300"
                >
                    <button
                        type="button"
                        @click="emit('close')"
                        class="rounded-xl border border-base-300 bg-transparent px-4 py-2.5 text-sm font-semibold text-base-content/70 hover:bg-base-200 hover:text-base-content transition-colors duration-200"
                        :disabled="form.processing"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-primary-content shadow hover:bg-primary/90 transition-colors duration-200 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? "Saving..." : "Save Entry" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
