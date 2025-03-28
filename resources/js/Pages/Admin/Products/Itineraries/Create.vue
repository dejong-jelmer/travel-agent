<script setup>
import { useForm } from "@inertiajs/vue3";

import Layout from "@/Pages/Layouts/Layout.vue";
import { Input, Text, ImageUploader } from '@/Pages/Layouts/Components/Form';

const props = defineProps({
    product: Object,
    errors: Object,
});
</script>

<template>
    <Layout>
        <form  @submit.prevent="submit" >
            <div class="gap-6 bg-white p-8 rounded-lg shadow grid grid-cols-2">
                <div class="space-y-6">
                    <Input type="text" name="title" label="Titel" :required="true" v-model="form.title" :feedback="errors.title"/>
                    <Input type="text" name="subtitle" label="Subtitel" :required="true" v-model="form.subtitle" :feedback="errors.subtitle"/>
                    <Text name="description" label="Omschrijving" :required="true" v-model="form.description" />
                </div>
                <div class="space-y-6">
                    <Input type="text" name="remark" label="Opmerking" v-model="form.remark" :feedback="errors.remark"/>
                    <label class="form-label">Hoofdafbeelding (URL)</label>
                    <ImageUploader v-model="form.image" @image-uploaded="handleImageUploaded" />
                </div>
                <button type="submit" class="form-button">Opslaan</button>
            </div>
        </form>
    </Layout>
</template>
<script>
export default {
  components: {
    ImageUploader,
    Input,
    Text,
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
  watch: {
    uploadedImages: {
        handler(images) {
            this.form.images = images;
        },
        deep: true,
    },
  },
  methods: {
    handleImageUploaded(file) {
        this.form.image = this.uploadedImage = file;
    },
    submit() {
        const form = useForm(this.form);
        form.post(route("products.itineraries.store", this.product.id), { forceFormData: true });
    }
  },
};
</script>
