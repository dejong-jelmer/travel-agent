<script setup>
defineProps({
    item: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        required: true,
    },
    categoryOptions: {
        type: Array,
        required: true,
    },
    typeOptions: {
        type: Array,
        default: () => [],
    },
    showTypeSelect: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['update', 'delete']);
</script>

<template>
    <div class="flex items-start gap-3 group">
        <!-- Type select -->
        <div v-if="showTypeSelect" class="w-40">
            <Select
                name="type"
                v-model="item.type"
                :multiple="false"
                :required="true"
                :options="typeOptions"
                :feedback="errors[`items.${index}.type`]"
                :placeholder="$t('admin.trips.edit.items.select_type')"
                :show-label="false"
            />
        </div>

        <!-- Category select -->
        <div class="w-48">
            <Select
                name="category"
                v-model="item.category"
                :multiple="false"
                :required="true"
                :options="categoryOptions"
                :feedback="errors[`items.${index}.category`]"
                :placeholder="$t('admin.trips.edit.items.select_category')"
                :show-label="false"
            />
        </div>

        <!-- Item input -->
        <div class="flex-1">
            <Input
                type="text"
                name="items[]"
                v-model="item.item"
                :placeholder="$t('admin.trips.edit.items.description')"
                :feedback="errors[`items.${index}.item`]"
                :show-label="false"
            />
        </div>

        <!-- Delete button -->
        <DeleteButton @delete="emit('delete', index)" />
    </div>
</template>
