<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    testimonial: Object,
    tripOptions: Array,
    relationshipOptions: Array,
    limits: Object,
});

const form = useForm({
    _method: "PUT",
    id: props.testimonial.id,
    // A string, the value type of the select; empty for a general testimonial
    trip_id: props.testimonial.trip_id ? String(props.testimonial.trip_id) : '',
    quote: props.testimonial.quote,
    body: props.testimonial.body ?? '',
    author_name: props.testimonial.author_name,
    relationship: props.testimonial.relationship,
    author_context: props.testimonial.author_context ?? '',
    travelled_on: props.testimonial.travelled_on,
    photo: props.testimonial.image?.public_url ?? null,
    link_url: props.testimonial.link_url ?? '',
    link_label: props.testimonial.link_label ?? '',
    is_published: props.testimonial.is_published,
});

function submit() {
    form.post(route("admin.testimonials.update", props.testimonial), { forceFormData: true });
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
