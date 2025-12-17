<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';

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

    <div class="min-h-screen bg-muted/50 flex items-center justify-center p-4">
        <!-- Centered Card -->
        <div class="bg-card text-card-foreground border border-border shadow-sm rounded-lg p-8 w-full max-w-md">

            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <img src="/images/logo-bakmi-palur.png" alt="Logo" class="h-20 mx-auto mb-4 object-contain" />
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Bakmi Jowo Palur
                </h1>
                <p class="text-sm text-muted-foreground mt-2">
                    Masuk ke sistem kasir untuk memulai
                </p>
            </div>

            <!-- Status Message -->
            <div v-if="status"
                class="mb-6 p-3 rounded-md bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-400 text-sm font-medium text-center">
                {{ status }}
            </div>

            <!-- Login Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Email -->
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" v-model="form.email" required autofocus autocomplete="username"
                        placeholder="nama@email.com" />
                    <p v-if="form.errors.email" class="text-sm text-destructive font-medium">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm font-medium text-primary hover:underline hover:text-primary/90">
                            Lupa password?
                        </Link>
                    </div>
                    <Input id="password" type="password" v-model="form.password" required
                        autocomplete="current-password" placeholder="••••••••" />
                    <p v-if="form.errors.password" class="text-sm text-destructive font-medium">
                        {{ form.errors.password }}
                    </p>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center space-x-2">
                    <Checkbox id="remember" :checked="form.remember" @update:checked="form.remember = $event" />
                    <Label for="remember" class="font-normal cursor-pointer">Ingat saya</Label>
                </div>

                <!-- Submit Button -->
                <Button type="submit"
                    class="w-full bg-primary text-primary-foreground hover:bg-primary/90 rounded-lg h-11 text-base font-semibold"
                    :disabled="form.processing">
                    <template v-if="form.processing">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Memproses...
                    </template>
                    <template v-else>
                        Masuk
                    </template>
                </Button>
            </form>

            <!-- Footer -->
            <div class="mt-8 text-center text-xs text-muted-foreground">
                &copy; {{ new Date().getFullYear() }} Bakmi Jowo Palur. All rights reserved.
            </div>
        </div>
    </div>
</template>
