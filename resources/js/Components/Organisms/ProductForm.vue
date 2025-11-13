<script setup>
import { computed } from 'vue';
const emit = defineEmits(['submit', 'initialized']);
const props = defineProps({
    countries: Object,
    form: Object,
});

const imageErrors = computed(() =>
    Object.keys(props.form.errors)
        .filter((key) => key.startsWith('images.'))
        .map((key) => props.form.errors[key])
);

</script>

<template>
    <form @submit.prevent="submit" class="max-w-wide mx-auto">
        <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
            <!-- Header Section -->
            <div class="laptop:col-span-3 bg-white py-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700">
                            {{ form.id ? 'Bewerk Product' : 'Nieuw Product' }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-700/50">
                            Beheer de details van uw reisproduct
                        </p>
                    </div>
                </div>
            </div>

            <!-- Middenkolom: Product Details + Media -->
            <div class="laptop:col-span-2 space-y-8">
                <!-- Product Details Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Product Details</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Basis informatie over het reisproduct</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 laptop:grid-cols-2 gap-6">
                            <Input type="text" name="name" label="Productnaam" :required="true" v-model="form.name"
                                :feedback="form.errors.name" placeholder="Bijv. Venetië Express Treinreis" />
                            <Input type="text" name="slug" label="URL Slug" :required="true" v-model="form.slug"
                                :feedback="form.errors.slug" placeholder="venetie-express-treinreis" />
                        </div>
                        <TextArea name="description" label="Omschrijving" :required="true" v-model="form.description"
                            :feedback="form.errors.description"
                            placeholder="Beschrijf de reis en wat reizigers kunnen verwachten..." :rows="6" />
                    </div>
                </section>

                <!-- Media Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Media</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Upload afbeeldingen voor het product</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Featured Afbeelding
                            </label>
                            <UniversalImageUploader v-model="form.featuredImage" preview-size="large"
                                :label="form.featuredImage ? 'Wijzig hoofdafbeelding' : 'Selecteer een afbeelding'"
                                :feedback="form.errors.featuredImage"
                                @initialized="emit('initialized')" />
                            <p class="mt-2 text-xs text-gray-700/30">
                                Deze afbeelding wordt gebruikt als hoofdafbeelding in overzichten
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Galerij Afbeeldingen
                            </label>
                            <UniversalImageUploader v-model="form.images" :multiple="true"
                                label="Sleep meerdere afbeeldingen hierheen" :feedback="imageErrors"
                                @initialized="emit('initialized')" />

                            <p class="mt-2 text-xs text-gray-700/30">
                                Upload meerdere afbeeldingen voor de productgalerij
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Rechterkolom: Prijs & Duur + Instellingen -->
            <div class="laptop:col-start-3 space-y-8">
                <!-- Settings & Configuration Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Instellingen</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Configureer zichtbaarheid</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200">
                                    <Checkbox v-model="form.active" name="active" class="flex-1">
                                        <span class="font-medium text-gray-700">Actief</span>
                                        <span class="block text-xs text-gray-700/30 mt-1">
                                            Product is zichtbaar op de website
                                        </span>
                                    </Checkbox>
                                </div>
                                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200">
                                    <Checkbox v-model="form.featured" name="featured" class="flex-1">
                                        <span class="font-medium text-gray-700">Uitgelicht</span>
                                        <span class="block text-xs text-gray-700/30 mt-1">
                                            Toon op homepage als featured product
                                        </span>
                                    </Checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Pricing & Duration Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Prijs & Duur</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Definieer de kosten en reisduur</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <Input type="text" name="price" label="Prijs per persoon" :required="true"
                                v-model="form.price" :feedback="form.errors.price" placeholder="€ 1.295">
                            <template #prefix>
                                <span class="text-gray-700/30">€</span>
                            </template>
                            </Input>
                            <Input type="number" name="duration" label="Duur" :required="true" v-model="form.duration"
                                :feedback="form.errors.duration" placeholder="7">
                            <template #suffix>
                                <span class="text-gray-700/30">dagen</span>
                            </template>
                            </Input>
                        </div>
                    </div>
                </section>

                <!-- Linked countries -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Landen</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Configureer gekoppelde landen</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <Select name="country" v-model="form.countries" :multiple="true" :required="true"
                                :options="countries" :feedback="form.errors.countries"
                                :placeholder="'Selecteer één of meerdere landen'" />
                            <p class="mt-2 text-xs text-gray-700/30">
                                Selecteer de landen die bezocht worden tijdens deze reis
                            </p>
                        </div>
                    </div>
                </section>
            </div>

        </div>
        <!-- Footer Actions -->
        <div
            class="laptop:col-span-2 flex items-center justify-between border-t border-gray-200 bg-white rounded-lg mt-6 p-6 shadow-sm">
            <p class="text-sm text-gray-700/30">
                <span v-if="form.isDirty" class="text-status-warning font-medium">
                    Er zijn niet opgeslagen wijzigingen
                </span>
                <span v-else class="text-status-success">
                    Alles opgeslagen
                </span>
            </p>
            <FormSubmit :form="form" label="Product Opslaan" @submit="submit" />
        </div>
    </form>
</template>
