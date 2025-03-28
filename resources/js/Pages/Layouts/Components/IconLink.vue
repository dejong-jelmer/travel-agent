<script setup>
import { Delete, Edit, View, Itinerary } from '@/Pages/Icons';
import { Inertia } from '@inertiajs/inertia';
import { shallowRef, markRaw } from 'vue';

const props = defineProps({
    icon: {
        type: String,
        required: true,
    },
    href: {
        type: String,
        required: true
    },
    method: {
        type: String,
        default: 'get'
    },
    showConfirm: {
        type:Boolean,
        default: false
    },
    prompt: {
        type: String,
        required: false
    },
});

const icons = {
    Delete: markRaw(Delete),
    Edit: markRaw(Edit),
    View: markRaw(View),
    Itinerary: markRaw(Itinerary),
};
const currentIcon = shallowRef(icons[`${props.icon}`]);

const confirmDelete = (href, showConfirm, prompt) => {
    if(showConfirm) {
        if (!confirm(prompt)) {
            return;
        }
    }
    Inertia[`${props.method}`](href);
}
</script>

<template>
    <div class="group w-fit mx-auto" @click="confirmDelete(href, showConfirm, prompt)">
        <button class="bg-transparent p-2 rounded-lg border border-custom-red group-hover:bg-custom-red">
            <component :is="currentIcon" class="h-5 stroke-custom-red group-hover:stroke-white" />
        </button>
    </div>
</template>
