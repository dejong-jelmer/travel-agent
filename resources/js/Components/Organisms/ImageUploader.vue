<script setup>
import { usePage } from '@inertiajs/vue3';
import FormFeedback from "@/Components/Atoms/FormFeedback.vue";
const page = usePage();
const settings = page.props.settings;
</script>
<template>
    <div>
        <!-- File Input -->
        <div class="flex items-left justify-left gap-6 flex-warp mb-4">
            <label for="image-upload" class="form-button">
                {{ label }}
            </label>
            <input id="image-upload" type="file" accept="image/*" class="hidden" @change="handleImageUpload" />
        </div>
        <!-- Error message for adding file -->
        <div v-if="errorMessage" class="mt-4 text-red-500 text-sm">
            {{ errorMessage || 'Or no error message'}}
        </div>
        <!-- Error message from form request validation -->
        <template v-if="feedback">
            <FormFeedback :message="feedback" />
        </template>
        <!-- Image Preview -->
        <div v-if="imagePreview" class="relative mb-4">
            <img
                :src="imagePreview"
                alt="Preview"
                class="max-w-full h-auto rounded-lg shadow-md"
                @error="handleImageError"
                @load="handleImageLoad"
            />
            <!-- Remove button -->
                <div role="button"
                    class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-full pl-2 pb-2 p-1 text-xs leading-3 font-bold hover:bg-red-600 z-10"
                    @click="removeImage(index)">
                    ✕
                </div>
        </div>
        <!-- Fallback message if image cannot be loaded -->
        <div v-if="imageLoadError" class="mb-4 p-4 bg-gray-100 rounded-lg text-gray-600">
            <p class="text-sm">{{ imageLoadError }}</p>
        </div>
        <div v-if="imageFile" class="mt-8">
            <p>Bestandsnaam: {{ imageFile.name }}</p>
            <p :class="{'text-red-500': sizeExceedsMax(imageFile.size, (settings.maxFileSize * 1000))}">Bestandsgrote: {{ formatBytes(imageFile.size) }}</p>
            <p v-if="sizeExceedsMax(imageFile.size, (settings.maxFileSize * 1000))" class="text-red-500">Maximale bestandsgrote is {{ formatBytes(settings.maxFileSize, true) }}</p>
            <p>Bestandstype: {{ imageFile.type }}</p>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ImageUploader',
    props: {
        modelValue: {
            type: [Object, String],
            required: false
        },
        label: String,
        feedback: {
            type: [ String, Array ],
            required: false,
        },
    },
    data() {
        return {
            imagePreview: null,
            imageFile: null,
            errorMessage: '',
            imageLoadError: null,
        };
    },
    mounted() {
        if(this.modelValue) {
            // Als modelValue een string is (URL), probeer deze te converteren
            if (typeof this.modelValue === 'string') {
                this.convertImageUrlToFile(this.modelValue);
            } else {
                // Als het al een File object is, gebruik het direct
                this.handleFileChange(this.modelValue);
            }
        }
    },
    methods: {
        async convertImageUrlToFile(imageUrl) {
            try {
                // Reset errors
                this.imageLoadError = null;
                this.errorMessage = '';

                // First try to set up the preview
                this.imagePreview = imageUrl;

                const response = await fetch(imageUrl);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const blob = await response.blob();

                // Check if it's actually an image
                if (!blob.type.startsWith('image/')) {
                    throw new Error('Het bestand is geen geldige afbeelding');
                }

                const fileName = imageUrl.split('/').pop() || 'unknown.jpg';
                const file = new File([blob], fileName, { type: blob.type });

                this.imageFile = file;
                this.$emit('update:modelValue', file);

            } catch (error) {
                console.warn('Kon afbeelding niet laden:', error.message);

                // Put error message but let the rest of the component function
                this.imageLoadError = error.message;
                this.imageFile = null;
                this.imagePreview = null;

                // Emit null so that the parent component knows that there is no file
                this.$emit('update:modelValue', null);
            }
        },
        handleImageUpload(event) {
            const file = event.target.files[0];

            // Reset errors
            this.errorMessage = '';
            this.imageLoadError = null;

            if (!file) {
                this.errorMessage = 'Selecteer een afbeeldingsbestand.';
                return;
            }

            if (file && file.type.startsWith('image/')) {
                this.handleFileChange(file);
            } else {
                this.errorMessage = 'Upload een geldig afbeeldingsbestand.';
                return;
            }
        },
        handleFileChange(file) {
            try {
                this.imageFile = file;

                // Create preview URL and handle possible errors
                if (file instanceof File) {
                    this.imagePreview = URL.createObjectURL(file);
                }

                this.errorMessage = '';
                this.imageLoadError = null;
                this.$emit('update:modelValue', file);

            } catch (error) {
                console.error('Fout bij verwerken van bestand:', error);
                this.errorMessage = 'Fout bij verwerken van het bestand.';
                this.imageFile = null;
                this.imagePreview = null;
                this.$emit('update:modelValue', null);
            }
        },
        removeImage(index) {
            try {
                // Cleanup object URL to prevent memory leaks
                if (this.imagePreview && this.imagePreview.startsWith('blob:')) {
                    URL.revokeObjectURL(this.imagePreview);
                }
                this.imageFile = null;
                this.imagePreview = null;
                this.$emit('update:modelValue', null);

            } catch (error) {
                console.error('Fout bij verwijderen van afbeelding:', error);
            }
        },
        handleImageError(event) {
            console.warn('Afbeelding kon niet worden geladen:', event.target.src);
            this.imageLoadError = 'Afbeelding kon niet worden geladen';
            this.imagePreview = null;
        },
        handleImageLoad() {
            // Reset error if image is loaded successfully
            this.imageLoadError = null;
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
    // Cleanup object URLs to prevent memory leaks
    beforeUnmount() {
        if (this.imagePreview && this.imagePreview.startsWith('blob:')) {
            URL.revokeObjectURL(this.imagePreview);
        }
    }
};
</script>
