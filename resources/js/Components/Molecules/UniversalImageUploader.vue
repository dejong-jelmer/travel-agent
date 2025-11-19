<script setup>
import FormFeedback from "@/Components/Atoms/FormFeedback.vue";
</script>

<template>
    <div>
        <!-- Hidden file input -->
        <input
            ref="fileInput"
            type="file"
            :multiple="multiple"
            accept="image/*"
            class="hidden"
            @change="handleFiles"
        />

        <!-- Drop zone -->
        <div
            class="border-2 border-dashed rounded-lg p-8 transition-colors cursor-pointer"
            :class="isDragging ? 'border-accent-link bg-accent-link/5' : 'border-gray-300 hover:border-gray-400'"
            @click="triggerFileInput"
            @dragover.prevent="isDragging = true"
            @dragenter.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
        >
            <div class="flex flex-col items-center justify-center space-y-2">
                <div class="text-accent-link font-medium">
                    {{ isDragging ?
                        (multiple ? 'Laat afbeeldingen los om te uploaden' : 'Laat afbeelding los om te uploaden') :
                        (label || (multiple ? 'Klik hier of sleep afbeeldingen hierheen' : 'Klik hier of sleep een afbeelding hierheen'))
                    }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ multiple ? 'Meerdere afbeeldingen toegestaan' : 'Eén afbeelding toegestaan' }}
                </div>
            </div>

            <!-- Error message for processing image/file -->
            <div v-if="errorMessage" class="mt-4 text-status-error text-sm text-center">
                {{ errorMessage }}
            </div>

            <!-- Error message from form request validation -->
            <template v-if="feedback">
                <FormFeedback :message="feedback" />
            </template>

            <!-- Preview Section -->
            <div v-if="hasImages" class="mt-4">
                <!-- Large preview (single mode only) -->
                <div v-if="!multiple && previewSize === 'large'" class="relative">
                    <img
                        v-if="!imageLoadError && singleImageData.url"
                        :src="singleImageData.url"
                        alt="Preview"
                        class="max-w-full h-auto rounded-lg shadow-md"
                        @error="handleImageError(0)"
                        @load="handleImageLoad(0)"
                    />

                    <!-- Fallback for broken image -->
                    <div
                        v-else-if="imageLoadError"
                        class="p-4 bg-gray-100 rounded-lg text-gray-600"
                    >
                        <p class="text-sm">{{ imageLoadError }}</p>
                    </div>

                    <!-- Remove button -->
                    <button
                        v-if="singleImageData.url"
                        type="button"
                        class="absolute -top-2 -right-2 w-8 h-8 bg-status-error text-white rounded-full flex items-center justify-center text-sm font-bold hover:bg-red-600 hover:scale-110 transition-all shadow-md z-10"
                        @click.stop="removeImage(0)"
                        aria-label="Verwijder afbeelding"
                    >
                        ✕
                    </button>

                    <!-- File info (only in large mode) -->
                    <div v-if="singleImageFile" class="mt-4 space-y-1 text-sm">
                        <p>Bestandsnaam: {{ singleImageFile.name }}</p>
                        <p :class="{'text-status-error': sizeExceedsMax(singleImageFile.size)}">
                            Bestandsgrootte: {{ formatBytes(singleImageFile.size) }}
                        </p>
                        <p v-if="sizeExceedsMax(singleImageFile.size)" class="text-status-error">
                            Maximale bestandsgrootte is {{ formatBytes(images.max_size, true) }}
                        </p>
                        <p>Bestandstype: {{ singleImageFile.type }}</p>
                    </div>
                </div>

                <!-- Thumbnail grid (multiple mode or thumbnail preference) -->
                <div v-else class="flex flex-wrap justify-center gap-2">
                    <div
                        v-for="(imageData, index) in previewImagesData"
                        :key="index"
                        class="relative w-24 h-24"
                    >
                        <!-- Normal image -->
                        <img
                            v-if="!imageData.error && imageData.url"
                            :src="imageData.url"
                            class="w-full h-full object-cover rounded-lg shadow"
                            @error="handleImageError(index)"
                            @load="handleImageLoad(index)"
                        />

                        <!-- Fallback for broken image -->
                        <div
                            v-else-if="imageData.error"
                            class="w-full h-full bg-gray-100 rounded-lg shadow flex flex-col items-center justify-center text-gray-500 text-xs p-1"
                        >
                            <div class="text-lg">📷</div>
                            <div class="text-center leading-3">Kan afbeelding niet laden</div>
                        </div>

                        <!-- Loading state -->
                        <div
                            v-else-if="imageData.loading"
                            class="w-full h-full bg-gray-200 rounded-lg shadow flex items-center justify-center text-gray-500 text-xs animate-pulse"
                        >
                            Laden...
                        </div>

                        <!-- Remove button -->
                        <button
                            type="button"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-status-error text-white rounded-full flex items-center justify-center text-sm font-bold hover:bg-red-600 hover:scale-110 transition-all shadow-md z-10"
                            @click.stop="removeImage(index)"
                            aria-label="Verwijder afbeelding"
                        >
                            ✕
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { usePage } from '@inertiajs/vue3';

