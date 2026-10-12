<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    tripOptions: Array,
    relationshipOptions: Array,
    limits: Object,
});

const form = useForm({
    trip_id: '',
    quote: '',
    body: '',
    author_name: '',
    relationship: '',
    author_context: '',
    travelled_on: null,
    photo: null,
    link_url: '',
    link_label: '',
    is_published: false,
});

function submit() {
    form.post(route("admin.testimonials.store"), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <TestimonialForm :form="form" :trip-options="tripOptions" :relationship-options="relationshipOptions"
                :limits="limits" @submit="submit" />
        </div>
    </Admin>
</template>
