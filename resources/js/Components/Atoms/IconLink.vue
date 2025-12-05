<script setup>
import { router } from '@inertiajs/vue3'
import { shallowRef, markRaw } from 'vue';

import {Trash2, Pencil, Eye, Route, Save, Plus, Send, RefreshCcw } from 'lucide-vue-next';

const props = defineProps({
    icon: {
        type: String,
        required: true,
        validator: (value) => ['Trash2', 'Pencil', 'Eye', 'Route', 'Save', 'Plus', 'Send', 'RefreshCcw'].includes(value)
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
        validator: (value) => ['delete', 'info'].includes(value)
    }
});

const icons = {
    Trash2: markRaw(Trash2),
    Pencil: markRaw(Pencil),
    Eye: markRaw(Eye),
    Route: markRaw(Route),
    Save: markRaw(Save),
    Plus: markRaw(Plus),
    Send: markRaw(Send),
    Refresh: markRaw(RefreshCcw),
};
const currentIcon = shallowRef(icons[`${props.icon}`]);

const handleClick = (href, showConfirm, prompt) => {
    if (showConfirm) {
        if (!confirm(prompt)) {
            return;
        }
    }
    if (href) {
        router.visit(href, {
            method: props.method
        });
    }
}
</script>

<template>
    <div class="group/iconlink w-fit" @click="handleClick(href, showConfirm, prompt)">
        <button
            class="p-2 rounded-lg border"
            :class="{
                'bg-white group-hover/iconlink:bg-status-error border-status-error': type === 'delete',
                'bg-white group-hover/iconlink:bg-slate-700 border-slate-700': type === 'info'
            }"
        >
            <component :is="currentIcon"
                class="h-5"
                :class="{
                    'stroke-status-error group-hover/iconlink:stroke-white': type === 'delete',
                    'stroke-slate-700 group-hover/iconlink:stroke-white': type === 'info'
                }"
            />
        </button>
    </div>
</template>
