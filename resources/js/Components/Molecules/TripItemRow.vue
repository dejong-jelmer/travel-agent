<script setup>
import Input from '@/Components/Molecules/Input.vue';
import Select from '@/Components/Molecules/Select.vue';
import DeleteButton from '@/Components/Atoms/DeleteButton.vue';

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
    error: {
        type: String,
        default: null,
    },
});

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
                :feedback="null"
                placeholder="Select category..."
                :show-label="false"
            />
        </div>

        <!-- Item input -->
        <div class="flex-1">
            <Input
                type="text"
                name="item"
                v-model="item.item"
                placeholder="Omschrijving..."
                :feedback="error"
                :show-label="false"
            />
        </div>

        <!-- Delete button -->
        <DeleteButton @delete="emit('delete', index)" />
    </div>
</template>
