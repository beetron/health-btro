<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

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
                            Health Stats Tracker @ bTro.net
                        </h2>
                        <p class="text-sm text-base-content/60 mt-1">
                            Sign in to your account to continue
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
                            <InputLabel for="password" value="Password" />

                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1"
                                :class="{ 'input-error': form.errors.password }"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />

                            <InputError
                                class="mt-1.5 text-xs text-error"
                                :message="form.errors.password"
                            />
                        </div>

                        <div
                            class="flex flex-wrap items-center justify-between gap-2 pt-1 text-sm"
                        >
                            <label
                                class="label cursor-pointer justify-start gap-2 py-0"
                            >
                                <Checkbox
                                    name="remember"
                                    v-model:checked="form.remember"
                                    class="checkbox-sm"
                                />
                                <span
                                    class="label-text select-none text-base-content/70"
                                >
                                    Remember me
                                </span>
                            </label>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="link link-hover link-primary text-sm font-medium"
                            >
                                Forgot password?
                            </Link>
                        </div>

                        <div class="form-control mt-6">
                            <PrimaryButton
                                class="w-full normal-case"
                                :class="{ loading: form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing">Log in</span>
                            </PrimaryButton>
                        </div>

                        <div
                            class="text-center text-sm text-base-content/60 pt-2"
                        >
                            Not registered yet?
                            <Link
                                :href="route('register')"
                                class="link link-hover link-primary font-semibold ml-1"
                            >
                                Create an account
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
