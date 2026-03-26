<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    statusOptions: Array,
});

const form = useForm({
    title: "",
    excerpt: "",
    body: "",
    featured_image: null,
    meta_title: "",
    meta_description: "",
    status: "draft",
});

function submit() {
    form.post(route("admin.posts.store"), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <BlogPostForm :form="form" :status-options="statusOptions" @submit="submit" />
    </Admin>
</template>
