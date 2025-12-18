<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    LayoutGrid,
    Store,
    ChartPie,
    User,
    History,
    Menu as MenuIcon,
    Users,
    Tags,
    LogOut,
    Settings,
    ChevronRight,
} from 'lucide-vue-next';
import {
    Sheet,
    SheetContent,
    SheetTrigger,
} from '@/components/ui/sheet';

const page = usePage();
const user = computed(() => (page.props.auth as { user: { role?: string; name: string; email: string } })?.user);
const isAdmin = computed(() => user.value?.role === 'admin');
const isOpen = ref(false);

// Helper to safely get route
const getRoute = (name: string) => {
    try {
        return route(name);
    } catch (e) {
        console.error(`Route ${name} not found`, e);
        return '#';
    }
};

const handleSheetClose = () => {
    isOpen.value = false;
};
</script>

<template>
    <nav
        class="fixed bottom-0 left-0 right-0 z-[100] h-20 pb-safe bg-zinc-950/80 backdrop-blur-xl border-t border-white/5 flex justify-between items-center px-6 shadow-[0_-5px_20px_rgba(0,0,0,0.3)] md:hidden">
        <!-- ADMIN NAVIGATION -->
        <template v-if="isAdmin">
            <Link :href="getRoute('dashboard')"
                class="flex flex-col items-center justify-center gap-1 transition-colors hover:text-zinc-300 w-full"
                :class="route().current('dashboard') ? 'text-orange-500' : 'text-zinc-500'">
                <LayoutGrid class="w-6 h-6 transition-transform active:scale-95"
                    :class="route().current('dashboard') ? 'stroke-[2.5]' : 'stroke-2'" />
                <span class="text-[10px] font-medium mt-1">Dashboard</span>
            </Link>

            <Link :href="getRoute('history.index')"
                class="flex flex-col items-center justify-center gap-1 transition-colors hover:text-zinc-300 w-full"
                :class="route().current('history.*') ? 'text-orange-500' : 'text-zinc-500'">
                <History class="w-6 h-6 transition-transform active:scale-95"
                    :class="route().current('history.*') ? 'stroke-[2.5]' : 'stroke-2'" />
                <span class="text-[10px] font-medium mt-1">Riwayat</span>
            </Link>

            <!-- POS BUTTON -->
            <Link :href="getRoute('pos.index')" class="group relative -translate-y-6">
                <div
                    class="w-14 h-14 rounded-full bg-gradient-to-tr from-orange-600 to-orange-400 text-white shadow-lg shadow-orange-500/30 flex items-center justify-center border-4 border-zinc-950 transition-transform active:scale-95 group-hover:scale-105">
                    <Store class="w-6 h-6" />
                </div>
            </Link>

            <Link :href="getRoute('reports.index')"
                class="flex flex-col items-center justify-center gap-1 transition-colors hover:text-zinc-300 w-full"
                :class="route().current('reports.*') ? 'text-orange-500' : 'text-zinc-500'">
                <ChartPie class="w-6 h-6 transition-transform active:scale-95"
                    :class="route().current('reports.*') ? 'stroke-[2.5]' : 'stroke-2'" />
                <span class="text-[10px] font-medium mt-1">Laporan</span>
            </Link>

            <!-- MORE MENU (SHEET) -->
            <Sheet v-model:open="isOpen">
                <SheetTrigger as-child>
                    <button
                        class="flex flex-col items-center justify-center gap-1 transition-colors hover:text-zinc-300 w-full"
                        :class="isOpen ? 'text-orange-500' : 'text-zinc-500'">
                        <MenuIcon class="w-6 h-6 transition-transform active:scale-95"
                            :class="isOpen ? 'stroke-[2.5]' : 'stroke-2'" />
                        <span class="text-[10px] font-medium mt-1">Lainnya</span>
                    </button>
                </SheetTrigger>
                <SheetContent side="bottom"
                    class="h-[85vh] rounded-t-[2rem] border-zinc-800 bg-zinc-950 text-white p-0">
                    <div class="p-6 pb-2 border-b border-zinc-800/50">
                        <div class="flex items-center justify-center mb-6">
                            <div class="w-12 h-1 bg-zinc-800 rounded-full"></div>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center text-orange-500 font-bold text-xl">
                                {{ user?.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">{{ user?.name }}</h3>
                                <p class="text-sm text-zinc-500">{{ user?.email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 space-y-2 overflow-y-auto max-h-[calc(85vh-140px)]">
                        <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2 px-2">Menu Utama</div>

                        <Link :href="getRoute('categories.index')" @click="handleSheetClose"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-zinc-900 active:bg-zinc-800 transition-colors text-zinc-200">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-blue-500/10 text-blue-500">
                                    <Tags class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Kategori Menu</span>
                            </div>
                            <ChevronRight class="w-4 h-4 text-zinc-600" />
                        </Link>

                        <Link :href="getRoute('users.index')" @click="handleSheetClose"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-zinc-900 active:bg-zinc-800 transition-colors text-zinc-200">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-green-500/10 text-green-500">
                                    <Users class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Kelola Pegawai</span>
                            </div>
                            <ChevronRight class="w-4 h-4 text-zinc-600" />
                        </Link>

                        <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mt-6 mb-2 px-2">Pengaturan
                        </div>

                        <Link :href="getRoute('profile.edit')" @click="handleSheetClose"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-zinc-900 active:bg-zinc-800 transition-colors text-zinc-200">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-zinc-800 text-zinc-400">
                                    <Settings class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Profil & Akun</span>
                            </div>
                            <ChevronRight class="w-4 h-4 text-zinc-600" />
                        </Link>

                        <Link method="post" as="button" :href="getRoute('logout')"
                            class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-red-500/10 active:bg-red-500/20 transition-colors text-red-400">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-red-500/10 text-red-500">
                                    <LogOut class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Keluar Aplikasi</span>
                            </div>
                        </Link>
                    </div>
                </SheetContent>
            </Sheet>
        </template>

        <!-- CASHIER NAVIGATION -->
        <template v-else>
            <Link :href="getRoute('history.index')"
                class="flex flex-col items-center justify-center gap-1 transition-colors hover:text-zinc-300 w-full"
                :class="route().current('history.*') ? 'text-orange-500' : 'text-zinc-500'">
                <History class="w-6 h-6 transition-transform active:scale-95"
                    :class="route().current('history.*') ? 'stroke-[2.5]' : 'stroke-2'" />
                <span class="text-[10px] font-medium mt-1">Riwayat</span>
            </Link>

            <!-- POS BUTTON -->
            <Link :href="getRoute('pos.index')" class="group relative -translate-y-6">
                <div
                    class="w-14 h-14 rounded-full bg-gradient-to-tr from-orange-600 to-orange-400 text-white shadow-lg shadow-orange-500/30 flex items-center justify-center border-4 border-zinc-950 transition-transform active:scale-95 group-hover:scale-105">
                    <Store class="w-6 h-6" />
                </div>
            </Link>

            <!-- MORE MENU (SHEET) -->
            <Sheet v-model:open="isOpen">
                <SheetTrigger as-child>
                    <button
                        class="flex flex-col items-center justify-center gap-1 transition-colors hover:text-zinc-300 w-full"
                        :class="isOpen ? 'text-orange-500' : 'text-zinc-500'">
                        <User class="w-6 h-6 transition-transform active:scale-95"
                            :class="isOpen ? 'stroke-[2.5]' : 'stroke-2'" />
                        <span class="text-[10px] font-medium mt-1">Profil</span>
                    </button>
                </SheetTrigger>
                <SheetContent side="bottom"
                    class="h-[60vh] rounded-t-[2rem] border-zinc-800 bg-zinc-950 text-white p-0">
                    <div class="p-6 pb-2 border-b border-zinc-800/50">
                        <div class="flex items-center justify-center mb-6">
                            <div class="w-12 h-1 bg-zinc-800 rounded-full"></div>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center text-orange-500 font-bold text-xl">
                                {{ user?.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">{{ user?.name }}</h3>
                                <p class="text-sm text-zinc-500">{{ user?.email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 space-y-2">
                        <Link :href="getRoute('profile.edit')" @click="handleSheetClose"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-zinc-900 active:bg-zinc-800 transition-colors text-zinc-200">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-zinc-800 text-zinc-400">
                                    <Settings class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Pengaturan Akun</span>
                            </div>
                            <ChevronRight class="w-4 h-4 text-zinc-600" />
                        </Link>

                        <Link method="post" as="button" :href="getRoute('logout')"
                            class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-red-500/10 active:bg-red-500/20 transition-colors text-red-400">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-red-500/10 text-red-500">
                                    <LogOut class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Keluar Aplikasi</span>
                            </div>
                        </Link>
                    </div>
                </SheetContent>
            </Sheet>
        </template>
    </nav>
</template>

<style scoped>
.pb-safe {
    padding-bottom: env(safe-area-inset-bottom, 20px);
}
</style>
