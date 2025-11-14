<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
});

function submit() {
    form.post(route("admin.countries.store"), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <form @submit.prevent="submit" class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-2 gap-8">
                <!-- Header Section -->
                <div class="laptop:col-span-2 bg-white py-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-700">
                                Nieuw Land
                            </h1>
                            <p class="mt-1 text-sm text-gray-700/50">
                                Voeg een nieuw land toe aan de database
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Main Content Section -->
                <div class="laptop:col-span-2">
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Land Informatie</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Voer de naam van het land in</p>
                        </div>
                        <div class="p-6">
                            <Input
                                @input="form.clearErrors('name')"
                                type="text"
                                name="name"
                                label="Land naam"
                                :required="true"
                                v-model="form.name"
                                :feedback="form.errors.name"
                                :forceShow="!!form.errors.name"
                            />
                        </div>
                    </section>
                </div>

                <!-- Footer Actions -->
                <div
                    class="laptop:col-span-2 flex items-center justify-between border-t border-gray-200 bg-white rounded-lg mt-6 p-6 shadow-sm">
                    <p class="text-sm text-gray-700/30">
                        <span v-if="form.isDirty" class="text-status-warning font-medium">
                            Er zijn niet opgeslagen wijzigingen
                        </span>
                        <span v-else class="text-status-success">
                            Alles opgeslagen
                        </span>
                    </p>
                    <FormSubmit :form="form" label="Land Opslaan" @submit="submit" />
                </div>
            </div>
        </form>
    </Admin>
</template>
