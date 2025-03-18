<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import Layout from "@/Pages/Layouts/Layout.vue";
import Input from "@/Pages/Layouts/Components/Form/Input.vue";
import Text from "@/Pages/Layouts/Components/Form/Text.vue";
import ImageUploader from "@/Pages/Layouts/Components/Form/ImageUploader.vue";

const props = defineProps({
    itinerary: Object,
    errors: Object,
});
</script>

<template>
    <Layout>
        <form  @submit.prevent="submit" >
            <div class="gap-6 bg-white p-8 rounded-lg shadow grid grid-cols-2">
                <div class="space-y-6">
                    <Input type="text" name="title" label="Titel" v-model="form.title" :feedback="errors.title"/>
                    <Input type="text" name="subtitle" label="Subtitel" v-model="form.subtitle" :feedback="errors.subtitle"/>
                    <Text name="description" label="Omschrijving" v-model="form.description" />
                </div>
                <div class="space-y-6">
                    <Input type="text" name="remark" label="Remark" v-model="form.remark" :feedback="errors.remark"/>
                    <label class="form-label">Hoofdafbeelding (URL)</label>
                    <ImageUploader v-model="itinerary.image" @image-uploaded="handleImageUploaded" />
                    <div v-if="uploadedImage" class="mt-8">
                        <h2 class="text-xl font-semibold mb-2">Data van geuploade afbeelding:</h2>
                        <p>File Name: {{ uploadedImage.name }}</p>
                        <p>File Size: {{ uploadedImage.size }} bytes</p>
                        <p>File Type: {{ uploadedImage.type }}</p>
                    </div>
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
        title: this.itinerary.title,
        subtitle: this.itinerary.subtitle,
        description: this.itinerary.description,
        image: this.itinerary.image,
        remark: this.itinerary.remark,
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
        console.log(form);
        // form.post(route("products.update", this.product.id), { forceFormData: true });
    }
  },
};
</script>
