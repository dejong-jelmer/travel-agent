<script setup>
import { reactive } from 'vue'

defineProps({
    products: Object,
})
const showMoreOptions = reactive({});

</script>
<template>
    <Admin :links="products.links">
        <template v-if="products.data.length > 0">
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">Product</th>
                            <th class="py-4 px-6 text-center">Land(en)</th>
                            <th class="py-4 px-6 text-center">Prijs</th>
                            <th class="py-4 px-6 text-center">Dagen</th>
                            <th class="py-4 px-6 text-center">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(product, index) in products.data" :key="index" class="hover:bg-gray-100 transition">
                            <td class="py-4 px-6 text-center">{{ product.id }}</td>
                            <td class="py-4 px-6 text-center">{{ product.name }}</td>
                            <td class="py-4 px-6 text-center">{{ product.countries_list }}</td>
                            <td class="py-4 px-6 text-center">€ {{ product.price }}</td>
                            <td class="py-4 px-6 text-center">{{ product.duration }}</td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <IconLink class="mx-auto" icon="Eye" :href="route('admin.products.show', { product })"
                                    v-tippy="'Bekijk reisproduct'" />
                                <IconLink class="mx-auto" icon="Pencil" :href="route('admin.products.edit', product)"
                                    v-tippy="'Bewerk reisproduct'" />
                                <div class="w-fit mx-auto" v-tippy="`Meer opties`">
                                    <button class="info-button"
                                        @click="showMoreOptions[product.id] = !showMoreOptions[product.id]">
                                        <More class="h-5" />
                                    </button>
                                </div>
                                <div v-if="showMoreOptions[product.id]" class="space-y-2">
                                    <IconLink class="mx-auto" icon="Route" :href="product.itineraries?.length ?
                                        route('admin.products.itineraries.index', product)
                                        : route('admin.products.itineraries.create', product)"
                                        v-tippy="'Bekijk reisplan van deze reis'" />
                                    <IconLink class="mx-auto" type="delete" icon="Trash2"
                                        :href="route('admin.products.destroy', product)" method="delete"
                                        :showConfirm="true" prompt="Weet je zeker dat je deze reis wilt verwijderen?"
                                        v-tippy="'Verwijder reisproduct!'" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
        <template v-else>
            <div class="p-5">
                <p>Er zijn nog geen reis producten klik <DefaultLink :href="route('admin.products.create')">hier</DefaultLink> om een product toe te voegen.
                </p>
            </div>
        </template>
    </Admin>
</template>
