<template>
    <Admin>
        <!-- Actieknoppen -->
        <div class="w-full flex justify-end mb-6">
            <div class="flex space-x-3">
                <IconLink icon="Edit" :href="route('admin.bookings.edit', booking)" v-tippy="'Bewerk boeking'" />
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto grid gap-8 tablet:grid-cols-3">

            <!-- Linker kolom (2/3 breed) -->
            <div class="tablet:col-span-2 bg-white p-8 rounded-2xl shadow-lg space-y-6">
                <div class="space-y-3">
                    <h2 class="text-2xl font-bold text-primary-dark">
                        Boekingsgegevens
                    </h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid">
                            <div class="flex gap-2 justify-between">
                                <span class="font-semibold">Boekingsreferentie:</span>
                                <span class="">{{ booking.reference }}</span>
                            </div>
                            <div class="flex gap-2 justify-between">
                                <span class="font-semibold">Reis:</span>
                                <DefaultLink :href="route('admin.products.show', booking.product)">
                                    {{ booking.product?.name }}
                                </DefaultLink>
                            </div>
                            <div class="flex gap-2 justify-between">
                                <span class="font-semibold">Vertrek:</span>
                                <span class="">{{ booking.departure_date_formatted }}</span>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <div class="grid">
                                <span class="font-semibold">Contact gegevens:</span>
                                <span class="">{{ booking.contact?.name }}</span>
                                <span class="whitespace-pre-line">{{ booking.contact?.address }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reizigers -->
                <div class="grid gap-8 laptop:grid-cols-2">
                    <div>
                        <p class="font-semibold text-gray-700">Volwassenen</p>
                        <div class="mt-2 space-y-2">
                            <div v-for="(adult, index) in booking.adults" :key="index">
                                <Booker :booker="adult" :index="index" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700">Kinderen</p>
                        <div class="mt-2 space-y-2">
                            <div v-for="(child, index) in booking.children" :key="index">
                                <Booker :booker="child" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rechter kolom (1/3 breed) -->
            <div class="bg-white p-8 rounded-2xl shadow-lg space-y-4">
                <h3 class="text-xl font-semibold text-gray-800">Acties</h3>
                <!-- Extra info of buttons kan hier -->
                <p class="text-gray-600">Nog wat handige details of extra controls...</p>
            </div>

        </div>
    </Admin>

</template>
<script setup>
import { ref } from 'vue'

const props = defineProps({
    booking: Object
})


const openIndex = ref(null)

const toggle = (index) => {
    openIndex.value = openIndex.value === index ? null : index
}

</script>
