<script setup>
const props = defineProps({
    form: Object,
    meals: Object,
    transport: Object,
});

const emit = defineEmits(['submit']);
</script>

<template>
    <form @submit.prevent="emit('submit')" class="max-w-wide mx-auto">
        <div class="grid grid-cols-1 laptop:grid-cols-2 gap-8">
            <!-- Header Section -->
            <div class="laptop:col-span-2 bg-white py-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700">
                            {{ form.id ? 'Bewerk Reisplan' : 'Nieuw Reisplan' }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-700/50">
                            Beheer de details van de dagplanning
                        </p>
                    </div>
                </div>
            </div>

            <!-- Linkerkolom: Basis Informatie -->
            <div class="space-y-8">
                <!-- Basic Information Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Basis Informatie</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Titel, locatie en omschrijving van de dag</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <Input type="text" name="title" label="Titel" :required="true" v-model="form.title"
                            :feedback="form.errors.title" placeholder="Bijv. Aankomst in Venetië" />
                        <Input type="text" name="location" label="Locatie" :required="true" v-model="form.location"
                            :feedback="form.errors.location" placeholder="Bijv. Venetië, Italië" />
                        <TextArea name="description" label="Omschrijving" :required="true" v-model="form.description"
                            :feedback="form.errors.description"
                            placeholder="Beschrijf wat er deze dag gebeurt..." :rows="6" />
                        <Input type="text" name="remark" label="Opmerking" :required="false" v-model="form.remark"
                            :feedback="form.errors.remark" placeholder="Optionele opmerking of waarschuwing" />
                        <p class="text-xs text-gray-700/30">
                            Opmerkingen worden getoond met een waarschuwingspictogram
                        </p>
                    </div>
                </section>
            </div>

            <!-- Rechterkolom: Media -->
            <div class="space-y-8">
                <!-- Media Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Media</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Upload afbeelding voor deze dag</p>
                    </div>
                    <div class="p-6">
                        <UniversalImageUploader v-model="form.image" preview-size="large"
                            :label="form.image ? 'Afbeelding wijzigen' : 'Selecteer een afbeelding'"
                            :feedback="form.errors.image"
                            @initialized="emit('initialized')" />
                        <p class="mt-2 text-xs text-gray-700/30">
                            Deze afbeelding wordt gebruikt voor dit dagprogramma
                        </p>
                    </div>
                </section>

                <!-- Details Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Details</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Verblijf, activiteiten en faciliteiten</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <Input type="text" name="accommodation" label="Verblijf" :required="true"
                            v-model="form.accommodation" :feedback="form.errors.accommodation"
                            placeholder="Bijv. Hotel Centrale" />
                        <Input type="text" name="activities" label="Activiteiten" :required="true"
                            v-model="form.activities" :feedback="form.errors.activities"
                            placeholder="Bijv. Stadswandeling, museumbezoek" />
                        <Select name="transport" label="Vervoer" v-model="form.transport" :multiple="true"
                            :required="false" :options="transport" :feedback="form.errors.transport"
                            :placeholder="'Kies vervoer types'" />
                        <Select name="meals" label="Maaltijden" v-model="form.meals" :multiple="true" :required="false"
                            :options="meals" :feedback="form.errors.meals"
                            :placeholder="'Kies inbegrepen maaltijden'" />
                    </div>
                </section>
            </div>


        </div>

        <!-- Footer Actions -->
        <FormFooter :form="form" label="Reisplan Opslaan" @submit="emit('submit')" />
    </form>
</template>
