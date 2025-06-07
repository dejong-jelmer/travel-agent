<script setup>
import IconLink from '@/Pages/Layouts/Components/IconLink.vue';
import { ref } from "vue";
import {
    Input,
    Text,
    ImageUploader,
} from './Form';

const isDirty = ref(false);
const props = defineProps({
    errors: Object,
    form: Object,
});
</script>
<template>
    <form @submit.prevent="submit" @change="isDirty = true">
        <div class="space-y-4">
            <div class=" flex justify-end">
                <IconLink icon="Save" @click="submit" v-tippy="'Sla reisplan op'" class="mx-0" :class="{ 'animate-bounce': isDirty }" />
            </div>
            <div class="p-4 tablet:p-8 laptop:p-10 space-y-6">
                <div class="flex flex-col tablet:flex-row tablet:space-x-6 space-y-6 tablet:space-y-0">
                    <div class="w-full tablet:w-1/2 grid gap-4">
                        <ImageUploader :label="form.image ? 'Afbeelding wijzigen' : 'Selecteer een afbeelding'"
                            v-model="form.image" />
                    </div>
                    <div class="grid gap-6 w-full tablet:w-1/2">
                        <Input type="text" name="title" label="Titel" :required="true" v-model="form.title"
                            :feedback="errors.title" />
                        <Input type="text" name="subtitle" label="Subtitel" :required="true" v-model="form.subtitle"
                            :feedback="errors.subtitle" />
                        <Text name="description" label="Omschrijving" :required="true" v-model="form.description" />
                        <Input type="text" name="remark" label="Opmerking" :required="false" v-model="form.remark"
                            :feedback="errors.remark" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
<script>
export default {
    components: {
        ImageUploader,
        Input,
        Text,
    },
    methods: {
        submit() {
            this.$emit('submit');
        }
    }
}
</script>
