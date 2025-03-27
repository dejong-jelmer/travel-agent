<script setup>
import { Link, useForm } from "@inertiajs/vue3";

import Layout from "@/Pages/Layouts/Layout.vue";
import { Input, Text, ImageUploader } from '@/Pages/Layouts/Components/Form';

const props = defineProps({
    itinerary: Object,
    errors: Object,
});
</script>

<template>
    <Layout>
        {{ errors }}
        <form @submit.prevent="submit">
            <div class="gap-6 bg-white p-8 rounded-lg shadow grid laptop:grid-cols-2">
                <div class="space-y-6">
                    <Input type="text" name="title" label="Titel" :required="true" v-model="form.title"
                        :feedback="errors.title" />
                    <Input type="text" name="subtitle" label="Subtitel" :required="true" v-model="form.subtitle"
                        :feedback="errors.subtitle" />
                    <Text name="description" label="Omschrijving" :required="true" v-model="form.description" />
                </div>
                <div class="space-y-6">
                    <Input type="text" name="remark" label="Opmerking" v-model="form.remark"
                        :feedback="errors.remark" />
                    <label class="form-label">Reisplan afbeelding toevoegen</label>
                    <ImageUploader v-model="itinerary.image" @image-uploaded="handleImageUploaded" />

                </div>
                <button type="submit" class="form-button">Opslaan</button>
                <Link as="button" class="form-button bg-red-700 hover:bg-red-400"
                    :href="route('itineraries.destroy', itinerary)" method="delete" @click.prevent="confirmDelete">
                Verwijderen
                </Link>
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
            form.post(route("itineraries.update", this.itinerary.id), { forceFormData: true });
        },
        confirmDelete(event) {
            if (!confirm('Weet je zeker dat je dit reisplan wilt verwijderen?')) {
                event.preventDefault();
            }
        },
    }
};
</script>
