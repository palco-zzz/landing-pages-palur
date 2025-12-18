<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

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

    <Head title="Login" />

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side - Branding Section -->
        <div class="relative w-full lg:w-1/2 min-h-[30vh] lg:min-h-screen bg-cover bg-center flex items-center justify-center"
            style="background-image: url('/images/login-hero-image.jpg');">
            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-black/60"></div>

            <!-- Branding Content -->
            <div class="relative z-10 text-center px-8 py-12 lg:py-0">
                <!-- Logo -->
                <img src="/images/logo-bakmi-palur.png" alt="Mie Lethek Palur"
                    class="w-32 h-32 lg:w-48 lg:h-48 mx-auto mb-6 object-contain" />

                <!-- Brand Name -->
                <h1 class="text-2xl lg:text-4xl font-bold text-white mb-3">
                    Mie Lethek Palur
                </h1>

                <!-- Tagline -->
                <p class="text-orange-400 text-sm lg:text-lg font-medium tracking-wider">
                    Sehat • Tanpa Micin • Otentik
                </p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div
            class="w-full lg:w-1/2 min-h-[70vh] lg:min-h-screen bg-zinc-950 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Form Header -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl lg:text-3xl font-bold text-white mb-2">
                        Selamat Datang
                    </h2>
                    <p class="text-zinc-400 text-sm">
                        Masuk ke sistem kasir untuk memulai
                    </p>
                </div>

                <!-- Status Message -->
                <div v-if="status"
                    class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm text-center">
                    {{ status }}
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-zinc-300 mb-2">
                            Email
                        </label>
                        <input id="email" v-model="form.email" type="email"
                            class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            placeholder="nama@email.com" required autofocus autocomplete="username" />
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-400">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-medium text-zinc-300">
                                Password
                            </label>
                            <Link v-if="canResetPassword" :href="route('password.request')"
                                class="text-sm text-orange-500 hover:text-orange-400 transition-colors">
                                Lupa password?
                            </Link>
                        </div>
                        <input id="password" v-model="form.password" type="password"
                            class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            placeholder="••••••••" required autocomplete="current-password" />
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-400">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember" v-model="form.remember" type="checkbox"
                            class="w-4 h-4 bg-zinc-900 border-zinc-700 rounded text-orange-500 focus:ring-orange-500 focus:ring-offset-zinc-950" />
                        <label for="remember" class="ml-3 text-sm text-zinc-400">
                            Ingat saya
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="form.processing"
                        class="w-full py-3.5 px-6 bg-orange-600 hover:bg-orange-500 disabled:bg-orange-600/50 text-white font-semibold rounded-xl shadow-lg shadow-orange-600/20 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-zinc-950 transition-all duration-200">
                        <span v-if="form.processing" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Memproses...
                        </span>
                        <span v-else>Masuk</span>
                    </button>
                </form>

                <!-- Footer -->
                <p class="mt-8 text-center text-xs text-zinc-500">
                    © {{ new Date().getFullYear() }} Mie Lethek Palur. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>
