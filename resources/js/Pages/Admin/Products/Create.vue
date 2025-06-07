<script setup>
import { reactive } from 'vue'
import { useForm } from "@inertiajs/vue3";
import IconLink from "@/Pages/Layouts/Components/IconLink.vue";
import AdminLayout from "@/Pages/Layouts/AdminLayout.vue";
import ProductForm from "@/Pages/Layouts/Components/ProductForm.vue";

const props = defineProps({
    errors: Object,
    countries: Object,
});

const form = reactive({
    name: "",
    slug: "",
    description: "",
    price: "",
    duration: "",
    countries: [],
    featuredImage: null,
    images: [],
    active: false,
    featured: false,
});

function submit() {
    const submitForm = useForm(form);
    submitForm.post(route("products.store"), { forceFormData: true });
}
</script>

<template>
    <AdminLayout>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ProductForm
                :form="form"
                :errors="errors"
                :countries="countries"
                @submit="submit"
            />
        </div>
    </AdminLayout>
</template>
