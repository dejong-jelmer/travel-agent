<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import { Copy, FileText } from '@lucide/vue';
import { destroy } from '@/Travel/destroy.js';
import { formatShortDate } from '@/Travel/format.js';

// The travel documents of the booking: the PDF to check, and the link that shows them to the travelers.
const props = defineProps({
    booking: {
        type: Object,
        required: true,
    },
    // { url, expires_on, expired }, or null while there is none.
    shareLink: {
        type: Object,
        default: null,
    },
});

const toast = useToast();
const linkInput = ref(null);

const makeLink = () => {
    if (props.shareLink && !confirm('Een nieuwe deellink maken? De huidige link werkt daarna niet meer.')) {
        return;
    }

    router.post(route('admin.bookings.share-link.store', props.booking.id), {}, { preserveScroll: true });
};

const revokeLink = () =>
    destroy(
        route('admin.bookings.share-link.destroy', props.booking.id),
        'Deellink intrekken? De reizigers kunnen hun documenten dan niet meer via deze link openen.',
    );

// The clipboard API needs a secure context, which a local .test site is not; selecting the text works everywhere.
const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(props.shareLink.url);
    } catch {
        linkInput.value.select();
        document.execCommand('copy');
    }

    toast.success('Link gekopieerd.');
};
</script>

<template>
    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 bg-white px-6 py-4">
            <div class="flex items-center gap-2">
                <FileText class="h-4 w-4 text-gray-400 flex-shrink-0" />
                <h2 class="text-lg font-semibold text-gray-700">Reisdocumenten</h2>
            </div>
            <p class="mt-1 text-sm text-gray-700/30 pl-6">Reisschema, accommodaties en praktische informatie, in één PDF.</p>
        </div>
        <div class="p-6 space-y-6 text-sm">
            <a :href="route('admin.bookings.travel-documents', booking.id)" target="_blank" rel="noopener"
                class="inline-flex items-center gap-1 font-medium text-brand-primary hover:text-brand-link">
                <FileText class="h-4 w-4" aria-hidden="true" />
                PDF bekijken
            </a>

            <div class="space-y-2">
                <h3 class="font-medium text-gray-700">Deellink</h3>
                <template v-if="shareLink">
                    <div class="flex items-center gap-2">
                        <label class="sr-only" for="share-link">Deellink</label>
                        <input id="share-link" ref="linkInput" :value="shareLink.url" readonly
                            class="form-input min-w-0 flex-1 py-1.5 text-xs text-gray-600" @focus="$event.target.select()" />
                        <button type="button" class="p-1.5 rounded text-gray-400 hover:text-brand-primary hover:bg-gray-100"
                            aria-label="Deellink kopiëren" v-tippy="'Kopiëren'" @click="copyLink">
                            <Copy class="h-4 w-4" aria-hidden="true" />
                        </button>
                    </div>
                    <p :class="shareLink.expired ? 'text-status-error' : 'text-gray-500'">
                        {{ shareLink.expired ? 'Verlopen na' : 'Werkt tot en met' }} {{ formatShortDate(shareLink.expires_on) }},
                        30 dagen na de terugreis.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <button type="button" class="font-medium text-brand-primary hover:text-brand-link" @click="makeLink">
                            Nieuwe link maken
                        </button>
                        <button type="button" class="font-medium text-gray-500 hover:text-status-error" @click="revokeLink">
                            Intrekken
                        </button>
                    </div>
                </template>
                <template v-else>
                    <p class="text-gray-500">
                        De reizigers openen hun documenten via een link, zonder in te loggen. De link werkt tot 30 dagen na de
                        terugreis.
                    </p>
                    <button type="button" class="font-medium text-brand-primary hover:text-brand-link" @click="makeLink">
                        Deellink maken
                    </button>
                </template>
            </div>
        </div>
    </section>
</template>
