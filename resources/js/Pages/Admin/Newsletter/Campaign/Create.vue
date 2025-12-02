<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    statusOptions: Object,
    trips: Object
});

const form = useForm({
    hero_image: "",
    subject: "",
    content: "",
    preview_text: "",
    status: "",
    scheduled_at: "",
    trips: [],
});

function submit() {
    form.post(route("admin.newsletter.campaigns.store"));
}
</script>
<template>
    <Admin>
        <CampaignForm :form="form" :statusOptions="statusOptions" :trips="trips" @submit="submit" />
    </Admin>
</template>
