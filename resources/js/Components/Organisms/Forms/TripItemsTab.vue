<script setup>
import { Plus } from 'lucide-vue-next';

const props = defineProps({
    form: Object,
    typeOptions: Array,
});

const itemsForType = (typeValue) =>
    props.form.items.filter(item => item.type === typeValue);

const lastItemEmpty = (typeValue) => {
    const items = itemsForType(typeValue);
    const last = items.at(-1);
    return last ? last.item === '' : false;
};

const addItem = (typeValue) => {
    if (lastItemEmpty(typeValue)) {
        return;
    }
    props.form.items.push({
        type: typeValue,
        item: '',
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
        <div v-for="type in typeOptions" :key="type.id" class="space-y-6">
            <h3 class="text-xl font-bold text-gray-800 border-b-2 border-primary-default pb-2">
                {{ type.name }}
            </h3>

            <div class="space-y-2 mb-3 ml-4">
                <TripItemRow
                    v-for="(item, index) in itemsForType(type.id)"
                    :key="`${type.id}-${form.items.indexOf(item)}`"
                    :index="form.items.indexOf(item)"
                    :item="item"
                    :type-options="typeOptions"
                    :show-type-select="true"
                    :errors="form.errors"
                    @delete="() => deleteItem(item)"
                />
            </div>

            <div class="ml-4">
                <button
                    type="button"
                    @click="addItem(type.id)"
                    class="flex items-center gap-2 px-3 py-2 text-sm text-primary-default hover:text-primary-dark transition-colors"
                    :class="{ 'cursor-not-allowed': lastItemEmpty(type.id) }"
                >
                    <Plus class="h-4 w-4" />
                    <span>{{ $t('forms.actions.add') }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
