<script setup>
import { useForm } from "@inertiajs/vue3";
import Layout from "@/Pages/Layouts/Layout.vue";
import Input from "@/Pages/Layouts/Components/Form/Input.vue";
import Text from "@/Pages/Layouts/Components/Form/Text.vue";
import Select from "@/Pages/Layouts/Components/Form/Select.vue";
import ImageUploader from "@/Pages/Layouts/Components/Form/ImageUploader.vue";
import Checkbox from "@/Pages/Layouts/Components/Form/Checkbox.vue";
import MultiImageUploader from "@/Pages/Layouts/Components/Form/MultiImageUploader.vue";


const props = defineProps({
    errors: Object,
    countries: Object
});
</script>

<template>
    <Layout>
        <form  @submit.prevent="submit" >
            <div class="gap-6 bg-white p-8 rounded-lg shadow grid grid-cols-2">
                <div class="space-y-6">
                    <Input type="text" name="name" label="Naam" v-model="form.name" :feedback="errors.name"/>
                    <Input type="text" name="slug" label="Slug" v-model="form.slug" :feedback="errors.slug"/>
                    <Text name="description" label="Omschrijving" v-model="form.description" />
                </div>
                <div class="space-y-6">
                    <Input type="number" name="price" label="Prijs (€)" v-model="form.price" :feedback="errors.price" />
                    <Input type="number" name="duration" label="Duur (dagen)" v-model="form.duration" :feedback="errors.duration" />
                    <Select
                        name="country" label="Land"
                        v-model="form.countries"
                        :multiple="true"
                        :options="countries"
                        :feedback="errors.countries"
                        :first-option="'Koppel één of meerder landen'"
                    />
                </div>
                <div class="space-y-6">
                    <label class="form-label">Hoofdafbeelding (URL)</label>
                    <ImageUploader v-model="form.image" @image-uploaded="handleImageUploaded" />
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
                    <MultiImageUploader v-model="uploadedImages" />
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
      uploadedImages: [],
      product: {},
      form: {
        name: '',
        slug: '',
        description: '',
        price: '',
        duration: '',
        countries: [],
        image: '',
        images: '',
        active: false,
        featured: false,
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

        form.post(route("products.store"), { forceFormData: true });
    }
  },
};
</script>
