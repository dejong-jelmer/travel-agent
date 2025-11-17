<script setup>
const props = defineProps({
    product: Object,
    required: true,
});

</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
                <!-- Header Section -->
                <div class="laptop:col-span-3 bg-white py-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-700">
                                {{ product.name }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-700/50">
                                {{ product.slug }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <IconLink icon="Pencil" :href="route('admin.products.edit', product)"
                                v-tippy="'Bewerk reisproduct'" />
                            <IconLink icon="Route" :href="product.itineraries?.length ?
                                route('admin.products.itineraries.index', product)
                                : route('admin.products.itineraries.create', product)"
                                v-tippy="'Bekijk reisplan van deze reis'" />
                        </div>
                    </div>
                </div>

                <!-- Middenkolom: Product Details + Media -->
                <div class="laptop:col-span-2 space-y-8">
                    <!-- Product Details Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Product Details</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Informatie over het reisproduct</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Naam</label>
                                <p class="mt-1 text-gray-900">{{ product.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Slug</label>
                                <p class="mt-1 text-gray-900">{{ product.slug }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Beschrijving</label>
                                <p class="mt-1 text-gray-900">{{ product.description }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Media Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Afbeeldingen</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Product media</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <div v-if="product.featured_image">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Featured Afbeelding
                                </label>
                                <img :src="product.featured_image.full_path" alt="Featured image"
                                    class="max-w-full h-auto rounded-lg shadow-md" />
                            </div>

                            <div v-if="product.images?.length">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Galerij Afbeeldingen
                                </label>
                                <div class="flex flex-wrap gap-4">
                                    <img v-for="(image, index) in product.images" :key="index" :src="image.full_path"
                                        :alt="`Product image ${index}`"
                                        class="w-24 h-24 object-cover rounded-lg shadow-md" />
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Rechterkolom: Prijs & Duur + Instellingen + Landen -->
                <div class="laptop:col-start-3 space-y-8">
                    <!-- Settings Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Instellingen</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Zichtbaarheid</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Actief</span>
                                <Pill :type="product.active ? 'success' : 'warning'">
                                    {{ product.active ? "Ja" : "Nee" }}
                                </Pill>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Uitgelicht</span>
                                <Pill class="px-3 py-1 rounded-full text-xs font-semibold"
                                    :type="product.featured ? 'success' : 'info'">
                                    {{ product.featured ? "Ja" : "Nee" }}
                                </Pill>
                            </div>
                        </div>
                    </section>

                    <!-- Pricing & Duration Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Prijs & Duur</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Kosten en reisduur</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Prijs per persoon</label>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ product.price }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Duur</label>
                                <p class="mt-1 text-xl text-gray-900">{{ product.duration }} dagen</p>
                            </div>
                        </div>
                    </section>

                    <!-- Countries Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Gekoppelde Landen</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Bestemmingen</p>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                <Pill v-for="country in product.countries" :key="country.id" type="success"
                                    variant="transparent">
                                    {{ country.name }}
                                </Pill>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </Admin>
</template>
