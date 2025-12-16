<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, Pencil, Trash2, MoreHorizontal, Tags, UtensilsCrossed } from 'lucide-vue-next';

// Layout
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Kategori', href: route('categories.index') },
];

// Types
interface Category {
    id: number;
    name: string;
    menus_count: number;
}

// Props
const props = defineProps<{
    categories: Category[];
}>();

// State
const isDialogOpen = ref(false);
const editingCategory = ref<Category | null>(null);
const deleteConfirmId = ref<number | null>(null);

// Form
const form = useForm({
    name: '',
});

// Computed
const dialogTitle = computed(() => editingCategory.value ? 'Edit Kategori' : 'Tambah Kategori');

// Methods
const openCreateDialog = () => {
    editingCategory.value = null;
    form.reset();
    isDialogOpen.value = true;
};

const openEditDialog = (category: Category) => {
    editingCategory.value = category;
    form.name = category.name;
    isDialogOpen.value = true;
};

const submitForm = () => {
    if (editingCategory.value) {
        router.put(route('categories.update', { category: editingCategory.value.id }), {
            name: form.name,
        }, {
            onSuccess: () => {
                isDialogOpen.value = false;
                form.reset();
            },
        });
    } else {
        router.post(route('categories.store'), {
            name: form.name,
        }, {
            onSuccess: () => {
                isDialogOpen.value = false;
                form.reset();
            },
        });
    }
};

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const deleteCategory = () => {
    if (deleteConfirmId.value) {
        router.delete(route('categories.destroy', { category: deleteConfirmId.value }), {
            onSuccess: () => {
                deleteConfirmId.value = null;
            },
        });
    }
};
</script>

<template>

    <Head title="Manajemen Kategori" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Manajemen Kategori</h1>
                    <p class="text-sm text-slate-500">Kelola kategori menu</p>
                </div>
                <Button class="bg-orange-500 hover:bg-orange-600" @click="openCreateDialog">
                    <Plus class="w-4 h-4 mr-2" />
                    Tambah Kategori
                </Button>
            </div>

            <!-- Table -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nama Kategori</TableHead>
                            <TableHead class="text-center">Jumlah Menu</TableHead>
                            <TableHead class="text-right w-20">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="categories.length === 0">
                            <TableCell colspan="3" class="text-center py-12">
                                <div class="flex flex-col items-center gap-2">
                                    <Tags class="w-12 h-12 text-slate-300" />
                                    <p class="text-slate-500">Belum ada kategori</p>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="category in categories" :key="category.id">
                            <TableCell class="font-medium">
                                <div class="flex items-center gap-2">
                                    <Tags class="w-4 h-4 text-orange-500" />
                                    {{ category.name }}
                                </div>
                            </TableCell>
                            <TableCell class="text-center">
                                <Badge variant="outline">
                                    {{ category.menus_count }} menu
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            <MoreHorizontal class="w-4 h-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem @click="openEditDialog(category)">
                                            <Pencil class="w-4 h-4 mr-2" />
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuItem v-if="category.menus_count === 0" class="text-red-600"
                                            @click="confirmDelete(category.id)">
                                            <Trash2 class="w-4 h-4 mr-2" />
                                            Hapus
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription class="sr-only">
                        Tambahkan atau ubah nama kategori menu.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="name">Nama Kategori</Label>
                        <Input id="name" v-model="form.name" placeholder="Contoh: Makanan Berat" required />
                    </div>

                    <DialogFooter class="gap-2">
                        <Button type="button" variant="outline" @click="isDialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" class="bg-orange-500 hover:bg-orange-600" :disabled="form.processing">
                            {{ editingCategory ? 'Simpan' : 'Tambah' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog :open="deleteConfirmId !== null" @update:open="deleteConfirmId = null">
            <DialogContent class="sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle>Hapus Kategori?</DialogTitle>
                    <DialogDescription class="sr-only">
                        Konfirmasi penghapusan kategori.
                    </DialogDescription>
                </DialogHeader>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Apakah Anda yakin ingin menghapus kategori ini?
                </p>
                <DialogFooter class="gap-2">
                    <Button variant="outline" @click="deleteConfirmId = null">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="deleteCategory">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
