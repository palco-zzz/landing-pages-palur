import { ref, watch, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { toast } from 'vue-sonner';

// Types
interface OrderItem {
    menu_id: number;
    quantity: number;
    price: number;
    subtotal: number;
}

interface OfflineOrder {
    uuid: string;
    customer_name: string;
    items: OrderItem[];
    created_at: string;
}

interface SyncResponse {
    status: 'success' | 'error';
    synced_count: number;
    errors?: string[];
    message: string;
}

const STORAGE_KEY = 'pos_offline_queue';

export function useOfflineTransaction() {
    // State
    const isOnline = ref(navigator.onLine);
    const offlineQueue = ref<OfflineOrder[]>([]);
    const isSyncing = ref(false);

    // Load queue from localStorage on init
    const loadFromStorage = () => {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                offlineQueue.value = JSON.parse(stored);
            }
        } catch (e) {
            console.error('Failed to load offline queue from localStorage:', e);
            offlineQueue.value = [];
        }
    };

    // Save queue to localStorage
    const saveToStorage = () => {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(offlineQueue.value));
        } catch (e) {
            console.error('Failed to save offline queue to localStorage:', e);
        }
    };

    // Clear queue and storage
    const clearQueue = () => {
        offlineQueue.value = [];
        localStorage.removeItem(STORAGE_KEY);
    };

    // Add order to offline queue
    const addToQueue = (orderData: Omit<OfflineOrder, 'created_at'>): boolean => {
        try {
            const offlineOrder: OfflineOrder = {
                ...orderData,
                created_at: new Date().toISOString(),
            };

            offlineQueue.value.push(offlineOrder);
            saveToStorage();

            toast.info('Pesanan Disimpan Offline', {
                description: `Pesanan ${orderData.customer_name} akan dikirim saat koneksi kembali.`,
            });

            return true;
        } catch (e) {
            console.error('Failed to add order to offline queue:', e);
            return false;
        }
    };

    // Sync offline orders to server
    const syncNow = async (): Promise<boolean> => {
        // Don't sync if offline, already syncing, or queue is empty
        if (!isOnline.value || isSyncing.value || offlineQueue.value.length === 0) {
            return false;
        }

        isSyncing.value = true;

        try {
            const response = await axios.post<SyncResponse>(route('pos.sync'), {
                orders: offlineQueue.value,
            });

            if (response.data.status === 'success') {
                const count = response.data.synced_count;

                // Clear the queue on success
                clearQueue();

                toast.success('Sinkronisasi Berhasil', {
                    description: `${count} pesanan offline berhasil diunggah.`,
                });

                // Log any partial errors if they exist
                if (response.data.errors && response.data.errors.length > 0) {
                    console.warn('Some orders had errors:', response.data.errors);
                }

                return true;
            } else {
                throw new Error(response.data.message);
            }
        } catch (e: any) {
            console.error('Failed to sync offline orders:', e);

            toast.error('Gagal Sinkronisasi', {
                description: 'Pesanan offline tetap tersimpan, akan dicoba lagi nanti.',
            });

            return false;
        } finally {
            isSyncing.value = false;
        }
    };

    // Event handlers for online/offline detection
    const handleOnline = () => {
        isOnline.value = true;
        toast.success('Koneksi Kembali', {
            description: 'Anda kembali online.',
        });
    };

    const handleOffline = () => {
        isOnline.value = false;
        toast.warning('Koneksi Terputus', {
            description: 'Pesanan akan disimpan secara offline.',
        });
    };

    // Watch online status and auto-sync when back online
    watch(isOnline, (online) => {
        if (online && offlineQueue.value.length > 0) {
            // Delay sync slightly to ensure connection is stable
            setTimeout(() => {
                syncNow();
            }, 1000);
        }
    });

    // Setup event listeners on mount
    onMounted(() => {
        loadFromStorage();
        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);

        // Try to sync if we have pending orders and we're online
        if (isOnline.value && offlineQueue.value.length > 0) {
            syncNow();
        }
    });

    // Cleanup on unmount
    onUnmounted(() => {
        window.removeEventListener('online', handleOnline);
        window.removeEventListener('offline', handleOffline);
    });

    return {
        // State
        isOnline,
        offlineQueue,
        isSyncing,

        // Computed
        hasOfflineOrders: () => offlineQueue.value.length > 0,
        offlineCount: () => offlineQueue.value.length,

        // Actions
        addToQueue,
        syncNow,
        clearQueue,
    };
}
