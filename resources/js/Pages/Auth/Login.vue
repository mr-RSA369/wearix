<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />
            <!-- Heading -->
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">
                Login to your account
            </h2>

            <!-- Status Message -->
            <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <!-- Email -->
                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        :error="form.errors.email"
                        autofocus
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Password -->
                <div>
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        :error="form.errors.password"
                        autocomplete="current-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Remember + Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600">
                            Remember me
                        </span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-indigo-600 hover:underline"
                    >
                        Forgot password?
                    </Link>
                </div>

                <!-- Button -->
                <PrimaryButton
                    class="w-full justify-center py-2 text-base"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>

                <!-- Register Link -->
                <p class="text-center text-sm text-gray-600 mt-4">
                    Don’t have an account?
                    <Link
                        :href="route('register')"
                        class="text-indigo-600 font-medium hover:underline"
                    >
                        Register here
                    </Link>
                </p>

            </form>

            <div class="space-y-3 mt-6">

    <!-- Google -->
    <a
        href="/auth/google"
        class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-lg py-2 hover:bg-gray-100 transition"
    >
        <span class="font-medium">Continue with Google</span>
    </a>

    <!-- Facebook -->
    <a
        href="/auth/facebook"
        class="w-full flex items-center justify-center gap-3 bg-blue-600 text-white rounded-lg py-2 hover:bg-blue-700 transition"
    >
        <span class="font-medium">Continue with Facebook</span>
    </a>

</div>
    </GuestLayout>
</template>
