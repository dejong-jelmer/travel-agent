<script setup>
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

const props = defineProps({
    settings: { type: Object, required: true },
});

const { t } = useI18n();

const form = useForm({
    booking_season_end: props.settings.booking_season_end
        ? new Date(props.settings.booking_season_end + 'T00:00:00')
        : null,
});

function submit() {
    form.transform((data) => ({
        ...data,
        booking_season_end: data.booking_season_end
            ? new Date(data.booking_season_end).toISOString().split('T')[0]
            : null,
    })).put(route('admin.settings.update'));
}
</script>

<template>
    <Admin>
        <form @submit.prevent="submit" class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-2 gap-8">
                <!-- Header -->
                <div class="laptop:col-span-2 bg-white py-10">
                    <h1 class="text-3xl font-bold text-gray-700">
                        {{ t('forms.settings.heading') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-700/50">
                        {{ t('forms.settings.subheading') }}
                    </p>
                </div>

                <!-- Booking Season Section -->
                <div class="space-y-8">
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">
                                {{ t('forms.settings.fields.booking_season_end.label') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-700/30">
                                {{ t('forms.settings.fields.booking_season_end.help') }}
                            </p>
                        </div>
                        <div class="p-6 space-y-4">
                            <DatePicker
                                v-model="form.booking_season_end"
                                :min-date="new Date()"
                                :feedback="form.errors.booking_season_end"
                            />
                        </div>
                    </section>
                </div>
            </div>

            <FormFooter
                :form="form"
                :label="t('forms.settings.submit.update')"
                @submit="submit"
            />
        </form>
    </Admin>
</template>
