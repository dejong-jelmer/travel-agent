<script setup>
const emit = defineEmits(['submit']);
const props = defineProps({
    countries: Object,
    form: Object,
});
</script>
<template>
    <form @submit.prevent="submit">
        <div class="w-full flex justify-end">
            <IconLink icon="Save" @click="emit('submit')" v-tippy="'Sla reisproduct op'" class="mx-0"
                :class="{ 'animate-bounce': form.isDirty }" />
        </div>

        <div class="flex flex-col tablet:flex-row tablet:space-x-6 space-y-6 tablet:space-y-0">
            <div class="w-full tablet:w-1/2 grid gap-4">
                <ImageUploader
                    :label="form.featuredImage ? 'Afbeelding wijzigen' : 'Selecteer een afbeelding'"
                    v-model="form.featuredImage"
                    :feedback="form.errors.featuredImage"
                />
                <MultiImageUploader v-model="form.images" :feedback="form.errors.images" />
            </div>
            <div class="w-full tablet:w-1/2 grid gap-6 auto-rows-max">
                <div class="grid grid-cols-2 gap-4">
                    <Checkbox v-model="form.active" name="active" label="Actief" />
                    <Checkbox v-model="form.featured" name="featured" label="Uitgelicht" />
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div class="grid gap-4 auto-rows-max">
                        <Input type="text" name="name" label="Naam" :required="true" v-model="form.name"
                            :feedback="form.errors.name" />
                        <Input type="text" name="slug" label="Slug" :required="true" v-model="form.slug"
                            :feedback="form.errors.slug" />
                        <TextArea name="description" label="Omschrijving" :required="true" v-model="form.description" />
                    </div>
                    <div class="grid gap-4 auto-rows-max">
                        <Input type="text" name="price" label="Prijs (€)" :required="true" v-model="form.price"
                            :feedback="form.errors.price" />
                        <Input type="number" name="duration" label="Duur (dagen)" :required="true"
                            v-model="form.duration" :feedback="form.errors.duration" />
                        <Select name="country" label="Gekoppelde landen" v-model="form.countries" :multiple="true"
                            :required="true" :options="countries" :feedback="form.errors.countries"
                            :placeholder="'Koppel één of meerdere landen'" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
