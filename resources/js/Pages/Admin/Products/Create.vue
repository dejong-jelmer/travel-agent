<script setup>
import { useForm } from "@inertiajs/vue3";
import IconLink from '@/Pages/Layouts/Components/IconLink.vue';
import Layout from "@/Pages/Layouts/Layout.vue";
import ProductForm from '@/Pages/Layouts/Components/ProductForm.vue';

const props = defineProps({
    errors: Object,
    countries: Object
});
</script>

<template>
    <Layout>
        <ProductForm :form="form" :errors="errors" :countries="countries" @submit="submit" />
    </Layout>
</template>
<script>
export default {
    data() {
        return {
            product: {},
            form: {
                name: '',
                slug: '',
                description: '',
                price: '',
                duration: '',
                countries: [],
                featuredImage: null,
                images: [],
                active: false,
                featured: false,
            },
        }
    },

    methods: {
        submit() {
            const form = useForm(this.form);
            form.post(route("products.store"), { forceFormData: true });
        }
    },
};
</script>
