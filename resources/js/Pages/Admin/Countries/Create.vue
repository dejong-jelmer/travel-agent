<script setup>
import { reactive, ref } from 'vue'
import { useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Pages/Layouts/AdminLayout.vue";
import IconLink from '@/Pages/Layouts/Components/IconLink.vue';
import { Input } from '@/Pages/Layouts/Components/Form';

const emit = defineEmits(['submit']);
const isDirty = ref(false);
const props = defineProps({
    errors: Object,
    countries: Object,
});

const form = reactive({
    name: ""
});

function submit() {
    const submitForm = useForm(form);
    submitForm.post(route("countries.store"), { forceFormData: true });
}
</script>

<template>
    <AdminLayout>
        <form @submit.prevent="submit" @change="isDirty = true">

            <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
                <div class="w-full flex justify-end">
                    <IconLink icon="Save" @click="emit('submit')" v-tippy="'Sla land op'" class="mx-0"
                        :class="{ 'animate-bounce': isDirty }" />
                </div>
                <Input type="text" name="name" label="Land naam" :required="true" v-model="form.name"
                    :feedback="errors.name" />
            </div>
        </form>
    </AdminLayout>
</template>
