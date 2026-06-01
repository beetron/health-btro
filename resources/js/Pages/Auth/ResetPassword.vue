<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

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
                            Reset Password
                        </h2>
                        <p class="text-sm text-base-content/60 mt-1">
                            Set up your new credentials to access your account
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="form-control w-full">
                            <InputLabel for="email" value="Email Address" />

                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1"
                                :class="{ 'input-error': form.errors.email }"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="name@example.com"
                            />

                            <InputError
                                class="mt-1.5 text-xs text-error"
                                :message="form.errors.email"
                            />
                        </div>

                        <div class="form-control w-full">
                            <InputLabel for="password" value="New Password" />

                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1"
                                :class="{ 'input-error': form.errors.password }"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />

                            <InputError
                                class="mt-1.5 text-xs text-error"
                                :message="form.errors.password"
                            />
                        </div>

                        <div class="form-control w-full">
                            <InputLabel
                                for="password_confirmation"
                                value="Confirm New Password"
                            />

                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1"
                                :class="{
                                    'input-error':
                                        form.errors.password_confirmation,
                                }"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />

                            <InputError
                                class="mt-1.5 text-xs text-error"
                                :message="form.errors.password_confirmation"
                            />
                        </div>

                        <div class="form-control mt-6">
                            <PrimaryButton
                                class="w-full"
                                :class="{ loading: form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing"
                                    >Reset Password</span
                                >
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
