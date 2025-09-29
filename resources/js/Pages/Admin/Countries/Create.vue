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
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <div class="w-full flex justify-end">
                <IconLink icon="Save" @click="submit" v-tippy="'Sla land op'" class="mx-0"
                    :class="{ 'animate-bounce': form.isDirty }" />
            </div>
            <Input @input="form.clearErrors('name')" type="text" name="name" label="Land naam" :required="true"
                v-model="form.name" :feedback="form.errors.name" :forceShow="!!form.errors.name" />
        </div>
    </Admin>
</template>