export default {
    name: 'UniversalImageUploader',
    props: {
        modelValue: {
            type: [Object, String, Array],
            required: false
        },
        multiple: {
            type: Boolean,
            default: false
        },
        previewSize: {
            type: String,
            default: 'thumbnail',
            validator: (value) => ['large', 'thumbnail'].includes(value)
        },
        label: {
            type: String,
            required: false
        },
        feedback: {
            type: [String, Array],
            required: false
        }
    },
    emits: ['update:modelValue', 'change'],
    data() {
        return {
            uploadedImages: [], // Can contain File objects or string paths
            imageStates: [],
            errorMessage: '',
            imageLoadError: null,
            isDragging: false,
            initialized: false,
            isInitializing: false,
            originalFileCount: 0,
            originalFileNames: [],
            cachedObjectUrls: new Map() // Map<File, string> to prevent memory leaks
        };
    },
    async mounted() {
        if (!this.initialized) {
            this.initialized = true;
            await this.initializeFromModelValue();
        }
    },
    computed: {
        settings() {
            const page = usePage();
            return page.props.images || {};
        },
        hasImages() {
            return this.uploadedImages.length > 0;
        },
        hasChanges() {
            // Check if count changed
            if (this.uploadedImages.length !== this.originalFileCount) {
                return true;
            }
            // Check if files changed (by name/path)
            const currentNames = this.uploadedImages.map(item =>
                typeof item === 'string' ? item : item.name
            );
            return !this.arraysEqual(currentNames, this.originalFileNames);
        },
        singleImageFile() {
            if (this.multiple) return null;
            const item = this.uploadedImages[0];
            // Only return File objects (not string paths) for file info display
            return item instanceof File ? item : null;
        },
        singleImageData() {
            if (this.multiple || !this.uploadedImages[0]) return { url: null, error: false, loading: false };

            const state = this.imageStates[0] || { loading: false, error: false };
            const url = this.getOrCreateObjectURL(this.uploadedImages[0]);

            return { url, error: state.error, loading: state.loading };
        },
        previewImagesData() {
            if (!this.multiple && this.previewSize === 'large') return [];

            return this.uploadedImages.map((file, index) => {
                const state = this.imageStates[index] || { loading: false, error: false };
                const url = this.getOrCreateObjectURL(file);

                return { url, error: state.error, loading: state.loading };
            });
        }
    },
    methods: {
        getOrCreateObjectURL(item) {
            // Handle string paths (existing images)
            if (typeof item === 'string') {
                // If already a full path, return as-is
                if (item.startsWith('/storage/') || item.startsWith('http')) {
                    return item;
                }
                // Otherwise convert filename to full path
                return `/storage/images/${item}`;
            }

            // Handle File objects (new uploads)
            if (!(item instanceof File)) {
                return null;
            }

            // Return cached URL if it exists
            if (this.cachedObjectUrls.has(item)) {
                return this.cachedObjectUrls.get(item);
            }

            // Create new URL and cache it
            try {
                const url = URL.createObjectURL(item);
                this.cachedObjectUrls.set(item, url);
                return url;
            } catch (error) {
                console.warn('Kon preview URL niet maken:', error);
                return null;
            }
        },
        revokeObjectURL(file) {
            if (this.cachedObjectUrls.has(file)) {
                const url = this.cachedObjectUrls.get(file);
                if (url && url.startsWith('blob:')) {
                    URL.revokeObjectURL(url);
                }
                this.cachedObjectUrls.delete(file);
            }
        },
        arraysEqual(a, b) {
            if (a.length !== b.length) return false;
            return a.every((val, index) => val === b[index]);
        },
        async initializeFromModelValue() {
            if (!this.modelValue) return;

            // Set flag to prevent emit during initialization
            this.isInitializing = true;

            if (this.multiple) {
                // Multiple mode: modelValue should be array of strings (paths) or Files
                const images = Array.isArray(this.modelValue) ? this.modelValue : [];
                if (images.length > 0) {
                    // Accept both strings (existing image paths) and Files (new uploads)
                    this.uploadedImages = [...images];
                    // Initialize states for all images
                    this.imageStates = images.map(() => ({ loading: false, error: false }));
                }
            } else {
                // Single mode: modelValue can be string (path) or File
                if (typeof this.modelValue === 'string' || this.modelValue instanceof File) {
                    this.uploadedImages = [this.modelValue];
                    this.imageStates = [{ loading: false, error: false }];
                }
            }

            // Set original state after initialization
            this.setOriginalState();

            // Clear initialization flag
            this.isInitializing = false;

            // Emit the data to parent (no conversion needed)
            this.emitUpdate();

            // Notify parent that initialization is complete
            // this.$emit('initialized');
        },
        setOriginalState() {
            this.originalFileCount = this.uploadedImages.length;
            this.originalFileNames = this.uploadedImages.map(item =>
                typeof item === 'string' ? item : item.name
            );
        },
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        handleDrop(event) {
            this.isDragging = false;

            try {
                this.errorMessage = '';
                const files = Array.from(event.dataTransfer.files);

                // Validate files
                const validFiles = files.filter(file => {
                    if (!file.type.startsWith('image/')) {
                        console.warn(`Bestand ${file.name} is geen afbeelding`);
                        return false;
                    }
                    return true;
                });

                if (validFiles.length !== files.length) {
                    this.errorMessage = 'Sommige bestanden zijn geen geldige afbeeldingen en zijn overgeslagen.';
                }

                if (validFiles.length > 0) {
                    if (this.multiple) {
                        // Multiple mode: add all files
                        const startIndex = this.uploadedImages.length;
                        validFiles.forEach((_, index) => {
                            this.setImageState(startIndex + index, { loading: false, error: false });
                        });
                        this.uploadedImages.push(...validFiles);
                    } else {
                        // Single mode: only first file
                        this.setImageState(0, { loading: false, error: false });
                        this.uploadedImages = [validFiles[0]];
                        this.imageStates = [{ loading: false, error: false }];
                    }
                    this.emitUpdate();
                }
            } catch (error) {
                console.error('Fout bij verwerken van bestanden:', error);
                this.errorMessage = 'Er is een fout opgetreden bij het uploaden van de bestanden.';
            }
        },
        handleFiles(event) {
            try {
                this.errorMessage = '';
                const files = Array.from(event.target.files);

                // Validate files
                const validFiles = files.filter(file => {
                    if (!file.type.startsWith('image/')) {
                        console.warn(`Bestand ${file.name} is geen afbeelding`);
                        return false;
                    }
                    return true;
                });

                if (validFiles.length !== files.length) {
                    this.errorMessage = 'Sommige bestanden zijn geen geldige afbeeldingen en zijn overgeslagen.';
                }

                if (validFiles.length > 0) {
                    if (this.multiple) {
                        // Multiple mode: add all files
                        const startIndex = this.uploadedImages.length;
                        validFiles.forEach((_, index) => {
                            this.setImageState(startIndex + index, { loading: false, error: false });
                        });
                        this.uploadedImages.push(...validFiles);
                    } else {
                        // Single mode: only first file
                        this.handleFileChange(validFiles[0]);
                    }
                    this.emitUpdate();
                }

                // Reset input
                event.target.value = '';
            } catch (error) {
                console.error('Fout bij verwerken van bestanden:', error);
                this.errorMessage = 'Er is een fout opgetreden bij het uploaden van de bestanden.';
            }
        },
        handleFileChange(file) {
            try {
                // Cleanup old file's object URL if it exists (only for File objects)
                if (this.uploadedImages[0] instanceof File) {
                    this.revokeObjectURL(this.uploadedImages[0]);
                }

                this.uploadedImages = [file];
                this.imageStates = [{ loading: false, error: false }];
                this.errorMessage = '';
                this.imageLoadError = null;
            } catch (error) {
                console.error('Fout bij verwerken van bestand:', error);
                this.errorMessage = 'Fout bij verwerken van het bestand.';
                this.uploadedImages = [];
                this.imageStates = [];
            }
        },
        removeImage(index) {
            try {
                // Cleanup object URL to prevent memory leaks (only for File objects)
                const item = this.uploadedImages[index];
                if (item instanceof File) {
                    this.revokeObjectURL(item);
                }

                this.uploadedImages.splice(index, 1);
                this.imageStates.splice(index, 1);
                this.emitUpdate();

                // Clear error messages if there are no more images
                if (this.uploadedImages.length === 0) {
                    this.errorMessage = '';
                    this.imageLoadError = null;
                }

            } catch (error) {
                console.error('Fout bij verwijderen van afbeelding:', error);
            }
        },
        handleImageError(index) {
            console.warn(`Afbeelding ${index} kon niet worden geladen`);
            if (!this.multiple && this.previewSize === 'large') {
                this.imageLoadError = 'Afbeelding kon niet worden geladen';
            } else {
                this.setImageState(index, { loading: false, error: true });
            }
        },
        handleImageLoad(index) {
            if (!this.multiple && this.previewSize === 'large') {
                this.imageLoadError = null;
            } else {
                this.setImageState(index, { loading: false, error: false });
            }
        },
        setImageState(index, state) {
            while (this.imageStates.length <= index) {
                this.imageStates.push({ loading: false, error: false });
            }
            this.imageStates[index] = { ...this.imageStates[index], ...state };
        },
        emitUpdate() {
            try {
                // Don't emit during initialization
                if (this.isInitializing) {
                    return;
                }

                let value;
                if (this.multiple) {
                    // Multiple mode: emit array
                    const validFiles = this.uploadedImages.filter((file, index) => {
                        const state = this.imageStates[index];
                        return !state || !state.error;
                    });
                    value = [...validFiles];
                } else {
                    // Single mode: emit single file or null
                    const file = this.uploadedImages[0] || null;
                    const state = this.imageStates[0];
                    value = (!state || !state.error) ? file : null;
                }

                // Emit v-model update
                this.$emit("update:modelValue", value);

                // Emit change event with hasChanges flag
                this.$emit("change", {
                    files: value,
                    hasChanges: this.hasChanges
                });
            } catch (error) {
                console.error('Fout bij emit update:', error);
            }
        },
        formatBytes(bytes, isKb = false, decimals = 2) {
            if (bytes === 0) return "0 Bytes";
            bytes = isKb ? (bytes * 1000) : bytes;
            const sizes = ["Bytes", "KB", "MB", "GB", "TB", "PB"];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return parseFloat((bytes / Math.pow(1024, i)).toFixed(decimals)) + " " + sizes[i];
        },
        sizeExceedsMax(bytes) {
            const maxBytes = (this.images?.max_size || 5000) * 1000;
            return bytes > maxBytes;
        }
    },
    beforeUnmount() {
        try {
            // Cleanup all cached object URLs
            this.cachedObjectUrls.forEach((url, file) => {
                if (url && url.startsWith('blob:')) {
                    URL.revokeObjectURL(url);
                }
            });
            this.cachedObjectUrls.clear();
        } catch (error) {
            console.error('Fout bij cleanup:', error);
        }
    }
};
</script>
