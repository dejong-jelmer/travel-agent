<script setup>
const props = defineProps({
    subscribers: Object,
});
</script>
<template>
    <Admin :links="subscribers.links">
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">Nieuwsbrief Abonnees</h1>
        </div>
        <template v-if="subscribers.data.length > 0">
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">Naam</th>
                            <th class="py-4 px-6 text-center">Email</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-center">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(subscriber, index) in subscribers.data" :key="index"
                            class="hover:bg-gray-100 transition">
                            <td class="py-4 px-6 text-center">{{ subscriber.id }}</td>
                            <td class="py-4 px-6 text-center">{{ subscriber.name || '-' }}</td>
                            <td class="py-4 px-6 text-center">{{ subscriber.email || '-' }}</td>
                            <td class="py-4 px-6 text-center">
                                <SubscriberStatusBadge class="w-1/2 mx-auto" :status="subscriber.status">
                                    <span v-if="subscriber.status === 'unsubscribed'">Uitgeschreven</span>
                                    <span v-else-if="subscriber.status === 'active'">Actief</span>
                                    <span v-else-if="subscriber.status === 'expired'">Verlopen</span>
                                    <span v-else>In afwachting</span>
                                </SubscriberStatusBadge>
                            </td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <DropdownMenu>
                                    <template #default="{ MenuItem }">
                                        <component v-if="subscriber.status === 'expired'" :is="MenuItem">
                                            <IconLink icon="Refresh"
                                                :href="route('admin.newsletter.subscribers.resend', subscriber)"
                                                :showConfirm="true"
                                                prompt="Deze nieuwsbrief abonnee een nieuwe bevestigingsmail sturen?"
                                                v-tippy="'Bevestigingsmail opnieuw versturen'" />
                                        </component>
                                        <component :is="MenuItem">
                                            <IconLink type="delete" icon="Trash2"
                                                :href="route('admin.newsletter.subscribers.destroy', subscriber)"
                                                method="delete" :showConfirm="true"
                                                prompt="Weet je zeker dat je deze nieuwsbrief abonnee wilt verwijderen?"
                                                v-tippy="'Verwijder Abonnee'" />
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
                <p>Er is nog niemand geabboneerd op jouw nieuwsbrief.</p>
            </div>
        </template>
    </Admin>
</template>
