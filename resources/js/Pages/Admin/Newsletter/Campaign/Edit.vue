<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    campaign: Object,
    trips: Object,
    statusOptions: Object
});

const form = useForm({
    ...props.campaign,
    hero_image: props.campaign.hero_image?.public_url ?? null,
    trips: props.campaign.trips?.map((trip) => {
        return trip['id'];
    }),
});

function submit() {
    form.post(route("admin.newsletter.campaigns.update", props.campaign), { forceFormData: true });
}
</script>
<template>
    <Admin>
        <CampaignForm :form="form" :statusOptions="statusOptions" :trips="trips" @submit="submit" />
    </Admin>
</template>
