<script setup>
// import { Delete, Edit, View, Calendar, Save, Add } from '@/Icons';
import { shallowRef, markRaw } from 'vue';

const props = defineProps({
    icon: {
        type: String,
        required: true,
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

</script>

<template>
    <component :is="currentIcon" v-bind="$attrs" />
</template>
