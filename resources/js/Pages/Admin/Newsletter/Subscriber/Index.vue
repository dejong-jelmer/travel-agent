<script setup>
const props = defineProps({
    subscribers: Object,
});
</script>
<template>
    <Admin :links="subscribers.links">
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">{{ $t('admin.newsletter.subscribers.index.title') }}</h1>
        </div>
        <template v-if="subscribers.data.length > 0">
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.subscribers.index.table_headers.name') }} </th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.subscribers.index.table_headers.email') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.subscribers.index.table_headers.status') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.newsletter.subscribers.index.table_headers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(subscriber, index) in subscribers.data" :key="index"
                            class="hover:bg-gray-100 transition">
                            <td class="py-4 px-6 text-center">{{ subscriber.id }}</td>
                            <td class="py-4 px-6 text-center">{{ subscriber.name || '-' }}</td>
                            <td class="py-4 px-6 text-center">{{ subscriber.email || '-' }}</td>
                            <td class="py-4 px-6 text-center">
                                <SubscriberStatusBadge class="w-full mx-auto" :status="subscriber.status">
                                    <span >{{ subscriber.status_label || '-' }}</span>
                                </SubscriberStatusBadge>
                            </td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <DropdownMenu>
                                    <template #default="{ MenuItem }">
                                        <component v-if="subscriber.status === 'expired'" :is="MenuItem">
                                            <IconLink icon="Refresh"
                                                :href="route('admin.newsletter.subscribers.resend', subscriber)"
                                                :showConfirm="true"
                                                :prompt="$t('admin.newsletter.subscribers.actions.resend_confirm')"
                                                v-tippy="$t('admin.newsletter.subscribers.actions.resend')" />
                                        </component>
                                        <component :is="MenuItem">
                                            <IconLink type="delete" icon="Trash2"
                                                :href="route('admin.newsletter.subscribers.destroy', subscriber)"
                                                method="delete" :showConfirm="true"
                                                :prompt="$t('admin.newsletter.subscribers.actions.delete_confirm')"
                                                v-tippy="$t('admin.newsletter.subscribers.actions.delete')" />
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
                <p>{{ $t('admin.newsletter.subscribers.index.no_subscribers') }}</p>
            </div>
        </template>
    </Admin>
</template>
