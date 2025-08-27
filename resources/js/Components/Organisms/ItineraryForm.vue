<script setup>
import { ref } from "vue";

const props = defineProps({
    errors: Object,
    form: Object,
    meals: Object,
    transport: Object,
});

const isDirty = ref(false);
const emit = defineEmits(['submit'])

const submit = () => {
  emit('submit')
}
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
                        <Input type="text" name="accommodation" label="Verblijf" :required="true" v-model="form.accommodation"
                            :feedback="errors.accommodation" />
                        <Input type="text" name="activities" label="Activitieten" :required="true" v-model="form.activities"
                            :feedback="errors.activities" />
                    </div>
                    <div class="grid gap-6 w-full tablet:w-1/2">
                        <Input type="text" name="title" label="Titel" :required="true" v-model="form.title"
                            :feedback="errors.title" />
                        <Input type="text" name="location" label="Locatie" :required="true" v-model="form.location"
                            :feedback="errors.location" />
                        <TextArea name="description" label="Omschrijving" :required="true" v-model="form.description" />
                        <Input type="text" name="remark" label="Opmerking" :required="false" v-model="form.remark"
                            :feedback="errors.remark" />
                        <Select name="transport" label="Vervoer" v-model="form.transport" :multiple="true"
                            :required="false" :options="transport" :feedback="errors.tranport"
                            :placeholder="'Kies vervoer types'" />
                        <Select name="meals" label="Maaltijden" v-model="form.meals" :multiple="true"
                            :required="false" :options="meals" :feedback="errors.meals"
                            :placeholder="'Kies inbegrepen maaltijden'" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
