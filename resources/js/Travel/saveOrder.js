import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import { fetchApi } from '@/fetchApi';

// Sends the new order of a list to its order route. When that fails, the page reloads to show the order as it is
// stored.
export const saveOrder = (url, items) =>
    fetchApi(url, { method: 'PATCH', body: { ids: items.map((item) => item.id) } }).catch(() => {
        useToast().error('De volgorde kon niet worden opgeslagen.');
        router.reload();
    });
