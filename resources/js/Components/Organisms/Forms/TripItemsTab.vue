<script setup>
import { computed } from 'vue';
import { Plus } from 'lucide-vue-next';

const props = defineProps({
    form: Object,
    typeOptions: Object,
    categoryOptions: Object,
});

// Groepeer categories per type
const categoriesByType = computed(() => {
    const grouped = {};

    props.categoryOptions.forEach(category => {
        if (!category.disabled && category.type) {
            if (!grouped[category.type]) {
                grouped[category.type] = [];
            }
            grouped[category.type].push(category);
        }
    });

    return grouped;
});

const getTypeLabel = (typeValue) => {
    const type = props.typeOptions.find(t => t.id === typeValue);
    return type ? type.name : typeValue;
};

const itemsFor = (category) => {
    return props.form.items.filter(
        item => item.category === category
    );
};

const addItem = (category, type) => {
    props.form.items.push({
        type,
        category,
        item: ''
    });
};

const deleteItem = (item) => {
    const itemIndex = props.form.items.indexOf(item);
    if (itemIndex !== -1) {
        props.form.items.splice(itemIndex, 1);
    }
};
</script>

<template>
    <div class="space-y-10">
        <div v-for="(categories, typeValue) in categoriesByType" :key="typeValue" class="space-y-6">
            <h3 class="text-xl font-bold text-gray-800 border-b-2 border-primary-default pb-2">
                {{ getTypeLabel(typeValue) }}
            </h3>

            <div v-for="category in categories" :key="category.id" class="ml-4 mb-6">
                <h4 class="text-base font-semibold text-gray-700 mb-3">{{ category.name }}</h4>

                <div class="space-y-2 mb-3 ml-4">
                    <TripItemRow
                        v-for="(item, index) in itemsFor(category.id)"
                        :key="index"
                        :index="index"
                        :item="item"
                        @delete="() => deleteItem(item)"
                        :category-options="categoryOptions"
                        :show-type-select="false"
                    />
                </div>

                <!-- Add button -->
                <div class="ml-4">
                    <button
                        type="button"
                        @click="addItem(category.id, category.type)"
                        class="flex items-center gap-2 px-3 py-2 text-sm text-primary-default hover:text-primary-dark transition-colors"
                    >
                        <Plus class="h-4 w-4" />
                        <span>{{ $t('forms.actions.add') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
