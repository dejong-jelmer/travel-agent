<script setup>
const props = defineProps({
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
    showTypeSelect: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: null,
    },
});

// console.log(props.errors[`items.${index}.item`]);

const emit = defineEmits(['update', 'delete']);

// Update type automatically when category changes
const handleCategoryChange = (newCategory) => {
    const selectedCategory = props.categoryOptions.find(cat => cat.id === newCategory);
    if (selectedCategory && selectedCategory.type) {
        props.item.type = selectedCategory.type;
    }
};
</script>

<template>
    <div class="flex items-start gap-3 group">
        <!-- Category select -->
        <div class="w-48">
            <Select
                name="category"
                v-model="item.category"
                @update:modelValue="handleCategoryChange"
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
