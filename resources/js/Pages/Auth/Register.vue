<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">
                Register for an account
        </h2>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    :error="form.errors.name"
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    :error="form.errors.email"
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    :error="form.errors.password"
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    :error="form.errors.password_confirmation"
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex flex-col text-center items-center justify-end">
                <PrimaryButton
                    class="w-full justify-center py-2 text-base"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>

                <!-- Register Link -->
                <p class="text-center text-sm text-gray-600 mt-4">
                    Already have an account?
                    <Link
                        :href="route('login')"
                        class="text-indigo-600 font-medium hover:underline"
                    >
                        Log in here
                    </Link>
                </p>

            </div>
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
