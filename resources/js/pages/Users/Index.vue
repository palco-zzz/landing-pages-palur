<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, Pencil, Trash2, MoreHorizontal, Users, UserCog } from 'lucide-vue-next';

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

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kelola Pegawai', href: route('users.index') },
];

// Types
interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'cashier';
    created_at: string;
}

// Props
const props = defineProps<{
    users: User[];
}>();

// Current user
const page = usePage();
const currentUserId = computed(() => (page.props.auth as { user: { id: number } })?.user?.id);

// State
const isDialogOpen = ref(false);
const editingUser = ref<User | null>(null);
const deleteConfirmId = ref<number | null>(null);

// Form
const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'cashier' as 'admin' | 'cashier',
});

// Computed
const dialogTitle = computed(() => editingUser.value ? 'Edit Pengguna' : 'Tambah Pengguna');

const roleLabels: Record<string, string> = {
    admin: 'Admin',
    cashier: 'Kasir',
};

const roleColors: Record<string, string> = {
    admin: 'bg-destructive/10 text-destructive border-destructive/20',
    cashier: 'bg-blue-500/10 text-blue-600 border-blue-500/20',
};

// Methods
const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const openCreateDialog = () => {
    editingUser.value = null;
    form.reset();
    form.role = 'cashier';
    isDialogOpen.value = true;
};

const openEditDialog = (user: User) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.role = user.role;
    isDialogOpen.value = true;
};

const submitForm = () => {
    if (editingUser.value) {
        // Update
        router.put(route('users.update', { user: editingUser.value.id }), {
            name: form.name,
            email: form.email,
            password: form.password || undefined,
            role: form.role,
        }, {
            onSuccess: () => {
                isDialogOpen.value = false;
                form.reset();
            },
        });
    } else {
        // Create
        router.post(route('users.store'), {
            name: form.name,
            email: form.email,
            password: form.password,
            role: form.role,
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

const deleteUser = () => {
    if (deleteConfirmId.value) {
        router.delete(route('users.destroy', { user: deleteConfirmId.value }), {
            onSuccess: () => {
                deleteConfirmId.value = null;
            },
        });
    }
};
</script>

<template>

    <Head title="Kelola Pegawai" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Manajemen Pengguna</h1>
                    <p class="text-sm text-muted-foreground">Kelola akun admin dan kasir</p>
                </div>
                <Button class="bg-primary text-primary-foreground hover:bg-primary/90 shadow-md"
                    @click="openCreateDialog">
                    <Plus class="w-4 h-4 mr-2" />
                    Tambah User
                </Button>
            </div>

            <!-- Table -->
            <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Role</TableHead>
                                <TableHead>Dibuat</TableHead>
                                <TableHead class="text-right w-20">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="users.length === 0">
                                <TableCell colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-2">
                                        <Users class="w-12 h-12 text-muted-foreground/20" />
                                        <p class="text-muted-foreground">Belum ada pengguna</p>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="user in users" :key="user.id">
                                <TableCell class="font-medium">
                                    <div class="flex items-center gap-2">
                                        {{ user.name }}
                                        <Badge v-if="user.id === currentUserId" variant="outline"
                                            class="text-xs border-primary/20 text-primary bg-primary/5">
                                            Anda
                                        </Badge>
                                    </div>
                                </TableCell>
                                <TableCell class="text-muted-foreground">{{ user.email }}</TableCell>
                                <TableCell>
                                    <Badge variant="outline" :class="roleColors[user.role]">
                                        {{ roleLabels[user.role] }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-sm text-muted-foreground">
                                    {{ formatDate(user.created_at) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" size="icon">
                                                <MoreHorizontal class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem @click="openEditDialog(user)">
                                                <Pencil class="w-4 h-4 mr-2" />
                                                Edit
                                            </DropdownMenuItem>
                                            <DropdownMenuItem v-if="user.id !== currentUserId"
                                                class="text-destructive focus:text-destructive focus:bg-destructive/10"
                                                @click="confirmDelete(user.id)">
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
                        Kelola data akses pegawai di sini.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Name -->
                    <div class="space-y-2">
                        <Label for="name">Nama</Label>
                        <Input id="name" v-model="form.name" placeholder="Nama lengkap" required />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" placeholder="email@example.com" required />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <Label for="password">Password</Label>
                        <Input id="password" v-model="form.password" type="password" placeholder="Minimal 8 karakter"
                            :required="!editingUser" />
                        <p v-if="editingUser" class="text-xs text-muted-foreground">
                            Kosongkan jika tidak ingin mengganti password
                        </p>
                    </div>

                    <!-- Role -->
                    <div class="space-y-2">
                        <Label>Role</Label>
                        <Select v-model="form.role">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="admin">Admin</SelectItem>
                                <SelectItem value="cashier">Kasir</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <DialogFooter class="gap-2">
                        <Button type="button" variant="outline" @click="isDialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" class="bg-primary text-primary-foreground hover:bg-primary/90"
                            :disabled="form.processing">
                            {{ editingUser ? 'Simpan Perubahan' : 'Tambah User' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog :open="deleteConfirmId !== null" @update:open="deleteConfirmId = null">
            <DialogContent class="sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle>Hapus Pengguna?</DialogTitle>
                    <DialogDescription class="sr-only">
                        Konfirmasi penghapusan pengguna.
                    </DialogDescription>
                </DialogHeader>
                <p class="text-sm text-muted-foreground">
                    Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <DialogFooter class="gap-2">
                    <Button variant="outline" @click="deleteConfirmId = null">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="deleteUser">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
