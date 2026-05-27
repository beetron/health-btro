<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div
            class="flex min-h-[calc(100vh-12rem)] items-center justify-center px-4 sm:px-6 lg:px-8"
        >
            <div
                class="card w-full max-w-md bg-base-100 shadow-xl border border-base-200"
            >
                <div class="card-body gap-4 p-6 sm:p-8">
                    <div class="text-center mb-2">
                        <h2
                            class="text-2xl font-bold tracking-tight text-base-content"
                        >
                            Forgot Password
                        </h2>
                        <p class="text-sm text-base-content/60 mt-1">
                            Enter your email address and we will send you a link
                            to reset your password.
                        </p>
                    </div>

                    <div
                        v-if="status"
                        class="alert alert-success text-sm py-3 shadow-sm mb-2"
                    >
                        <span>{{ status }}</span>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="form-control w-full">
                            <InputLabel
                                for="email"
                                value="Email Address"
                                class="label label-text font-medium"
                            />

                            <TextInput
                                id="email"
                                type="email"
                                class="input input-bordered w-full focus:input-primary mt-1"
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

                        <div class="form-control mt-6">
                            <PrimaryButton
                                class="btn btn-primary w-full normal-case"
                                :class="{ loading: form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing"
                                    >Email Password Reset Link</span
                                >
                            </PrimaryButton>
                        </div>

                        <div
                            class="text-center text-sm text-base-content/60 pt-2"
                        >
                            Remember your password?
                            <Link
                                :href="route('home')"
                                class="link link-hover link-primary font-semibold ml-1"
                            >
                                Log in here
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
