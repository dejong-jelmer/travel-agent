<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    statusOptions: Array,
    tripOptions: Array,
});

const form = useForm({
    title: "",
    excerpt: "",
    summary: "",
    body: "",
    featured_image: null,
    featured_image_alt: "",
    meta_title: "",
    meta_description: "",
    status: "draft",
    trips: [],
});

function submit() {
    form.post(route("admin.posts.store"), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <BlogPostForm :form="form" :status-options="statusOptions" :trip-options="tripOptions" @submit="submit" />
    </Admin>
</template>
