<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    blogPost: Object,
    statusOptions: Array,
    tripOptions: Array,
    // The ids of the related trips, as strings
    tripIds: Array,
});

const form = useForm({
    _method: "PUT",
    id: props.blogPost.id,
    title: props.blogPost.title,
    slug: props.blogPost.slug,
    excerpt: props.blogPost.excerpt ?? "",
    body: props.blogPost.body,
    featured_image: props.blogPost.hero_image?.public_url ?? null,
    featured_image_alt: props.blogPost.hero_image?.alt ?? "",
    meta_title: props.blogPost.meta_title ?? "",
    meta_description: props.blogPost.meta_description ?? "",
    status: props.blogPost.status,
    trips: props.tripIds,
});

function submit() {
    form.post(route("admin.posts.update", props.blogPost), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <BlogPostForm :form="form" :status-options="statusOptions" :trip-options="tripOptions" @submit="submit" />
    </Admin>
</template>
