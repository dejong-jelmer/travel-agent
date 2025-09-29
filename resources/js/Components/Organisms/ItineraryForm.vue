<script setup>
const props = defineProps({
    form: Object,
    meals: Object,
    transport: Object,
});

const emit = defineEmits(['submit'])

const submit = () => {
    emit('submit')
}
</script>
<template>
    <div class="space-y-4">
        <div class=" flex justify-end">
            <IconLink icon="Save" @click="submit" v-tippy="'Sla reisplan op'" class="mx-0"
                :class="{ 'animate-bounce': form.isDirty }" />
        </div>
        <div class="p-4 tablet:p-8 laptop:p-10 space-y-6">
            <div class="flex flex-col tablet:flex-row tablet:space-x-6 space-y-6 tablet:space-y-0">
                <div class="w-full tablet:w-1/2 grid gap-4">
                    <ImageUploader :label="form.image ? 'Afbeelding wijzigen' : 'Selecteer een afbeelding'"
                        v-model="form.image" />
                    <Input type="text" name="accommodation" label="Verblijf" :required="true"
                        v-model="form.accommodation" :feedback="form.errors.accommodation" />
                    <Input type="text" name="activities" label="Activitieten" :required="true" v-model="form.activities"
                        :feedback="form.errors.activities" />
                </div>
                <div class="grid gap-6 w-full tablet:w-1/2">
                    <Input type="text" name="title" label="Titel" :required="true" v-model="form.title"
                        :feedback="form.errors.title" />
                    <Input type="text" name="location" label="Locatie" :required="true" v-model="form.location"
                        :feedback="form.errors.location" />
                    <TextArea name="description" label="Omschrijving" :required="true" v-model="form.description" />
                    <Input type="text" name="remark" label="Opmerking" :required="false" v-model="form.remark"
                        :feedback="form.errors.remark" />
                    <Select name="transport" label="Vervoer" v-model="form.transport" :multiple="true" :required="false"
                        :options="transport" :feedback="form.errors.tranport" :placeholder="'Kies vervoer types'" />
                    <Select name="meals" label="Maaltijden" v-model="form.meals" :multiple="true" :required="false"
                        :options="meals" :feedback="form.errors.meals" :placeholder="'Kies inbegrepen maaltijden'" />
                </div>
            </div>
        </div>
    </div>
</template>
