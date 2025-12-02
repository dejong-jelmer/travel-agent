<script setup>
const props = defineProps({
    countries: Object,
});
</script>
<template>
    <Admin :links="countries.links">
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">Landen</h1>
            <IconLink v-tippy="'Voeg een land toe'" icon="Plus" type="info" :href="route('admin.countries.create')" />
        </div>
        <template v-if="countries.data.length > 0">
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">Land</th>
                            <th class="py-4 px-6 text-center">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(country, index) in countries.data" :key="index"
                            class="hover:bg-gray-100 transition">
                            <td class="py-4 px-6 text-center">{{ country.id }}</td>
                            <td class="py-4 px-6 text-center">{{ country.name }}</td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <IconLink class="mx-auto" type="delete" icon="Trash2"
                                    :href="route('admin.countries.destroy', country)" method="delete"
                                    :showConfirm="true" prompt="Weet je zeker dat je dit land wilt verwijderen?"
                                    v-tippy="'Verwijder land'" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
        <template v-else>
            <div class="p-5">
                <p>Je hebt nog geen landen aangemaakt.</p>
            </div>
        </template>
    </Admin>
</template>
