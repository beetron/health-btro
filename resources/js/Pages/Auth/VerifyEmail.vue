<script setup lang="ts">
import { computed } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent",
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

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
                            Verify Your Email
                        </h2>
                        <p
                            class="text-sm text-base-content/60 mt-2 text-left sm:text-center"
                        >
                            Thanks for signing up! Before getting started, could
                            you verify your email address by clicking on the
                            link we just emailed to you? If you didn't receive
                            the email, we will gladly send you another.
                        </p>
                    </div>

                    <div
                        v-if="verificationLinkSent"
                        class="alert alert-success text-sm py-3 shadow-sm mb-2"
                    >
                        <span
                            >A new verification link has been sent to the email
                            address you provided during registration.</span
                        >
                    </div>

                    <form @submit.prevent="submit" class="mt-2">
                        <div
                            class="flex flex-col sm:flex-row items-center justify-between gap-4"
                        >
                            <PrimaryButton
                                class="btn btn-primary w-full sm:w-auto normal-case"
                                :class="{ loading: form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing"
                                    >Resend Verification Email</span
                                >
                            </PrimaryButton>

                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="link link-hover link-secondary text-sm font-medium py-2 sm:py-0"
                            >
                                Log Out
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
