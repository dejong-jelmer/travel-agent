<script setup>
import { useForm } from "@inertiajs/vue3";
import Layout from "@/Pages/Layouts/Layout.vue";
import {
    Input,
    Select,
    Text,
    Checkbox,
    ImageUploader,
    MultiImageUploader
} from '@/Pages/Layouts/Components/Form';


const props = defineProps({
    product: Object,
    errors: Object,
    countries: Object,
});
</script>

<template>
    {{ errors }}
    <Layout>
        <form  @submit.prevent="submit" >
            <div class="gap-6 bg-white p-8 rounded-lg shadow grid grid-cols-2">
                <div class="space-y-6">
                    <Input type="text" name="name" label="Naam" :required="true" v-model="form.name" :feedback="errors.name"/>
                    <Input type="text" name="slug" label="Slug" :required="true" v-model="form.slug" :feedback="errors.slug"/>
                    <Text name="description" label="Omschrijving" :required="true" v-model="form.description" />
                </div>
                <div class="space-y-6">
                    <Input type="number" name="price" label="Prijs (€)" :required="true" v-model="form.price" :feedback="errors.price" />
                    <Input type="number" name="durantion" label="Duur (dagen)" :required="true" v-model="form.duration" :feedback="errors.duration" />
                    <Select
                        name="country" label="Land"
                        v-model="form.countries"
                        :options="countries"
                        :multiple="true"
                        :required="true"
                        :feedback="errors.countries"
                        :first-option="'Koppel één of meerder landen'"
                    />
                </div>
                <div class="space-y-6">
                    <label class="form-label">Hoofdafbeelding (URL)</label>
                    <ImageUploader v-model="product.image" @image-uploaded="handleImageUploaded" />
                    <div v-if="uploadedImage" class="mt-8">
                        <h2 class="text-xl font-semibold mb-2">Data van geuploade afbeelding:</h2>
                        <p>File Name: {{ uploadedImage.name }}</p>
                        <p>File Size: {{ uploadedImage.size }} bytes</p>
                        <p>File Type: {{ uploadedImage.type }}</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <Checkbox v-model="form.active" name="active" label="Actief" />
                    <Checkbox v-model="form.featured" name="featured" label="Uitgelicht" />
                    <MultiImageUploader v-model="form.images" />
                    {{ product }}
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
    MultiImageUploader,
    Input,
    Text,
    Checkbox
  },
  data() {
    return {
      uploadedImage: null,
      uploadedImages: this.product.image_urls,
      form: {
        name: this.product.name,
        description: this.product.description,
        slug: this.product.slug,
        price: this.product.price,
        duration: this.product.duration,
        countries: this.product.countries?.map(country => country.id),
        image: this.product.image,
        images: this.product.image_urls,
        active: this.product.active,
        featured: this.product.featured,
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
        form.post(route("products.update", this.product), { forceFormData: true });
    }
  },
};
</script>
