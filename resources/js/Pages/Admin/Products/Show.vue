<script setup>

const props = defineProps({
    product: Object,
    required: true,
});

</script>

<template>
    <Admin>
        <div class="w-full flex justify-end space-x-2">
            <IconLink icon="Edit" :href="route('admin.products.edit', product)" v-tippy="'Bewerk reisproduct'" />
            <IconLink icon="Calendar" :href="product.itineraries?.length ?
                route('admin.products.itineraries.index', product)
                : route('admin.products.itineraries.create', product)"
                v-tippy="'Bekijk reisplan van deze reis'" />
        </div>
        <div class="flex flex-col tablet:flex-row tablet:space-x-6 space-y-6 tablet:space-y-0">
            <div class="bg-white p-6 rounded-2xl shadow-lg grid gap-6 laptop:grid-cols-2">
                <!-- Product details -->
                <div class="space-y-4">
                    <h2 class="text-3xl font-bold text-gray-800">{{ product.name }}</h2>
                    <p class="text-gray-600"><strong>Beschrijving:</strong> {{ product.description }}</p>
                    <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-700"><strong>Prijs:</strong> €{{ product.price }}</p>
                            <p class="text-gray-700"><strong>Land:</strong> {{ product.countries[0].name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-700"><strong>Duur:</strong> {{ product.duration }} dagen</p>
                            <p class="text-gray-700"><strong>Actief:</strong> {{ product.active ? "Ja" : "Nee" }}</p>
                            <p class="text-gray-700"><strong>Uitgelicht:</strong> {{ product.featured ? "Ja" : "Nee" }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="space-y-4">
                    <div>
                        <strong class="text-gray-800">Hoofdafbeelding:</strong>
                        <img :src="product.featured_image?.path" alt="Product image"
                            class="w-full max-w-sm h-auto object-cover rounded-lg mt-2 shadow-md" />
                    </div>
                    <div>
                        <strong class="text-gray-800">Andere afbeeldingen:</strong>
                        <div class="flex flex-wrap gap-4">
                            <template v-for="(image, index) in product.images" :key="index">
                                <img :src="image.path" :alt="`Product image ${index}`"
                                    class="w-24 h-24 tablet:w-32 tablet:h-32 object-cover rounded-lg shadow-md" />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Admin>
</template>
