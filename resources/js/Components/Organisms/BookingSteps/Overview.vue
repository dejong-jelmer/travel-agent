<script setup>
import { Briefcase, Calendar, Euro, Train, Users, User, Phone, Mail, AtSign } from "lucide-vue-next";
import { useDateFormatter } from '@/Composables/useDateFormatter.js'
const { formattedDate } = useDateFormatter();

const props = defineProps({
    booking: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <div key="overview" class="space-y-6">
        <h2 class="text-xl font-bold text-brand-primary">{{ $t('booking_steps.overview.heading') }}</h2>
        <hr class="border-accent-sage/20">

        <div class="p-6 bg-white rounded-2xl shadow-md space-y-6">
            <!-- Reis -->
            <section class="w-[80%]">
                <h2 class="text-xl font-semibold text-brand-primary mb-3 flex items-center gap-2">
                    {{ $t('booking_steps.overview.summary_heading') }}
                </h2>
                <div class="grid gap-1 ml-4">
                    <div class="flex items-center">
                        <Briefcase class="inline w-4 h-4 mr-2 text-brand-light" />
                        <span class="flex-1 flex items-center gap-2">
                            <span>{{ $t('booking_steps.overview.trip_label') }}</span>
                            <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                            <strong>{{ booking.trip.name }}</strong>
                        </span>
                    </div>

                    <div class="flex items-center">
                        <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                        <span class="flex-1 flex items-center gap-2">
                            <span>{{ $t('booking_steps.overview.total_price') }}</span>
                            <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                            <span class="font-bold">€ {{ booking.trip.price }}</span>
                        </span>
                    </div>

                    <div class="flex items-center">
                        <Calendar class="inline w-4 h-4 mr-2 text-brand-light" />
                        <span class="flex-1 flex items-center gap-2">
                            <span>{{ $t('booking_steps.overview.total_duration') }}</span>
                            <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                            <span class="font-bold">{{ booking.trip.duration }} {{ $t('booking_steps.overview.days') }}</span>
                        </span>
                    </div>

                    <div class="flex items-center">
                        <Train class="inline w-4 h-4 mr-2 text-brand-light" />
                        <span class="flex-1 flex items-center gap-2">
                            <span>{{ $t('booking_steps.overview.departure_date') }}</span>
                            <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                            <span class="font-bold">{{ formattedDate(booking.departure_date) || $t('booking_steps.overview.no_date_chosen') }}</span>
                        </span>
                    </div>
                </div>

            </section>

            <!-- Reizigers -->
            <section>
                <h2 class="text-xl font-semibold text-brand-primary mb-3 flex items-center gap-2">
                    <Users class="w-5 h-5 text-brand-light" /> {{ $t('booking_steps.overview.travelers_heading') }}
                </h2>
                <div class="grid grid-cols-1 tablet:grid-cols-2 gap-3 ml-4">
                    <!-- Volwassenen -->
                    <div v-if="booking.travelers.adults.length">
                        <h3 class="font-medium text-brand-light">
                            {{ $t('booking_steps.overview.adults_label') }} ({{ booking.participants.adults }})
                        </h3>
                        <ul class="ml-4 mt-1 space-y-0.5 list-disc">
                            <li v-for="(adult, i) in booking.travelers.adults" :key="i">
                                {{ adult.full_name }}
                                ({{ adult.birthdate }} – {{ adult.nationality }})
                            </li>
                        </ul>
                    </div>

                    <!-- Kinderen -->
                    <div v-if="booking.travelers.children.length">
                        <h3 class="font-medium text-brand-light">
                            {{ $t('booking_steps.overview.children_label') }} ({{ booking.participants.children }})
                        </h3>
                        <ul class="ml-4 mt-1 space-y-0.5 list-disc">
                            <li v-for="(child, i) in booking.travelers.children" :key="i">
                                {{ child.full_name }}
                                ({{ child.birthdate }} – {{ child.nationality }})
                            </li>
                        </ul>
                    </div>
                </div>

                <p v-if="!booking.travelers.adults.length && !booking.travelers.children.length"
                    class="text-gray-500 mt-2">
                    {{ $t('booking_steps.overview.no_travelers') }}
                </p>
            </section>

            <!-- Contact -->
            <section>
                <h2 class="text-xl font-semibold text-brand-primary mb-3 flex items-center gap-2">
                    <Mail class="w-5 h-5 text-brand-light" /> {{ $t('booking_steps.overview.contact_info_heading') }}
                </h2>
                <div class="grid grid-cols-1 tablet:grid-cols-2 gap-2 ml-4">

                    <!-- Hoofdboeker -->
                    <address class="not-italic">
                        <p class="flex items-center">
                            <User class="w-4 h-4 mr-2 text-brand-light" /> {{
                                booking.travelers.adults?.[booking.main_booker]?.full_name }}
                        </p>
                        <p class="ml-6">
                            <!-- Adres -->
                            {{ booking.contact.street }} {{ booking.contact.house_number }} {{ booking.contact.addition
                            }}
                            <br>
                            {{ booking.contact.postal_code }} <span class="uppercase">{{ booking.contact.city }}</span>
                        </p>
                    </address>
                    <div>
                        <!-- Telefoon -->
                        <p class="flex items-center">
                            <Phone class="w-4 h-4 mr-2 text-brand-light" />
                            {{ booking.contact.phone }}
                        </p>

                        <!-- Email -->
                        <p class="flex items-center">
                            <AtSign class="w-4 h-4 mr-2 text-brand-light" />
                            {{ booking.contact.email }}
                        </p>
                    </div>
                </div>
            </section>
            <section class="grid gap-4 border-2 bg-slate-50 rounded-2xl p-6">

                <!-- Bevestiging -->
                <span @click="booking.clearErrors('has_confirmed')">
                    <Checkbox v-model="booking.has_confirmed" :feedback="booking.errors.has_confirmed">
                        {{ $t('booking_steps.overview.confirm_data') }}
                    </Checkbox>
                </span>
                <span @click="booking.clearErrors('has_accepted_conditions')">
                    <Checkbox v-model="booking.has_accepted_conditions" :feedback="booking.errors.has_accepted_conditions">
                        <span v-html="$t('booking_steps.overview.accept_terms')"></span>
                    </Checkbox>
                </span>
            </section>
        </div>
        <div v-if="Object.keys(booking.errors).length > 0" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <h3 class="text-sm font-semibold text-red-800 mb-2">
                {{ $t('booking_steps.overview.errors_found', { count: Object.keys(booking.errors).length }) }}
            </h3>
            <ul class="text-sm text-red-700 space-y-1">
                <li v-for="(error, key) in booking.errors" :key="key">
                    • {{ error }}
                </li>
            </ul>
        </div>
    </div>
</template>
