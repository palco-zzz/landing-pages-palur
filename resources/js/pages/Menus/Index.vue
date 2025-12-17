<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, Pencil, Trash2, MoreHorizontal, UtensilsCrossed, ImageIcon } from 'lucide-vue-next';

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
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Menu', href: route('menus.index') },
];

// Types
interface Category {
    id: number;
    name: string;
}

interface Menu {
    id: number;
    name: string;
    category_id: number;
    category: Category | null;
    price: number;
    image: string | null;
}

// Props
const props = defineProps<{
    menus: Menu[];
    categories: Category[];
}>();

// State
const isDialogOpen = ref(false);
const editingMenu = ref<Menu | null>(null);
const imagePreview = ref<string | null>(null);
const deleteConfirmId = ref<number | null>(null);

// Form
const form = useForm({
    name: '',
    category_id: '' as string | number,
    price: 0,
    image: null as File | null,
});

// Computed
const dialogTitle = computed(() => editingMenu.value ? 'Edit Menu' : 'Tambah Menu');

// Methods
const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const openCreateDialog = () => {
    editingMenu.value = null;
    form.reset();
    form.category_id = props.categories[0]?.id || '';
    imagePreview.value = null;
    isDialogOpen.value = true;
};

const openEditDialog = (menu: Menu) => {
    editingMenu.value = menu;
    form.name = menu.name;
    form.category_id = menu.category_id;
    form.price = menu.price;
    form.image = null;
    imagePreview.value = menu.image;
    isDialogOpen.value = true;
};

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    if (editingMenu.value) {
        router.post(route('menus.update', { menu: editingMenu.value.id }), {
            _method: 'PUT',
            name: form.name,
            category_id: form.category_id,
            price: form.price,
            image: form.image,
        }, {
            forceFormData: true,
            onSuccess: () => {
                isDialogOpen.value = false;
                form.reset();
            },
        });
    } else {
        router.post(route('menus.store'), {
            name: form.name,
            category_id: form.category_id,
            price: form.price,
            image: form.image,
        }, {
            forceFormData: true,
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

const deleteMenu = () => {
    if (deleteConfirmId.value) {
        router.delete(route('menus.destroy', { menu: deleteConfirmId.value }), {
            onSuccess: () => {
                deleteConfirmId.value = null;
            },
        });
    }
};
</script>

<template>

    <Head title="Manajemen Menu" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Daftar Menu</h1>
                    <p class="text-sm text-muted-foreground">Kelola menu makanan dan minuman</p>
                </div>
                <Button class="bg-primary text-primary-foreground hover:bg-primary/90 shadow-md"
                    @click="openCreateDialog">
                    <Plus class="w-4 h-4 mr-2" />
                    Tambah Menu
                </Button>
            </div>

            <!-- Table -->
            <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-16">Gambar</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Kategori</TableHead>
                                <TableHead class="text-right">Harga</TableHead>
                                <TableHead class="text-right w-20">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="menus.length === 0">
                                <TableCell colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-2">
                                        <UtensilsCrossed class="w-12 h-12 text-muted-foreground/20" />
                                        <p class="text-muted-foreground">Belum ada menu</p>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="menu in menus" :key="menu.id">
                                <TableCell>
                                    <Avatar class="w-12 h-12 rounded-lg">
                                        <AvatarImage v-if="menu.image" :src="menu.image" :alt="menu.name" />
                                        <AvatarFallback class="rounded-lg bg-muted">
                                            <UtensilsCrossed class="w-5 h-5 text-muted-foreground" />
                                        </AvatarFallback>
                                    </Avatar>
                                </TableCell>
                                <TableCell class="font-medium text-foreground">{{ menu.name }}</TableCell>
                                <TableCell>
                                    <Badge variant="outline" class="bg-primary/10 text-primary border-primary/20">
                                        {{ menu.category?.name || 'Tidak ada' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right font-medium">
                                    Rp {{ formatPrice(menu.price) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" size="icon">
                                                <MoreHorizontal class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem @click="openEditDialog(menu)">
                                                <Pencil class="w-4 h-4 mr-2" />
                                                Edit
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                class="text-destructive focus:text-destructive focus:bg-destructive/10"
                                                @click="confirmDelete(menu.id)">
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
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription class="sr-only">
                        Isi detail menu baru atau edit menu yang ada.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Name -->
                    <div class="space-y-2">
                        <Label for="name">Nama Menu</Label>
                        <Input id="name" v-model="form.name" placeholder="Contoh: Bakmi Goreng Spesial" required />
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <Label>Kategori</Label>
                        <Select v-model="form.category_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih kategori" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Price -->
                    <div class="space-y-2">
                        <Label for="price">Harga (Rp)</Label>
                        <Input id="price" v-model.number="form.price" type="number" min="0" placeholder="15000"
                            required />
                    </div>

                    <!-- Image -->
                    <div class="space-y-2">
                        <Label>Gambar</Label>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-20 h-20 rounded-lg border-2 border-dashed border-input flex items-center justify-center overflow-hidden bg-muted/30">
                                <img v-if="imagePreview" :src="imagePreview" alt="Preview"
                                    class="w-full h-full object-cover" />
                                <ImageIcon v-else class="w-8 h-8 text-muted-foreground" />
                            </div>
                            <div class="flex-1">
                                <Input type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
                                    @change="handleImageChange" />
                                <p class="text-xs text-muted-foreground mt-1">JPG, PNG, atau WebP. Maks 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <DialogFooter class="gap-2">
                        <Button type="button" variant="outline" @click="isDialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" class="bg-primary text-primary-foreground hover:bg-primary/90"
                            :disabled="form.processing">
                            {{ editingMenu ? 'Simpan Perubahan' : 'Tambah Menu' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog :open="deleteConfirmId !== null" @update:open="deleteConfirmId = null">
            <DialogContent class="sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle>Hapus Menu?</DialogTitle>
                    <DialogDescription class="sr-only">
                        Konfirmasi penghapusan menu.
                    </DialogDescription>
                </DialogHeader>
                <p class="text-sm text-muted-foreground">
                    Apakah Anda yakin ingin menghapus menu ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <DialogFooter class="gap-2">
                    <Button variant="outline" @click="deleteConfirmId = null">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="deleteMenu">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
