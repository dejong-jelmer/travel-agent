<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    product: Object,
    countries: Object,
});

const form = useForm({
    ...props.product,
    countries: props.product.countries?.map(country => country.id) ?? [],
    featuredImage: props.product.featured_image?.full_path ?? null,
    images: props.product.image_urls ?? [],
});

function submit() {
    form.post(route("admin.products.update", props.product), { forceFormData: true });
}

</script>

<template>
    <Admin>
        <ProductForm :form="form" :countries="countries" @submit="submit" />
    </Admin>
</template>
