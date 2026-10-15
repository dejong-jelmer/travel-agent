import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

// Deletes after the admin confirms the question. When the server refuses, because a leg or stay still uses the item,
// its message shows as a toast.
export const destroy = (url, question) => {
    if (!confirm(question)) {
        return;
    }

    router.delete(url, {
        preserveScroll: true,
        onError: (errors) => useToast().error(Object.values(errors)[0]),
    });
};
