<script setup>
import { usePage } from '@inertiajs/vue3';
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
        <div v-if="errorMessage" class="mt-4 text-red-500 text-sm">
            {{ errorMessage }}
        </div>
        <!-- Image Preview -->
        <div v-if="imagePreview" class="mb-4">
            <img 
                :src="imagePreview" 
                alt="Preview" 
                class="max-w-full h-auto rounded-lg shadow-md"
                @error="handleImageError"
                @load="handleImageLoad"
            />
        </div>
        <!-- Fallback bericht als afbeelding niet kan worden geladen -->
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
                
                // Eerst proberen de preview in te stellen
                this.imagePreview = imageUrl;
                
                const response = await fetch(imageUrl);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const blob = await response.blob();
                
                // Controleer of het daadwerkelijk een afbeelding is
                if (!blob.type.startsWith('image/')) {
                    throw new Error('Het bestand is geen geldige afbeelding');
                }
                
                const fileName = imageUrl.split('/').pop() || 'unknown.jpg';
                const file = new File([blob], fileName, { type: blob.type });
                
                this.imageFile = file;
                this.$emit('update:modelValue', file);
                
            } catch (error) {
                console.warn('Kon afbeelding niet laden:', error.message);
                
                // Zet error bericht maar laat de rest van de component functioneren
                this.imageLoadError = error.message;
                this.imageFile = null;
                this.imagePreview = null;
                
                // Emit null zodat het parent component weet dat er geen file is
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
                
                // Maak preview URL en handel mogelijke errors af
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
        handleImageError(event) {
            console.warn('Afbeelding kon niet worden geladen:', event.target.src);
            this.imageLoadError = 'Afbeelding kon niet worden geladen';
            this.imagePreview = null;
        },
        handleImageLoad() {
            // Reset error als afbeelding succesvol is geladen
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
    // Cleanup object URLs om memory leaks te voorkomen
    beforeUnmount() {
        if (this.imagePreview && this.imagePreview.startsWith('blob:')) {
            URL.revokeObjectURL(this.imagePreview);
        }
    }
};
</script>