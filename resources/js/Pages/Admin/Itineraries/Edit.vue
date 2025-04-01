<script setup>
import { useForm } from "@inertiajs/vue3";
import Layout from "@/Pages/Layouts/Layout.vue";
import { Input, Text, ImageUploader } from '@/Pages/Layouts/Components/Form';
import ItineraryForm from '@/Pages/Layouts/Components/ItineraryForm.vue';

const props = defineProps({
    itinerary: Object,
    errors: Object,
});
</script>

<template>
    <Layout>
        <ItineraryForm :form="form" :errors="errors" @submit="submit" />
    </Layout>
</template>
<script>
export default {
    components: {
        ItineraryForm,
    },
    data() {
        return {
            form: {
                title: this.itinerary.title,
                subtitle: this.itinerary.subtitle,
                description: this.itinerary.description,
                image: this.itinerary.image?.path,
                remark: this.itinerary.remark,
            },
        }
    },
    methods: {
        submit() {
            const form = useForm(this.form);
            form.post(route("itineraries.update", this.itinerary.id), { forceFormData: true });
        },
    }
};
</script>
