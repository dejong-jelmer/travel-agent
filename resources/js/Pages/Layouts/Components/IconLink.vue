<script setup>
import { Delete, Edit, View, Calendar, Save, Add } from '@/Pages/Icons';
import { router } from '@inertiajs/vue3'
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
        default: 'info',
    }
});

const icons = {
    Delete: markRaw(Delete),
    Edit: markRaw(Edit),
    View: markRaw(View),
    Calendar: markRaw(Calendar),
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

        router.visit(href, {
            method: `${props.method}`
        });
    }
}
</script>

<template>
    <div class="group w-fit" @click="confirmDelete(href, showConfirm, prompt)">
        <button
            class="p-2 rounded-lg border"
            :class="{
                    'bg-red-500 group-hover:bg-white border-red-500': type === 'delete',
                    'bg-white group-hover:bg-red-500 border border-red-500 group-hover:border-red-500': type === 'danger',
                    'bg-white group-hover:bg-primary-default border border-primary-default': type === 'info'
                }"
            >
            <component :is="currentIcon"
                class="h-5"
                :class="{
                        'stroke-white group-hover:stroke-red-500': type === 'delete',
                        'stroke-red-500 group-hover:stroke-white': type === 'danger',
                        'stroke-primary-default group-hover:stroke-white': type === 'info'
                    }"
            />
        </button>
    </div>
</template>
