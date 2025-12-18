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

    <div class="min-h-screen flex">
        <!-- Left Column - Visual Branding (Desktop Only) -->
        <div class="hidden lg:flex w-1/2 relative bg-zinc-900 items-center justify-center overflow-hidden">
            <!-- Background Image -->
            <img src="https://images.unsplash.com/photo-1591064663354-7247a582c290?q=80&w=1374&auto=format&fit=crop"
                alt="Mie Lethek Palur" class="absolute inset-0 w-full h-full object-cover" />

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

            <!-- Decorative Amber Glow -->
            <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-amber-500/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/4 -right-24 w-64 h-64 bg-orange-500/15 rounded-full blur-3xl"></div>

            <!-- Welcome Text Content -->
            <div class="relative z-10 text-center px-12 max-w-lg">
                <!-- Logo -->
                <div class="mb-8 inline-block">
                    <div
                        class="w-24 h-24 mx-auto rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl flex items-center justify-center p-3">
                        <img src="/images/logo-bakmi-palur.png" alt="Mie Lethek Palur Logo"
                            class="w-full h-full object-contain" />
                    </div>
                </div>

                <!-- Welcome Message -->
                <h1 class="text-4xl xl:text-5xl font-bold text-white mb-4 leading-tight">
                    Selamat Datang di<br />
                    <span class="text-amber-400">Mie Lethek Palur</span>
                </h1>

                <p class="text-lg text-gray-300 mb-6">
                    Sistem Kasir Digital untuk Warung Anda
                </p>

                <!-- Tagline -->
                <div class="flex items-center justify-center gap-3">
                    <div class="w-8 h-px bg-gradient-to-r from-transparent to-amber-500"></div>
                    <p class="text-amber-400 font-medium tracking-widest uppercase text-sm">
                        Otentik • Tanpa Micin • Sehat
                    </p>
                    <div class="w-8 h-px bg-gradient-to-l from-transparent to-amber-500"></div>
                </div>
            </div>
        </div>

        <!-- Right Column - Login Form -->
        <div class="flex w-full lg:w-1/2 items-center justify-center bg-white p-8 sm:p-12 lg:p-16">
            <div class="w-full max-w-md space-y-8">
                <!-- Mobile Logo (Only visible on mobile) -->
                <div class="lg:hidden text-center mb-8">
                    <div
                        class="w-20 h-20 mx-auto rounded-xl bg-amber-50 border border-amber-100 shadow-lg flex items-center justify-center p-2 mb-4">
                        <img src="/images/logo-bakmi-palur.png" alt="Mie Lethek Palur Logo"
                            class="w-full h-full object-contain" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Mie Lethek Palur</h2>
                </div>

                <!-- Form Header -->
                <div class="text-center lg:text-left">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                        Masuk ke Akun Anda
                    </h2>
                    <p class="text-sm text-gray-500">
                        Silakan masukkan detail login Anda untuk melanjutkan.
                    </p>
                </div>

                <!-- Status Message -->
                <div v-if="status"
                    class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm text-center">
                    {{ status }}
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Email
                        </label>
                        <input id="email" v-model="form.email" type="email"
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-amber-500 focus:ring-amber-500 sm:text-sm transition-shadow"
                            placeholder="nama@email.com" required autofocus autocomplete="username" />
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Kata Sandi
                        </label>
                        <input id="password" v-model="form.password" type="password"
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-amber-500 focus:ring-amber-500 sm:text-sm transition-shadow"
                            placeholder="••••••••" required autocomplete="current-password" />
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" v-model="form.remember" type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 cursor-pointer" />
                            <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer select-none">
                                Ingat Saya
                            </label>
                        </div>
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm font-medium text-amber-600 hover:text-amber-500 transition-colors">
                            Lupa Kata Sandi?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="form.processing"
                        class="flex w-full justify-center items-center gap-2 rounded-lg bg-amber-600 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-amber-500/25 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200 uppercase tracking-wider disabled:opacity-70 disabled:cursor-not-allowed">
                        <template v-if="form.processing">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Memproses...</span>
                        </template>
                        <template v-else>
                            <span>Masuk</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </template>
                    </button>
                </form>

                <!-- Footer -->
                <p class="text-center text-xs text-gray-400 pt-4">
                    © {{ new Date().getFullYear() }} Mie Lethek Palur. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>
