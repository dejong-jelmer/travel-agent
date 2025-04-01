<script setup>
import { useForm } from "@inertiajs/vue3";
import Layout from "@/Pages/Layouts/Layout.vue";
import ProductForm from '@/Pages/Layouts/Components/ProductForm.vue';


const props = defineProps({
    product: Object,
    errors: Object,
    countries: Object,
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
      form: {
        name: this.product.name,
        description: this.product.description,
        slug: this.product.slug,
        price: this.product.raw_price,
        duration: this.product.duration,
        countries: this.product.countries?.map(country => country.id),
        featuredImage: this.product.featured_image?.path,
        images: this.product.image_urls,
        active: this.product.active,
        featured: this.product.featured,
      },
    }
  },
  methods: {
    submit() {
        const form = useForm(this.form);
        form.post(route("products.update", this.product), { forceFormData: true });
    }
  },
};
</script>
