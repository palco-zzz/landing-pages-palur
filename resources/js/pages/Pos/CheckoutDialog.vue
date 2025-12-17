<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Banknote, QrCode, CreditCard, Check } from 'lucide-vue-next';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog';

// Types
interface Transaction {
    id: number;
    uuid: string;
    customer_name: string;
    total_amount: number;
    status: 'unpaid' | 'paid' | 'cancelled';
}

// Props & Emits
const props = defineProps<{
    open: boolean;
    transaction: Transaction | null;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
    'success': [];
}>();

// State
const paymentMethod = ref<'cash' | 'qris'>('cash');
const cashReceived = ref<number>(0);
const isProcessing = ref(false);

// Computed
const totalAmount = computed(() => props.transaction?.total_amount || 0);

const changeAmount = computed(() => {
    if (paymentMethod.value !== 'cash') return 0;
    return cashReceived.value - totalAmount.value;
});

const isChangeNegative = computed(() => changeAmount.value < 0);

const canConfirm = computed(() => {
    if (!props.transaction) return false;
    if (paymentMethod.value === 'cash') {
        return cashReceived.value >= totalAmount.value;
    }
    return true; // QRIS doesn't need cash validation
});

// Methods
const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const setQuickCash = (amount: number | 'exact') => {
    if (amount === 'exact') {
        cashReceived.value = totalAmount.value;
    } else {
        cashReceived.value = amount;
    }
};

const confirmPayment = () => {
    if (!props.transaction || !canConfirm.value) return;

    isProcessing.value = true;

    const data: { payment_method: string; cash_received?: number } = {
        payment_method: paymentMethod.value,
    };

    if (paymentMethod.value === 'cash') {
        data.cash_received = cashReceived.value;
    }

    router.post(route('pos.orders.checkout', { transaction: props.transaction.id }), data, {
        onSuccess: () => {
            emit('success');
            closeDialog();
        },
        onError: (errors) => {
            console.error('Payment failed:', errors);
            alert('Gagal memproses pembayaran. Silakan coba lagi.');
        },
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};

const closeDialog = () => {
    emit('update:open', false);
    // Reset state
    paymentMethod.value = 'cash';
    cashReceived.value = 0;
};

// Watch for transaction changes to auto-set exact amount for quick start
watch(() => props.transaction, (newVal) => {
    if (newVal) {
        cashReceived.value = newVal.total_amount;
    }
});

watch(() => props.open, (isOpen) => {
    if (isOpen && props.transaction) {
        cashReceived.value = props.transaction.total_amount;
    }
});
</script>

<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="sm:max-w-md bg-card text-card-foreground border-border">
            <DialogHeader>
                <DialogTitle class="text-xl">
                    Pembayaran - {{ transaction?.customer_name }}
                </DialogTitle>
                <DialogDescription class="sr-only">
                    Review total tagihan dan pilih metode pembayaran.
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-6 py-4">
                <!-- Total Display -->
                <div class="text-center py-6 bg-muted/50 rounded-xl">
                    <p class="text-sm text-muted-foreground mb-1">Total Pembayaran</p>
                    <p class="text-4xl font-bold text-primary">
                        Rp {{ formatPrice(totalAmount) }}
                    </p>
                </div>

                <!-- Payment Method Toggle -->
                <div class="grid grid-cols-2 gap-3">
                    <Button :variant="paymentMethod === 'cash' ? 'default' : 'outline'" class="h-16 flex-col gap-2"
                        :class="paymentMethod === 'cash' ? 'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground' : 'hover:bg-accent hover:text-accent-foreground'"
                        @click="paymentMethod = 'cash'">
                        <Banknote class="w-6 h-6" />
                        <span class="font-semibold">TUNAI</span>
                    </Button>
                    <Button :variant="paymentMethod === 'qris' ? 'default' : 'outline'" class="h-16 flex-col gap-2"
                        :class="paymentMethod === 'qris' ? 'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground' : 'hover:bg-accent hover:text-accent-foreground'"
                        @click="paymentMethod = 'qris'">
                        <QrCode class="w-6 h-6" />
                        <span class="font-semibold">QRIS</span>
                    </Button>
                </div>

                <!-- Cash Payment Section -->
                <template v-if="paymentMethod === 'cash'">
                    <!-- Cash Input -->
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                            Uang Diterima
                        </label>
                        <Input v-model.number="cashReceived" type="number" placeholder="0"
                            class="text-lg font-semibold text-right bg-background border-input" />
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="grid grid-cols-4 gap-2">
                        <Button variant="outline" size="sm" @click="setQuickCash('exact')">
                            Uang Pas
                        </Button>
                        <Button variant="outline" size="sm" @click="setQuickCash(50000)">
                            50rb
                        </Button>
                        <Button variant="outline" size="sm" @click="setQuickCash(100000)">
                            100rb
                        </Button>
                        <Button variant="outline" size="sm" @click="setQuickCash(200000)">
                            200rb
                        </Button>
                    </div>

                    <!-- Change Display -->
                    <div class="text-center py-4 rounded-xl" :class="isChangeNegative
                        ? 'bg-destructive/10'
                        : 'bg-green-500/10'">
                        <p class="text-sm mb-1" :class="isChangeNegative
                            ? 'text-destructive'
                            : 'text-green-600 dark:text-green-400'">
                            {{ isChangeNegative ? 'Kurang' : 'Kembalian' }}
                        </p>
                        <p class="text-2xl font-bold" :class="isChangeNegative
                            ? 'text-destructive'
                            : 'text-green-600 dark:text-green-400'">
                            Rp {{ formatPrice(Math.abs(changeAmount)) }}
                        </p>
                    </div>
                </template>

                <!-- QRIS Section -->
                <template v-else>
                    <div class="text-center py-8 bg-muted/30 rounded-xl">
                        <QrCode class="w-16 h-16 mx-auto text-muted-foreground mb-4" />
                        <p class="text-sm text-foreground">
                            Minta pelanggan scan QRIS untuk pembayaran
                        </p>
                        <p class="text-xs text-muted-foreground mt-2">
                            Konfirmasi setelah pembayaran diterima
                        </p>
                    </div>
                </template>
            </div>

            <DialogFooter>
                <Button variant="outline" @click="closeDialog" :disabled="isProcessing">
                    Batal
                </Button>
                <Button class="bg-green-600 hover:bg-green-700 text-white" :disabled="!canConfirm || isProcessing"
                    @click="confirmPayment">
                    <Check class="w-4 h-4 mr-2" />
                    {{ isProcessing ? 'Memproses...' : 'Konfirmasi Bayar' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
