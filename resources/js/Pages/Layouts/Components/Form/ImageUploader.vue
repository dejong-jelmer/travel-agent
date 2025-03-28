<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const settings = page.props.settings;
</script>
<template>
    <div>
        <!-- File Input -->
        <div class="flex items-left justify-left my-4">
            <label for="image-upload" class="form-button">
                {{ imagePreview ? 'Afbeelding toevoegen' : 'Wijzig afbeelding' }}
            </label>
            <input id="image-upload" type="file" accept="image/*" class="hidden" @change="handleImageUpload" />
        </div>
        <div v-if="errorMessage" class="mt-4 text-red-500 text-sm">
            {{ errorMessage }}
        </div>
        <!-- Image Preview -->
        <div v-if="imagePreview" class="mb-4">
            <img :src="imagePreview" alt="Preview" class="max-w-full h-auto rounded-lg shadow-md" />
        </div>
        <div v-if="imageFile" class="mt-8">
            <p>Bestandsnaam: {{ imageFile.name }}</p>
            <p :class="{'text-custom-red': sizeExceedsMax(imageFile.size, (settings.maxFileSize * 1000))}">Bestandsgrote: {{ formatBytes(imageFile.size) }}</p>
            <p v-if="sizeExceedsMax(imageFile.size, (settings.maxFileSize * 1000))" class="text-custom-red">Maximale bestandsgrote is {{ formatBytes(settings.maxFileSize, true) }}</p>
            <p>Bestandstype: {{ imageFile.type }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ImageUploader',
    props: {
        modelValue: String,
    },
    data() {
        return {
            imagePreview: this.modelValue,
            imageFile: null,
            errorMessage: '',
        };
    },
    mounted() {
        this.convertImageUrlToFile(this.modelValue);
    },
    methods: {
        async convertImageUrlToFile(imageUrl) {
            try {
                const response = await fetch(imageUrl);
                if (!response.ok) {
                    throw new Error('Kon de afbeelding niet downloaden.');
                }

                const blob = await response.blob();
                const fileName = imageUrl.split('/').pop();
                const file = new File([blob], fileName, { type: blob.type });
                this.handleFileChange(file);

            } catch (error) {
                console.error('Fout bij het converteren van de URL naar een File object:', error);
                this.imageFile = null;
            }
        },
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) {
                this.errorMessage = 'Please select an image file.';
                return;
            }
            if (file && file.type.startsWith('image/')) {
                console.log(file);
                this.handleFileChange(file);
            } else {
                this.errorMessage = 'Please upload a valid image file.';
                return;
            }
        },
        handleFileChange(file) {
            this.imageFile = file;
            this.imagePreview = URL.createObjectURL(file);
            this.$emit('image-uploaded', file);
        },
        formatBytes(bytes, isKb = false, decimals = 2) {
            if (bytes === 0) return "0 Bytes";
            bytes = isKb ? (bytes * 1000) : bytes;
            const sizes = ["Bytes", "KB", "MB", "GB", "TB", "PB"];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return parseFloat((bytes / Math.pow(1024, i)).toFixed(decimals)) + " " + sizes[i];
        },
        sizeExceedsMax(bytes, maxBytes) {
            return bytes > maxBytes;
        }
    },
};
</script>
