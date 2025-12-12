<script setup>
import { User } from "lucide-vue-next";
import { computed, toRef } from 'vue'

const props = defineProps({
    booking: { type: Object, required: true },
})

const contact = toRef(props.booking, 'contact')

const formatBookerDetails = (booker) => {
    return `${booker?.full_name} (${formatToDutchDate(parseDutchDate(booker?.birthdate))})`
}

const parseDutchDate = (dateString) => {

    const [day, month, year] = dateString.split('-');
    return new Date(year, month - 1, day);
};

const formatToDutchDate = (date) => {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
};



const bookerOptions = computed(() => {
    if (!props.booking?.travelers?.adults) return []
    return props.booking.travelers.adults.map((adult, index) => ({
        label: formatBookerDetails(adult),
        value: adult
    }))
})


</script>

<template>
    <div key="contact" class="space-y-6">
        <h2 class="text-xl font-bold text-brand-primary">{{ $t('booking_steps.contact.heading') }}</h2>
        <hr class="border-accent-sage/20">

        <div class="space-y-2 p-4 border rounded-lg">
            <div v-if="bookerOptions.length > 1" class="mb-5">
                <div class="grid grid-cols-2 gap-6">
                    <h2 class="text-base font-bold text-brand-primary">{{ $t('booking_steps.contact.main_booker_question') }}</h2>
                    <h2 class="text-base font-bold text-brand-primary">{{ $t('booking_steps.contact.chosen_main_booker') }}</h2>
                    <RadioGroup v-model="booking.main_booker" name="main_booker" :options="bookerOptions" />
                    <p class="font-bold flex items-center border rounded-2xl p-6 bg-slate-50">
                        <User class="w-4 h-4 mr-2 text-brand-light" />
                        {{ formatBookerDetails(props.booking.travelers.adults[props.booking.main_booker]) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-6">
                <Input type="text" name="street" :label="$t('booking_steps.contact.street_label')" :showLabel="true" :required="true"
                    v-model="contact.street" :feedback="booking.errors['contact.street']" :placeholder="$t('booking_steps.contact.street_placeholder')"
                    @keyup="booking.clearErrors('contact.street')" />
                <Input type="number" name="house_number" :label="$t('booking_steps.contact.house_number_label')" :showLabel="true" :required="true"
                    v-model="contact.house_number" :feedback="booking.errors['contact.house_number']"
                    :placeholder="$t('booking_steps.contact.house_number_placeholder')" @keyup="booking.clearErrors('contact.house_number')" />
                <Input type="text" name="addition" :label="$t('booking_steps.contact.addition_label')" :showLabel="true" :required="false"
                    :placeholder="$t('booking_steps.contact.addition_placeholder')" v-model="contact.addition"
                    :feedback="booking.errors['contact.addition']" @keyup="booking.clearErrors('contact.addition')" />
            </div>
            <div class="grid grid-cols-2 gap-6">

                <Input type="text" name="postal_code" :label="$t('booking_steps.contact.postal_code_label')" :showLabel="true" :required="true"
                    v-model="contact.postal_code" :feedback="booking.errors['contact.postal_code']"
                    :placeholder="$t('booking_steps.contact.postal_code_placeholder')" @keyup="booking.clearErrors('contact.postal_code')" />

                <Input type="text" name="city" :label="$t('booking_steps.contact.city_label')" :showLabel="true" :required="true"
                    v-model="contact.city" :placeholder="$t('booking_steps.contact.city_placeholder')" :feedback="booking.errors['contact.city']"
                    @keyup="booking.clearErrors('contact.city')" />
            </div>
            <div class="grid grid-cols-2 gap-6">

                <Input type="tel" name="phone" :label="$t('booking_steps.contact.phone_label')" :showLabel="true" :required="true"
                    v-model="contact.phone" :feedback="booking.errors['contact.phone']" :placeholder="$t('booking_steps.contact.phone_placeholder')"
                    @keyup="booking.clearErrors('contact.phone')" />

                <Input type="text" name="email" :label="$t('booking_steps.contact.email_label')" :showLabel="true" :required="true"
                    v-model="contact.email" :feedback="booking.errors['contact.email']" :placeholder="$t('booking_steps.contact.email_placeholder')"
                    @keyup="booking.clearErrors('contact.email')" />
            </div>
        </div>
    </div>

</template>
