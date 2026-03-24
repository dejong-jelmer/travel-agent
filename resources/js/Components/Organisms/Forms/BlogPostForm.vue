<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    form: Object,
    statusOptions: Array,
});

const emit = defineEmits(['submit']);
const { t } = useI18n();
</script>

<template>
    <form @submit.prevent="emit('submit')" class="max-w-wide mx-auto">
        <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
            <!-- Header Section -->
            <div class="laptop:col-span-3 bg-white py-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700">
                            {{ form.id ? t('forms.blog_post.edit_heading') : t('forms.blog_post.new_heading') }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-700/50">
                            {{ t('forms.blog_post.subheading') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content (left 2 cols) -->
            <div class="laptop:col-span-2 space-y-8">
                <!-- Content Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.blog_post.sections.content.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.blog_post.sections.content.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <Input type="text" name="title" :label="t('forms.blog_post.fields.title')"
                            :required="true" v-model="form.title" :feedback="form.errors.title" />

                        <TextArea name="excerpt" :label="t('forms.blog_post.fields.excerpt')"
                            v-model="form.excerpt" :feedback="form.errors.excerpt"
                            :rows="3" />

                        <div>
                            <Label for-field="body" :required="true">{{ t('forms.blog_post.fields.body') }}</Label>
                            <TipTap v-model="form.body" class="mt-1" />
                            <FormFeedback :message="form.errors.body" />
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar (right col) -->
            <div class="space-y-8">
                <!-- Featured Image -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.blog_post.sections.image.title') }}</h2>
                    </div>
                    <div class="p-6">
                        <ImageUploader v-model="form.featured_image" preview-size="large"
                            :feedback="form.errors.featured_image" />
                    </div>
                </section>

                <!-- Meta / SEO -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.blog_post.sections.meta.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.blog_post.sections.meta.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <Input type="text" name="meta_title" :label="t('forms.blog_post.fields.meta_title')"
                            v-model="form.meta_title" :feedback="form.errors.meta_title" />

                        <TextArea name="meta_description" :label="t('forms.blog_post.fields.meta_description')"
                            v-model="form.meta_description" :feedback="form.errors.meta_description"
                            :rows="3" />
                    </div>
                </section>

                <!-- Status -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.blog_post.sections.status.title') }}</h2>
                    </div>
                    <div class="p-6">
                        <Select name="status" :label="t('forms.blog_post.fields.status')"
                            v-model="form.status" :options="statusOptions"
                            option-key="id" option-value="name"
                            :feedback="form.errors.status" />
                    </div>
                </section>
            </div>
        </div>

        <!-- Footer Actions -->
        <FormFooter :form="form"
            :label="form.id ? t('forms.blog_post.submit.update') : t('forms.blog_post.submit.create')"
            @submit="emit('submit')" />
    </form>
</template>
