<script setup>
const props = defineProps({
    campaigns: Object,
});
</script>
<template>
    <Admin :links="campaigns.links">
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">Nieuwsbrief Campagnes</h1>
            <IconLink v-tippy="'Maak een nieuwe nieuwsbrief campagne'" icon="Plus" type="info" :href="route('admin.newsletter.campaigns.create')" />
        </div>
        <template v-if="campaigns.data.length > 0">
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">Onderwerp</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-center">Gepland voor</th>
                            <th class="py-4 px-6 text-center">Verzonden op</th>
                            <th class="py-4 px-6 text-center">Aantal verzonden</th>
                            <th class="py-4 px-6 text-center">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(campaign, index) in campaigns.data" :key="index"
                            class="hover:bg-gray-100 transition">
                            <td class="py-4 px-6 text-center">{{ campaign.id }}</td>
                            <td class="py-4 px-6 text-center">{{ campaign.subject || '-' }}</td>
                            <td class="py-4 px-6 text-center">
                                <CampaignStatusBadge class="w-1/2 mx-auto" :status="campaign.status">
                                    {{ campaign.status }}
                                </CampaignStatusBadge>
                            </td>
                            <td class="py-4 px-6 text-center">{{ campaign.scheduled_at_formatted || '-' }}</td>
                            <td class="py-4 px-6 text-center">{{ campaign.sent_at_formatted || '-' }}</td>
                            <td class="py-4 px-6 text-center">{{ campaign.sent_count || '-' }}</td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <IconLink class="mx-auto" icon="Pencil"
                                        :href="route('admin.newsletter.campaigns.edit', campaign)" v-tippy="'Bewerk nieuwsbrief campagne'" />
                                <IconLink class="mx-auto" icon="Send" method="post"
                                    :href="route('admin.newsletter.campaigns.send', campaign)"
                                    :showConfirm="true"
                                    prompt="Je staat op het punt deze campagne naar al je abbonees te versturen. Weet je zeker dat je deze nieuwsbrief campagne wilt versturen?"
                                    v-tippy="'Verstuur campagne'" />
                                <IconLink class="mx-auto" type="delete" icon="Trash2"
                                    :href="route('admin.newsletter.campaigns.destroy', campaign)" method="delete"
                                    :showConfirm="true"
                                    prompt="Weet je zeker dat je deze nieuwsbrief campagne wilt verwijderen?"
                                    v-tippy="'Verwijder campagne'" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
        <template v-else>
            <div class="p-5">
                <p>Je hebt nog geen nieuwsbrief campagnes.</p>
            </div>
        </template>
    </Admin>
</template>
