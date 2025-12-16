<script setup>
import { TriangleAlert } from 'lucide-vue-next';

    const props =  defineProps({
        form: {
            type: Object,
            required: true
        },
        label: {
            type: String,
            default: 'Opslaan'
        }
    })

const emit = defineEmits(['submit']);

</script>
<template>
    <div
        class="laptop:col-span-2 flex items-center justify-between bg-white rounded-lg mt-6 p-6">
        <p class="text-sm text-gray-700/30">
            <span v-if="form.isDirty" class="flex text-status-warning font-medium">
                <TriangleAlert class="h-5 w-5 mr-3" /> {{ $t('form.footer.unsaved_changes') }}
            </span>
            <span v-else class="text-status-success">
                {{ $t('form.footer.no_changes') }}
            </span>
        </p>
        <FormSubmit :form="form" :label="label" @click="emit('submit')" />
    </div>
</template>
