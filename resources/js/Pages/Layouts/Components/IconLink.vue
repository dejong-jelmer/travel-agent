<script setup>
import { Delete, Edit, View, Itinerary, Save, Add } from '@/Pages/Icons';
import { Inertia } from '@inertiajs/inertia';
import { shallowRef, markRaw } from 'vue';

const props = defineProps({
    icon: {
        type: String,
        required: true,
    },
    href: {
        type: String,
        required: false,
    },
    method: {
        type: String,
        default: 'get',
    },
    showConfirm: {
        type:Boolean,
        default: false,
    },
    prompt: {
        type: String,
        required: false,
    },
    type: {
        type: String,
        required: false,
        default: 'danger',
    }
});

const icons = {
    Delete: markRaw(Delete),
    Edit: markRaw(Edit),
    View: markRaw(View),
    Itinerary: markRaw(Itinerary),
    Save: markRaw(Save),
    Add: markRaw(Add),
};
const currentIcon = shallowRef(icons[`${props.icon}`]);

const confirmDelete = (href, showConfirm, prompt) => {
    if(href) {
        if(showConfirm) {
            if (!confirm(prompt)) {
                return;
            }
        }
        Inertia[`${props.method}`](href);
    }
}
</script>

<template>
    <div class="group w-fit" @click="confirmDelete(href, showConfirm, prompt)">
        <button
            class="p-2 rounded-lg border"
            :class="{
                    'bg-custom-red group-hover:bg-white border-custom-red': type === 'delete',
                    'bg-white group-hover:bg-custom-red border group-hover:border-custom-red': type === 'danger',
                    'bg-white group-hover:bg-custom-dark border group-hover:border-custom-dark': type === 'info'
                }"
            >
            <component :is="currentIcon"
                class="h-5"
                :class="{
                        'stroke-white group-hover:stroke-custom-red': type === 'delete',
                        'stroke-custom-red group-hover:stroke-white': type === 'danger',
                        'stroke-custom-dark group-hover:stroke-white': type === 'info'
                    }"
            />
        </button>
    </div>
</template>
