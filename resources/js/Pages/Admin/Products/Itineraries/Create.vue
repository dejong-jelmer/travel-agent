<script setup>
import { useForm } from "@inertiajs/vue3";

import Layout from "@/Pages/Layouts/Layout.vue";
import { Input, Text, ImageUploader } from '@/Pages/Layouts/Components/Form';
import ItineraryForm from '@/Pages/Layouts/Components/ItineraryForm.vue';


const props = defineProps({
    product: Object,
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
      uploadedImage: null,
      form: {
        title: '',
        subtitle: '',
        description: '',
        image: '',
        remark: '',
      },
    }
  },

  methods: {
    submit() {
        const form = useForm(this.form);
        form.post(route("products.itineraries.store", this.product.id), { forceFormData: true });
    }
  },
};
</script>
