<script setup>
const props = defineProps({
    campaigns: Object,
});
</script>
<template>
    <Admin :links="campaigns.links">
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">{{ $t('admin.newsletter.campaigns.index.title') }}</h1>
            <IconLink v-tippy="'Maak een nieuwe nieuwsbrief campagne'" icon="Plus" type="info"
                :href="route('admin.newsletter.campaigns.create')" />
        </div>
        <template v-if="campaigns.data.length > 0">
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.campaigns.index.table_headers.subject') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.campaigns.index.table_headers.status') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.campaigns.index.table_headers.planned_for') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.campaigns.index.table_headers.sent_at') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.campaigns.index.table_headers.sent_count') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.campaigns.index.table_headers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(campaign, index) in campaigns.data" :key="index"
                            class="hover:bg-gray-100 transition">
                            <td class="py-4 px-6 text-center">{{ campaign.id }}</td>
                            <td class="py-4 px-6 text-center">{{ campaign.subject || '-' }}</td>
                            <td class="py-4 px-6 text-center">
                                <CampaignStatusBadge class="w-full mx-auto" :status="campaign.status">
                                    {{ campaign.status_label }}
                                </CampaignStatusBadge>
                            </td>
                            <td class="py-4 px-6 text-center">{{ campaign.scheduled_at_formatted || '-' }}</td>
                            <td class="py-4 px-6 text-center">{{ campaign.sent_at_formatted || '-' }}</td>
                            <td class="py-4 px-6 text-center">{{ campaign.sent_count || '-' }}</td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <DropdownMenu>
                                    <template #default="{ MenuItem }">
                                        <component :is="MenuItem">
                                            <IconLink icon="Pencil"
                                                :href="route('admin.newsletter.campaigns.edit', campaign)"
                                                v-tippy="$t('admin.newsletter.campaigns.actions.edit')" />
                                        </component>
                                        <component :is="MenuItem">
                                            <IconLink icon="Send" method="post"
                                                :href="route('admin.newsletter.campaigns.send', campaign)"
                                                :showConfirm="true"
                                                :prompt="$t('admin.newsletter.campaigns.actions.send_confirm')"
                                                v-tippy="$t('admin.newsletter.campaigns.actions.send')" />
                                        </component>
                                        <component :is="MenuItem">
                                            <IconLink type="delete" icon="Trash2"
                                                :href="route('admin.newsletter.campaigns.destroy', campaign)"
                                                method="delete" :showConfirm="true"
                                                :prompt="$t('admin.newsletter.campaigns.actions.delete_confirm')"
                                                v-tippy="$t('admin.newsletter.campaigns.actions.delete')" />
                                        </component>
                                    </template>
                                </DropdownMenu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
        <template v-else>
            <div class="p-5">
                <p>{{ $t('admin.newsletter.campaigns.index.no_campaigns') }}</p>
            </div>
        </template>
    </Admin>
</template>
