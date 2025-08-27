<script setup>
import { reactive } from 'vue'
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    product: Object,
    errors: Object,
    countries: Object,
});

const form = reactive({
    name: props.product.name,
    description: props.product.description,
    slug: props.product.slug,
    price: props.product.raw_price,
    duration: props.product.duration,
    countries: props.product.countries?.map(country => country.id),
    featuredImage: props.product.featured_image?.path,
    images: props.product.image_urls,
    active: props.product.active,
    featured: props.product.featured,
})

function submit() {
    const submitForm = useForm(form);
    submitForm.post(route("products.update", props.product), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ProductForm :form="form" :errors="errors" :countries="countries" @submit="submit" />
        </div>
    </Admin>
</template>
