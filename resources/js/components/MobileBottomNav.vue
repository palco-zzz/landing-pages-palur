<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    LayoutGrid,
    UtensilsCrossed,
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
        class="fixed bottom-0 left-0 right-0 z-50 h-16 bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 flex justify-around items-center px-2 pb-safe md:hidden">
        <!-- ADMIN NAVIGATION -->
        <template v-if="isAdmin">
            <Link :href="getRoute('dashboard')"
                class="flex flex-col items-center justify-center gap-1 transition-colors w-full"
                :class="route().current('dashboard') ? 'text-amber-500' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'">
                <LayoutGrid class="w-6 h-6 transition-transform active:scale-95"
                    :class="route().current('dashboard') ? 'stroke-[2.5]' : 'stroke-2'" />
                <span class="text-[10px] font-medium mt-1">Home</span>
            </Link>

            <Link :href="getRoute('history.index')"
                class="flex flex-col items-center justify-center gap-1 transition-colors w-full"
                :class="route().current('history.*') ? 'text-amber-500' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'">
                <History class="w-6 h-6 transition-transform active:scale-95"
                    :class="route().current('history.*') ? 'stroke-[2.5]' : 'stroke-2'" />
                <span class="text-[10px] font-medium mt-1">Riwayat</span>
            </Link>

            <!-- POS BUTTON -->
            <Link :href="getRoute('pos.index')" class="group relative -translate-y-6">
                <div
                    class="w-14 h-14 rounded-full bg-amber-500 text-white shadow-md flex items-center justify-center border-4 border-white dark:border-zinc-950 transition-transform active:scale-95 group-hover:scale-105">
                    <Store class="w-6 h-6" />
                </div>
            </Link>

            <Link :href="getRoute('reports.index')"
                class="flex flex-col items-center justify-center gap-1 transition-colors w-full"
                :class="route().current('reports.*') ? 'text-amber-500' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'">
                <ChartPie class="w-6 h-6 transition-transform active:scale-95"
                    :class="route().current('reports.*') ? 'stroke-[2.5]' : 'stroke-2'" />
                <span class="text-[10px] font-medium mt-1">Laporan</span>
            </Link>

            <!-- MORE MENU (SHEET) -->
            <Sheet v-model:open="isOpen">
                <SheetTrigger as-child>
                    <button class="flex flex-col items-center justify-center gap-1 transition-colors w-full"
                        :class="isOpen ? 'text-amber-500' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'">
                        <MenuIcon class="w-6 h-6 transition-transform active:scale-95"
                            :class="isOpen ? 'stroke-[2.5]' : 'stroke-2'" />
                        <span class="text-[10px] font-medium mt-1">Lainnya</span>
                    </button>
                </SheetTrigger>
                <SheetContent side="bottom"
                    class="h-[85vh] rounded-t-[2rem] border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 p-0 user-select-none">
                    <div class="p-6 pb-2 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center justify-center mb-6">
                            <div class="w-12 h-1 bg-zinc-200 dark:bg-zinc-800 rounded-full"></div>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-12 h-12 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-500 font-bold text-xl">
                                {{ user?.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">{{ user?.name }}</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ user?.email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 space-y-2 overflow-y-auto max-h-[calc(85vh-140px)]">
                        <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2 px-2">Menu
                            Utama</div>

                        <Link :href="getRoute('menus.index')" @click="handleSheetClose"
                            class="flex items-center justify-between p-3 rounded-xl transition-all"
                            :class="route().current('menus.*') ? 'bg-amber-500/10 text-amber-500 font-medium border-l-2 border-amber-500' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-zinc-100'">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg"
                                    :class="route().current('menus.*') ? 'bg-amber-500/20 text-amber-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400'">
                                    <UtensilsCrossed class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Kelola Produk</span>
                            </div>
                            <ChevronRight class="w-4 h-4 text-zinc-400" />
                        </Link>

                        <Link :href="getRoute('categories.index')" @click="handleSheetClose"
                            class="flex items-center justify-between p-3 rounded-xl transition-all"
                            :class="route().current('categories.*') ? 'bg-amber-500/10 text-amber-500 font-medium border-l-2 border-amber-500' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-zinc-100'">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg"
                                    :class="route().current('categories.*') ? 'bg-amber-500/20 text-amber-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400'">
                                    <Tags class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Kategori Menu</span>
                            </div>
                            <ChevronRight class="w-4 h-4 text-zinc-400" />
                        </Link>

                        <Link :href="getRoute('users.index')" @click="handleSheetClose"
                            class="flex items-center justify-between p-3 rounded-xl transition-all"
                            :class="route().current('users.*') ? 'bg-amber-500/10 text-amber-500 font-medium border-l-2 border-amber-500' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-zinc-100'">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg"
                                    :class="route().current('users.*') ? 'bg-amber-500/20 text-amber-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400'">
                                    <Users class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Kelola Pegawai</span>
                            </div>
                            <ChevronRight class="w-4 h-4 text-zinc-400" />
                        </Link>

                        <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mt-6 mb-2 px-2">
                            Pengaturan
                        </div>

                        <Link :href="getRoute('profile.edit')" @click="handleSheetClose"
                            class="flex items-center justify-between p-3 rounded-xl transition-all"
                            :class="route().current('profile.*') ? 'bg-amber-500/10 text-amber-500 font-medium border-l-2 border-amber-500' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-zinc-100'">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg"
                                    :class="route().current('profile.*') ? 'bg-amber-500/20 text-amber-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400'">
                                    <Settings class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Profil & Akun</span>
                            </div>
                            <ChevronRight class="w-4 h-4 text-zinc-400" />
                        </Link>

                        <Link method="post" as="button" :href="getRoute('logout')"
                            class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-red-500/10 active:bg-red-500/20 transition-colors text-red-600 dark:text-red-400">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400">
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
                class="flex flex-col items-center justify-center gap-1 transition-colors w-full"
                :class="route().current('history.*') ? 'text-amber-500' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'">
                <History class="w-6 h-6 transition-transform active:scale-95"
                    :class="route().current('history.*') ? 'stroke-[2.5]' : 'stroke-2'" />
                <span class="text-[10px] font-medium mt-1">Riwayat</span>
            </Link>

            <!-- POS BUTTON -->
            <Link :href="getRoute('pos.index')" class="group relative -translate-y-6">
                <div
                    class="w-14 h-14 rounded-full bg-amber-500 text-white shadow-md flex items-center justify-center border-4 border-white dark:border-zinc-950 transition-transform active:scale-95 group-hover:scale-105">
                    <Store class="w-6 h-6" />
                </div>
            </Link>

            <!-- MORE MENU (SHEET) -->
            <Sheet v-model:open="isOpen">
                <SheetTrigger as-child>
                    <button class="flex flex-col items-center justify-center gap-1 transition-colors w-full"
                        :class="isOpen ? 'text-amber-500' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'">
                        <User class="w-6 h-6 transition-transform active:scale-95"
                            :class="isOpen ? 'stroke-[2.5]' : 'stroke-2'" />
                        <span class="text-[10px] font-medium mt-1">Profil</span>
                    </button>
                </SheetTrigger>
                <SheetContent side="bottom"
                    class="h-[60vh] rounded-t-[2rem] border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 p-0">
                    <div class="p-6 pb-2 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center justify-center mb-6">
                            <div class="w-12 h-1 bg-zinc-200 dark:bg-zinc-800 rounded-full"></div>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-12 h-12 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-500 font-bold text-xl">
                                {{ user?.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">{{ user?.name }}</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ user?.email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 space-y-2">
                        <Link :href="getRoute('profile.edit')" @click="handleSheetClose"
                            class="flex items-center justify-between p-3 rounded-xl transition-all"
                            :class="route().current('profile.*') ? 'bg-amber-500/10 text-amber-500 font-medium border-l-2 border-amber-500' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-zinc-100'">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg"
                                    :class="route().current('profile.*') ? 'bg-amber-500/20 text-amber-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400'">
                                    <Settings class="w-5 h-5" />
                                </div>
                                <span class="font-medium">Pengaturan Akun</span>
                            </div>
                            <ChevronRight class="w-4 h-4 text-zinc-400" />
                        </Link>

                        <Link method="post" as="button" :href="getRoute('logout')"
                            class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-red-500/10 active:bg-red-500/20 transition-colors text-red-600 dark:text-red-400">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400">
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
