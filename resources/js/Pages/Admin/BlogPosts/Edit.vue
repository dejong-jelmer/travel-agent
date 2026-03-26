<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    blogPost: Object,
    statusOptions: Array,
});

const form = useForm({
    _method: "PUT",
    id: props.blogPost.id,
    title: props.blogPost.title,
    slug: props.blogPost.slug,
    excerpt: props.blogPost.excerpt ?? "",
    body: props.blogPost.body,
    featured_image: props.blogPost.hero_image?.public_url ?? null,
    meta_title: props.blogPost.meta_title ?? "",
    meta_description: props.blogPost.meta_description ?? "",
    status: props.blogPost.status,
});

function submit() {
    form.post(route("admin.posts.update", props.blogPost), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <BlogPostForm :form="form" :status-options="statusOptions" @submit="submit" />
    </Admin>
</template>
