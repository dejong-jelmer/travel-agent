<script setup>
import { reactive } from 'vue'
import { EllipsisVertical } from 'lucide-vue-next'

const props = defineProps({
    bookings: Object
})
const showActions = reactive({});

</script>
<template>
    <Admin :links="bookings.links">
        <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
            <template v-if="bookings.data.length > 0">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">Referentie</th>
                            <th class="py-4 px-6 text-center">Reis</th>
                            <th class="py-4 px-6 text-center">Vertrekdatum</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-center">Betaling</th>
                            <th class="py-4 px-6 text-center">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(booking, index) in bookings.data" :key="index" class="transition hover:bg-gray-100">
                            <td class="py-4 px-6 text-center">{{ booking.id }}</td>
                            <td class="py-4 px-6 text-center">{{ booking.reference }}</td>
                            <td class="py-4 px-6 text-center">{{ booking.product?.name ?? '-' }}</td>
                            <td class="py-4 px-6 text-center">{{ booking.departure_date_formatted }}</td>
                            <td class="py-4 px-6 text-center">
                                <StatusBadge :status="booking.status">{{ booking.status }}</StatusBadge>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <PaymentStatusBadge :status="booking.payment_status">{{ booking.payment_status }}</PaymentStatusBadge>
                            </td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <div class="w-fit mx-auto" v-tippy="`Meer opties`">
                                    <button class="info-button"
                                        @click="showActions[booking.id] = !showActions[booking.id]">
                                        <EllipsisVertical class="h-5" />
                                    </button>
                                </div>
                                <div v-if="showActions[booking.id]" class="space-y-2">
                                    <IconLink class="mx-auto" icon="Eye" :href="route('admin.bookings.show', booking)"
                                        v-tippy="'Bekijk boeking'" />
                                    <IconLink class="mx-auto" icon="Pencil"
                                        :href="route('admin.bookings.edit', booking)" v-tippy="'Bewerk boeking'" />
                                    <IconLink class="mx-auto" type="delete" icon="Trash2"
                                        :href="route('admin.bookings.destroy', booking)" method="delete"
                                        :showConfirm="true" prompt="Weet je zeker dat je deze boeking wilt verwijderen?"
                                        v-tippy="'Verwijder boeking!'" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
            <template v-else>
                <div class="p-5">
                    <p>Er zijn nog geen boekingen.</p>
                </div>
            </template>
        </div>
    </Admin>
</template>
