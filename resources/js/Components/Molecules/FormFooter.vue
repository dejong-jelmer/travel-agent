<script setup>
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
            <span v-if="form.isDirty" class="text-status-warning font-medium">
                Er zijn niet opgeslagen wijzigingen.
            </span>
            <span v-else class="text-status-success">
                Er zijn geen wijzigingen.
            </span>
        </p>
        <FormSubmit :form="form" :label="label" @click="emit('submit')" />
    </div>
</template>
