<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div
            class="flex min-h-[calc(100vh-12rem)] items-center justify-center px-4 sm:px-6 lg:px-8"
        >
            <div
                class="card w-full max-w-md bg-base-100 shadow-xl border border-base-300"
            >
                <div class="card-body gap-4 p-6 sm:p-8">
                    <div class="text-center mb-2">
                        <h2
                            class="text-2xl font-bold tracking-tight text-base-content"
                        >
                            Confirm Password
                        </h2>
                        <p class="text-sm text-base-content/60 mt-2">
                            This is a secure area of the application. Please
                            confirm your password before continuing.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="form-control w-full">
                            <InputLabel for="password" value="Password" />

                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1"
                                :class="{ 'input-error': form.errors.password }"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                autofocus
                                placeholder="••••••••"
                            />

                            <InputError
                                class="mt-1.5 text-xs text-error"
                                :message="form.errors.password"
                            />
                        </div>

                        <div class="form-control mt-6">
                            <PrimaryButton
                                class="w-full"
                                :class="{ loading: form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing"
                                    >Confirm Password</span
                                >
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
