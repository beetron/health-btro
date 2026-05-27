<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

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
                            Create an Account
                        </h2>
                        <p class="text-sm text-base-content/60 mt-1">
                            Sign up to start tracking your health stats
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="form-control w-full">
                            <InputLabel
                                for="name"
                                value="Full Name"
                                class="label label-text font-medium"
                            />

                            <TextInput
                                id="name"
                                type="text"
                                class="input input-bordered w-full focus:input-primary mt-1"
                                :class="{ 'input-error': form.errors.name }"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="John Doe"
                            />

                            <InputError
                                class="mt-1.5 text-xs text-error"
                                :message="form.errors.name"
                            />
                        </div>

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
                                autocomplete="username"
                                placeholder="name@example.com"
                            />

                            <InputError
                                class="mt-1.5 text-xs text-error"
                                :message="form.errors.email"
                            />
                        </div>

                        <div class="form-control w-full">
                            <InputLabel
                                for="password"
                                value="Password"
                                class="label label-text font-medium"
                            />

                            <TextInput
                                id="password"
                                type="password"
                                class="input input-bordered w-full focus:input-primary mt-1"
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
                                value="Confirm Password"
                                class="label label-text font-medium"
                            />

                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="input input-bordered w-full focus:input-primary mt-1"
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
                                class="btn btn-primary w-full normal-case"
                                :class="{ loading: form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing">Register</span>
                            </PrimaryButton>
                        </div>

                        <div
                            class="text-center text-sm text-base-content/60 pt-2"
                        >
                            Already registered?
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
